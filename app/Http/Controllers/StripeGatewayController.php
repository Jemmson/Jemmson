<?php

namespace App\Http\Controllers;

use App\JobTask;
use App\Job;
use App\User;
use App\JobTaskStatus;
use App\StripeExpress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\TransferGroup;

class StripeGatewayController extends Controller
{
    //

    public function getStripeOauthUrl($path)
    {

//        https://connect.stripe.com/express/oauth/authorize?
//        redirect_uri=https://connect.stripe.com/connect/default/oauth/test
//        &client_id=ca_32D88BD1qLklliziD7gYQvctJIhWBSQ7&state={STATE_VALUE}

        $user = Auth::user();
        $contractor = $user->contractor()->get()->first();
        $clientId = env('STRIPE_CLIENT_ID');
        $responseType = 'code';
        $scope = 'read_write';
        $stripeLanding = 'register';
        $stripeOauthEndpoint = 'https://connect.stripe.com/express/oauth/authorize';
        $state = "$path:" . uniqid() . "-" . uniqid() . "-" . uniqid() . "-" . uniqid();
//        $capabilities = 'platform_payments';

        return "$stripeOauthEndpoint?response_type=$responseType" .
            "&stripe_landing=$stripeLanding" .
            "&redirect_uri=" . env('STRIPE_REDIRECT_URI') .
            "&client_id=$clientId" .
            "&scope=$scope" .
//            "&suggested_capabilities[]=$capabilities" .
            "&stripe_user[email]=$user->email" .
            "&stripe_user[country]=$user->billing_country" .
            "&stripe_user[phone_number]=$user->phone" .
            "&stripe_user[business_phone_number]=$user->phone" .
            "&stripe_user[business_name]=$contractor->company_name" .
            "&stripe_user[first_name]=$user->first_name" .
            "&stripe_user[last_name]=$user->last_name" .
            "&stripe_user[street_address]=$user->billing_address" .
            "&stripe_user[city]=$user->billing_city" .
            "&stripe_user[state]=$user->billing_state" .
            "&stripe_user[zip]=$user->billing_zip" .
            "&stripe_user[country]=US" .
            "&stripe_user[currency]=usd" .
            "&state=$state";

    }

    public function jobTasksExist($excluded, $jobId)
    {

        $jobTaskArray = [];

        $jobTasks = Job::where('id', '=', $jobId)->get()->first()->jobTasks()->get();

        foreach ($jobTasks as $jobTask) {
            $pay = true;
            foreach ($excluded as $key => $item) {
                if ($jobTask->id == $key) {
                    $pay = false;
                }
            }
            if ($pay) {
                array_push($jobTaskArray, $jobTask);
            }

        }

        return [
            "exists" => count($jobTaskArray) > 0,
            "jobTasks" => $jobTaskArray
        ];
    }

    public function hasNotBeenPaid($jobTask)
    {
        $jts = JobTaskStatus::where("job_task_id", "=", $jobTask->id)->get();
        return JobTaskStatus::getLastStatus($jts) !== 'paid';
    }

    public function getTotalTaskPrices($jobTasks)
    {
        $amount = 0;

        foreach ($jobTasks as $jobTask) {
            $amount = $amount + $jobTask->cust_final_price;
        }

        return $amount;
    }

    public function getClientSecret(Request $request)
    {
        $jobTasks = $this->jobTasksExist($request->excluded, $request->jobId);

        if ($jobTasks["exists"]) {
            $totalAmount = JobTask::totalAmountForAllPayableTasks($jobTasks["jobTasks"]);
        }

        $generalId = Job::where('id', '=', $request->jobId)->get()->first()->contractor_id;

        $transferGroupAttributes = [
          'general_id' => $generalId,
          'jemmson_amount' => $totalAmount,
          'job_id' => $request->jobId,
          'customer_id' => Auth::user()->getAuthIdentifier(),
          'transfer_group_guid' => uniqid() . "-" . uniqid() . "-" . uniqid() . "-" . uniqid(),
        ];

        $transferGroup = new TransferGroup();
        $transferGroup->createFromClientSecret($transferGroupAttributes);

        // Set your secret key: remember to switch to your live secret key in production
        // See your keys here: https://dashboard.stripe.com/account/apikeys
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        \Stripe\Stripe::$apiVersion = '2019-08-14';

        $intent = \Stripe\PaymentIntent::create([
            'amount' => $totalAmount,
            'currency' => 'usd',
            'metadata' => [
                'transferGroupId' => $transferGroup->id
            ]
        ]);

        foreach ($jobTasks['jobTasks'] as $jobTask) {
            $jobTask->payment_intent_id = $intent->id;
            $jobTask->transfer_group_id = $transferGroup->id;
            try {
                $jobTask->save();
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ], 200);
            }        }

        Log::debug(json_encode($intent));

        return $intent['client_secret'];
    }

    public function charge(Request $request)
    {

        $jobTasks = $this->jobTasksExist($request->excluded, $request->jobId);

        if ($jobTasks["exists"]) {

            $totalAmount = JobTask::totalAmountForAllPayableTasks($jobTasks["jobTasks"]);

//            TODO :: Should throw an exception for amount not being greater than zero
            if ($totalAmount > 0) {

                $job = Job::find($request->jobId);
                $customer = User::where('id', '=', $job->customer_id)->get()->first();
                $stripeExpress = new StripeExpress();

//                TOTALS
                $stripeCustomer = $stripeExpress->createStripeCustomer($customer, $job->contractor_id);
                $charge = $this->payJemmson($request->token, $totalAmount);
                $contractorAmount = $this->calculateContractorAmount($totalAmount, $jobTasks["jobTasks"]);
                $this->transferAmountToGeneral($contractorAmount['contractorFee'], $job->contractor_id, $charge->id);
                $this->transferSubAmounts($jobTasks, $charge->id);

            } else {
                return response()->json([
                    "error" => "Cannot Charge Tasks Must Total Greater Than 0"
                ], 200);
            }

        }
    }

    public function transferSubAmounts($jobTasks, $generalId, $chargeId)
    {

        foreach ($jobTasks as $jobTask) {
            if ($this->isASub($generalId, $jobTask->contractor_id)) {
                $sub = User::where('id', '=', $jobTask->contractor_id)->get()->first();
                $this->transferAmountToSub(
                    $jobTask->sub_final_price,
                    $sub->id,
                    $chargeId
                );
            }
        }
    }

    public function isASub($generalId, $jobTaskContractorId)
    {
        return $generalId !== $jobTaskContractorId;
    }

    public function transferAmountToSub($amount, $subId, $chargeId)
    {
        $accountId = StripeExpress::where('contractor_id', '=', $subId)
            ->get()->first()->stripe_user_id;
        return $this->transfer($amount, $accountId, $chargeId);
    }

    public function transfer($amount, $accountId, $chargeId)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        \Stripe\Stripe::$apiVersion = '2019-08-14';

        return \Stripe\Transfer::create([
            'amount' => $amount,
            'currency' => 'usd',
            'destination' => $accountId,
            "source_transaction" => $chargeId
        ]);
    }

    public function transferAmountToGeneral($amount, $contractorId, $chargeId)
    {
        $accountId = StripeExpress::where('contractor_id', '=', $contractorId)
            ->get()->first()->stripe_user_id;
        return $this->transfer($amount, $accountId, $chargeId);
    }

    public function calculateContractorAmount($totalAmount, $jobTasks)
    {
        $stripe = $totalAmount * env('STRIPE_PERCENT_FEE') + 30;
        $subFee = $this->getTotalSubAmount($jobTasks);
        $jemmsonFee = $this->getJemmsonTotalAmount($jobTasks);
        $contractorFee = $this->getContractorFee($totalAmount, $stripe, $subFee, $jemmsonFee);

        return [
            "stripe" => $stripe,
            "subFee" => $subFee,
            "jemmsonFee" => $jemmsonFee,
            "contractorFee" => $contractorFee
        ];
    }

    public function getContractorFee($totalAmount, $stripe, $subFee, $jemmsonFee)
    {
        return $totalAmount - $stripe - $subFee - $jemmsonFee;
    }

    public function getJemmsonTotalAmount($jobTasks)
    {
        return env('JEMMSON_FLAT_RATE');
    }

    public function getTotalSubAmount($jobTasks)
    {
        $amount = 0;

        foreach ($jobTasks as $jobTask) {
            $amount = $amount + $jobTask->sub_final_price;
        }

        return $amount;
    }

    public function payJemmson($token, $amount, $description = '')
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        \Stripe\Stripe::$apiVersion = '2019-08-14';

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'usd',
            'payment_method_types' => ['card'],
            'transfer_group' => '{ORDER10}',
        ]);

    }

    /**
     * Separate charges and transfers are supported only if both your platform and the
     * connected account are in the same region: for example, both in Europe or both in the U.S.
     */
    public function createCharge($token)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        \Stripe\Stripe::$apiVersion = '2019-08-14';

        return \Stripe\Charge::create([
            'amount' => 999,
            'currency' => 'usd',
            'source' => $token,
            'description' => 'Example charge',
        ]);
    }

    public function transferAmount($amount, $stripeKey, $transfer_group_guid)
    {
        return \Stripe\Transfer::create([
            'amount' => $amount,
            'currency' => 'usd',
            'destination' => $stripeKey,
            "source_transaction" => "{CHARGE_ID}",
            'transfer_group' => $transfer_group_guid,
        ]);
    }

    /**
     * Calculate the amounts for the different parties
     * Stripe -> 2.9% + 30 cents
     * Jemmson -> set in the env file -> JEMMSON_FLAT_RATE
     *
     * @param JobTask $jobTask
     * @return
     * @throws
     */
    public function calculateAmounts($jobTask)
    {

    }

    /**
     * Respond to the charge.succeeded event webhook
     *
     * @param
     * @return
     * @throws
     */
    public function getChargeSucceededEvent()
    {

    }

}
