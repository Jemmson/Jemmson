<?php

namespace App;

use App\Notifications\CustomerPaidForTask;
use App\Notifications\CustomerUnableToSendPaymentWithStripe;
use Illuminate\Database\Eloquent\Model;

class StripeExpress extends Model
{

    protected $table = 'stripe_expresses';
    protected $guarded = [];

    public static function makePayment($jobTask)
    {
        // get modals and relevant variables

        $task = $jobTask->task()->first();
        $sub_contractor_id = $jobTask->contractor_id;
        $general_contractor_id = $task->contractor_id;

        // if not payable howd you get here?
        if (!$jobTask->updatable(__('bid_task.customer_sent_payment'))) {
            return response()->json(['message' => "Can't pay for this task yet."], 422);
        }

        // amounts
        $subAmount = (int)$jobTask->sub_final_price;
        $generalAmount = (int)$jobTask->cust_final_price - $subAmount;

        // get stripe express modals
        $sub_contractor = User::find($sub_contractor_id);
        $general_contractor = User::find($general_contractor_id);
        $sub_stripeExpress = $sub_contractor->contractor()->first()->stripeExpress()->first();
        $general_stripeExpress = $general_contractor->contractor()->first()->stripeExpress()->first();

        // do contractors have an express account with us?
        if ($sub_stripeExpress === null && $sub_stripeExpress !== $general_stripeExpress) {
            $sub_contractor->notify(new CustomerUnableToSendPaymentWithStripe());
        }

        if ($general_stripeExpress === null) {
            $general_contractor->notify(new CustomerUnableToSendPaymentWithStripe());
        }

        if ($sub_stripeExpress === null || $general_stripeExpress === null) {
            return response()->json(['message' => 'Not all contractors have Stripe'], 422);
        }

        // get stripe user ids
        $sub_stripeUserId = $sub_stripeExpress->stripe_user_id;
        $general_stripeUserId = $general_stripeExpress->stripe_user_id;

        // charge results
        $s_charge = [];
        $g_charge = [];

        // pay sub the sub amount on the job task
        if ($subAmount > 0) {
            $s_charge = \Stripe\Charge::create(array(
                "amount" => $subAmount * 100,
                "currency" => "usd",
                "customer" => Auth::user()->stripe_id,
                "destination" => array(
                    "account" => $sub_stripeUserId,
                ),
            ));
            //notify
            $sub_contractor->notify(new CustomerPaidForTask($task, $sub_contractor));
        }

        // pay the general the customer price - sub price
        if ($generalAmount > 0) {
            $g_charge = \Stripe\Charge::create(array(
                "amount" => (int)$generalAmount * 100,
                "currency" => "usd",
                "customer" => Auth::user()->stripe_id,
                "destination" => array(
                    "account" => $general_stripeUserId,
                ),
            ));
            // notify
            $general_contractor->notify(new CustomerPaidForTask($task, $general_contractor));
        }

        // update task status
        $jobTask->updateStatus(__('bid_task.customer_sent_payment'));

        return response()->json([$s_charge, $g_charge], 200);
    }

}
