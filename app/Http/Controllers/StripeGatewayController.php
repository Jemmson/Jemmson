<?php

namespace App\Http\Controllers;

use App\JobTask;
use App\Job;
use App\JobTaskStatus;
use App\StripeExpress;
use Illuminate\Http\Request;
use oasis\names\specification\ubl\schema\xsd\CommonBasicComponents_2\Amount;
use Symfony\Component\Routing\Annotation\Route;
use Illuminate\Support\Facades\Auth;
use function MongoDB\BSON\toJSON;

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

        return "$stripeOauthEndpoint?response_type=$responseType" .
            "&stripe_landing=$stripeLanding" .
            "&redirect_uri=" . env('STRIPE_REDIRECT_URI') .
            "&client_id=$clientId" .
            "&scope=$scope" .
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

    public function jobTasksExist($excluded)
    {
        $jobTasks = [];

        foreach ($excluded as $key => $item) {
            if (!$item) {
                $jobTask = JobTask::where("id", "=", $key)->get()->first();
                if ($this->hasNotBeenPaid($jobTask)) {
                    array_push($jobTasks, $jobTask);
                }
            }
        }

        return [
            "exists" => count($jobTasks) > 0,
            "jobTasks" => $jobTasks
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

    public function charge(Request $request)
    {

        $jobTasks = $this->jobTasksExist($request->excluded);

        if ($jobTasks["exists"]) {

            $totalAmount = JobTask::totalAmountForAllPayableTasks($jobTasks["jobTasks"]);

//            TODO :: Should throw an exception for amount not being greater than zero
            if ($totalAmount > 0) {

                $job = Job::find($request->jobId);
                $customer = User::where('customer_id', '=', $job->customer_id)->get()->first();
                $stripeExpress = new StripeExpress();

//                TOTALS
                $stripeCustomer = $stripeExpress->createStripeCustomer($customer, $request->token);
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

        return \Stripe\Charge::create([
            'amount' => $amount,
            'currency' => 'usd',
            'source' => $token,
            'description' => $description
        ]);

    }

    /**
     * Separate charges and transfers are supported only if both your platform and the
     * connected account are in the same region: for example, both in Europe or both in the U.S.
     */
    public function createCharge($token)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

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
