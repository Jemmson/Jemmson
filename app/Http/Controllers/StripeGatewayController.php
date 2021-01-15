<?php

namespace App\Http\Controllers;

use App\ContractorCustomer;
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

        \Stripe\Stripe::setApiKey('sk_test_519YZpRIX4qnobbHhhbsdPCMtc4OhFEVP6cCshbIlTSuwYnRBbpf2a230zaqkCuKbkKZYUL7zIoyHVI3wCf7B3p2y00GdnzPrEV');

        \Stripe\Stripe::$apiVersion = env('STRIPE_API_VERSION');

        $user = Auth::user();
        $contractor = $user->contractor()->get()->first();
        $clientId = env('STRIPE_CLIENT_ID');
        $responseType = 'code';
        $scope = 'read_write';
        $stripeLanding = 'register';
        $stripeOauthEndpoint = 'https://connect.stripe.com/express/oauth/authorize';
//        $stripeOauthEndpoint = 'https://connect.stripe.com/oauth/authorize';
        $state = "$path:" . uniqid() . "-" . uniqid() . "-" . uniqid() . "-" . uniqid();
//        $capabilities = 'platform_payments';

        return "$stripeOauthEndpoint?response_type=$responseType" .
            "&stripe_landing=$stripeLanding" .
            "&redirect_uri=" . env('STRIPE_REDIRECT_URI') .
            "&client_id=$clientId" .
            "&scope=$scope" .
//            "&suggested_capabilities[]=$capabilities" .
            "&stripe_user[business_type]=individual" .
            "&stripe_user[email]=$user->email" .
            "&stripe_user[country]=$user->billing_country" .
            "&stripe_user[phone_number]=$user->phone" .
            "&stripe_user[business_phone_number]=$user->phone" .
            "&stripe_user[business_name]=$contractor->company_name" .
            "&stripe_user[first_name]=$user->first_name" .
            "&stripe_user[last_name]=$user->last_name" .
            "&stripe_user[country]=US" .
            "&state=$state";
    }


//"&api_version=2019-08-14" .
//"&stripe_user[street_address]=$user->billing_address" .
//"&stripe_user[city]=$user->billing_city" .
//"&stripe_user[state]=$user->billing_state" .
//"&stripe_user[zip]=$user->billing_zip" .
//"&stripe_user[currency]=usd" .


    public function jobTaskHasNotBeenExcluded($excluded, $jobTask)
    {
        foreach ($excluded as $key => $item) {
            if ($jobTask->id == $key) {
                return false;
            }
        }
        return true;
    }

    public function jobTaskHasBeenApprovedByContractor($latestStatus)
    {
        return $latestStatus == 'approved_subs_work';
    }

    public function JobTaskHasBeenCompletedByGeneral($latestStatus)
    {
        return $latestStatus == 'general_finished_work';
    }

    public function JobTaskHasNotBeenPaid($latestStatus)
    {
        return $latestStatus != 'paid';
    }

    public function jobTasksExist($excluded, $jobId)
    {

        $jobTaskArray = [];

        $jobTasks = Job::where('id', '=', $jobId)->get()->first()->jobTasks()->get();

        foreach ($jobTasks as $jobTask) {
            $jtNotExcluded = $this->jobTaskHasNotBeenExcluded($excluded, $jobTask);

            $jt = JobTaskStatus::where('job_task_id', '=', $jobTask->id)->get();
            $latestStatus = JobTaskStatus::getLastStatus($jt);

            $jtApproved = $this->jobTaskHasBeenApprovedByContractor($latestStatus);
            $jtCompleted = $this->JobTaskHasBeenCompletedByGeneral($latestStatus);
            $jtNotPaid = $this->JobTaskHasNotBeenPaid($latestStatus);

            if ($jtNotExcluded
                && ($jtApproved || $jtCompleted)
                && $jtNotPaid) {
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
            $totalAmount = JobTask::totalAmountForAllPayableTasks($jobTasks["jobTasks"]) + env('JEMMSON_FLAT_RATE');
            $generalId = Job::where('id', '=', $request->jobId)->get()->first()->contractor_id;
            $general = User::find($generalId);

            $transferGroup = $this->createTransferGroup($generalId, $totalAmount, $request->jobId);

            // Set your secret key: remember to switch to your live secret key in production
            // See your keys here: https://dashboard.stripe.com/account/apikeys
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            \Stripe\Stripe::$apiVersion = env('STRIPE_API_VERSION');

            $customerId = Job::where('id', '=', ($jobTasks["jobTasks"][0]->job_id))->get()->first()->customer_id;

            $customer = $this->getCustomer($customerId);
            $customerStripe = $this->getStripeCustomer($customer, $generalId);

// TODO:: handle payment method. payment method should only be attached when the
//            the customer has selected a payment method option on the client side
//            this will indicate which payment method to use for payment
//            if (\is_null($stripeId)) {
//                return null;
//            }
//            return $this->getPaymentMethod();
//            $paymentMethod = $this->getPaymentMethod($request->paymentMethod);

            $generalCompanyName = $this->getGeneralCompanyName($jobTasks["jobTasks"][0]);

            $intent = \Stripe\PaymentIntent::create([
                'amount' => $totalAmount,
                'currency' => 'usd',
                'customer' => $customerStripe->id,
                'payment_method' => null,
                'receipt_email' => $customer->email,
                'on_behalf_of' => $general->customer_stripe_id,
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
                }
            }

            Log::debug(json_encode($intent));

            return $intent['client_secret'];
        } else {
            return response()->json([
                'error' => 'There were no payable tasks'
            ], 200);
        }
    }

    public function getGeneralCompanyName($jobTask)
    {
        return Job::find($jobTask->job_id)
            ->get()->first()->contractor()
            ->get()->first()->contractor()
            ->get()->first()['company_name'];
    }

    public function getPaymentMethod($stripeId)
    {

    }

    public function hasPaymentMethod()
    {

    }

    public function getCustomer($customerId)
    {
        return User::find($customerId);
    }

    public function getStripeCustomer($customer, $generalId)
    {

        $customerStripe = $this->customerExists($customer->customer_stripe_id);

        if (\is_null($customerStripe)) {
            $customerStripe = $this->createCustomer($customer, $generalId);
        }

        return $customerStripe;
    }

    public function customerExists($customerStripeId)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        \Stripe\Stripe::$apiVersion = env('STRIPE_API_VERSION');

        if (\is_null($customerStripeId) || $customerStripeId == '') {
            return null;
        }

        return \Stripe\Customer::retrieve($customerStripeId);
    }

    public function createCustomer($customer, $generalId)
    {
        $customerStripe = $this->createStripeCustomer($customer);
        $this->updateCustomerStripeId($customer, $customerStripe->id);
        $this->updateGeneralCustomerStripeId($customer->id, $generalId, $customerStripe->id);
        return $customerStripe;
    }

    public function updateGeneralCustomerStripeId($customerId, $generalId, $customerStripeId)
    {
        $cc = ContractorCustomer::where('customer_user_id', '=', $customerId)
            ->where('contractor_user_id', '=', $generalId)->get()->first();
        $cc->customer_stripe_id = $customerStripeId;

        try {
            $cc->save();
        } catch (\Exception $e) {
            Log::error('Could not save to contractor customer table: ' . $e->getMessage());
            return response()->json([
                "message" => "Could not save to contractor customer table",
                "errors" => ["error" => [$e->getMessage()]]], 404);
        }


    }

    public function updateCustomerStripeId($customer, $stripeId)
    {
        $customer->customer_stripe_id = $stripeId;
        $customer->save();
    }

    public function createStripeCustomer($customer)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        \Stripe\Stripe::$apiVersion = env('STRIPE_API_VERSION');

        return \Stripe\Customer::create([
            "address" => [
                'line1' => $customer->billing_address,
                'line2' => $customer->billing_address_line_2,
                'city' => $customer->billing_city,
                'state' => $customer->billing_state,
                'country' => 'US',
                'postal_code' => $customer->billing_zip,
            ],
            'name' => $customer->name,
            'phone' => $customer->phone,
            'email' => $customer->email
        ]);
    }

    public function createTransferGroup($generalId, $totalAmount, $jobId)
    {
        $transferGroupAttributes = [
            'general_id' => $generalId,
            'jemmson_amount' => $totalAmount,
            'job_id' => $jobId,
            'customer_id' => Auth::user()->getAuthIdentifier(),
            'transfer_group_guid' => uniqid() . "-" . uniqid() . "-" . uniqid() . "-" . uniqid(),
        ];

        $transferGroup = new TransferGroup();
        $transferGroup->createFromClientSecret($transferGroupAttributes);

        return $transferGroup;
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
        \Stripe\Stripe::$apiVersion = env('STRIPE_API_VERSION');

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
        \Stripe\Stripe::$apiVersion = env('STRIPE_API_VERSION');

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
        \Stripe\Stripe::$apiVersion = env('STRIPE_API_VERSION');

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
