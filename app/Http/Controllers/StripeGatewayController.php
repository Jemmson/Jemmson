<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Routing\Annotation\Route;
use Illuminate\Support\Facades\Auth;

class StripeGatewayController extends Controller
{
    //

    public function getStripeOauthUrl($path)
    {
        $user = Auth::user();
        $contractor = $user->contractor()->get()->first();
        $clientId = env('STRIPE_CLIENT_ID');
        $responseType = 'code';
        $scope = 'read_write';
        $stripeLanding = 'register';
        $stripeOauthEndpoint = 'https://connect.stripe.com/oauth/authorize';
        $state = "$path:" . uniqid() . "-" . uniqid() . "-" . uniqid() . "-" . uniqid();


        return "$stripeOauthEndpoint?response_type=$responseType".
            "&stripe_landing=$stripeLanding".
            "&client_id=$clientId".
            "&scope=$scope".
            "&stripe_user[email]=$user->email".
            "&stripe_user[country]=$user->billing_country".
            "&stripe_user[phone_number]=$user->phone".
            "&stripe_user[business_phone_number]=$user->phone".
            "&stripe_user[business_name]=$contractor->company_name".
            "&stripe_user[first_name]=$user->first_name".
            "&stripe_user[last_name]=$user->last_name".
            "&stripe_user[street_address]=$user->billing_address".
            "&stripe_user[city]=$user->billing_city".
            "&stripe_user[state]=$user->billing_state".
            "&stripe_user[zip]=$user->billing_zip".
            "&stripe_user[country]=US".
            "&stripe_user[currency]=usd".
            "&state=$state";
    }

    public function charge(Request $request)
    {


//        if (count($request->excluded) > 0) {
//            $jobTask = JobTask::find($request->excluded[0]);
//            $job = Job::find($jobTask->job_id);
//            $jobTasks = $job->jobTasks()->get();
//        } else {
//
//        }
//
//
//
//
//        $jobTasks = JobTask::whereNotIn($request->token);

        $charge = $this->createCharge($request);

//        if (!$this->isAStripeCustomerForContractor()) {
//            $this->createCustomerForContractor();
//        }
//        // Set your secret key: remember to change this to your live secret key in production
//        // See your keys here: https://dashboard.stripe.com/account/apikeys
//        \Stripe\Stripe::setApiKey(env('STRIPE_KEY'));
//
//        foreach ($jobTasks as $jobTask) {
//            $amounts = $this->calculateAmounts($jobTask);
//
//            $transfer = new TransferGroup(
//                $general->id,
//                $sub->id,
//                $amounts['general'],
//                $amounts['sub'],
//                $amounts['jemmson'],
//                $amounts['stripe'],
//                $job->id,
//                $job_task->id
//            );
//
//            $this->transferAmount($jemmson->amount, $jemmson->key, $transfer->transfer_group_guid);
//
//            if ($jobTask->hasSub()) {
//                $this->transferAmount($sub->amount, $sub->key, $transfer->transfer_group_guid);
//            }
//
//            $this->transferAmount($general->amount, $general->key, $transfer->transfer_group_guid);
//
//            $jobTask->paid();
//
//            if ($job->allJobTasksHaveBeenPaid()) {
//                $job->paid();
//            }
//        }
    }

    public function allPartiesInSameRegion($customer, $general, $sub)
    {
        return true;
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
