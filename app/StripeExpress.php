<?php

namespace App;

use App\Notifications\CustomerPaidForTask;
use App\Notifications\CustomerUnableToSendPaymentWithStripe;
use Illuminate\Database\Eloquent\Model;
use \App\ContractorCustomer;

class StripeExpress extends Model
{

    protected $table = 'stripe_expresses';
    protected $guarded = [];


    public function transferFunds($jobId, $jobTasks, $totalAmount, $chargeId, $transferGroupId)
    {

//            TODO :: Should throw an exception for amount not being greater than zero
        if ($totalAmount > 0) {

            $job = Job::find($jobId);
            $generalId = $job->contractor_id;
            $stripeFee = (int)round($totalAmount * .029 + 30);
            $jemmsonFee = $this->getJemmsonFee($jobId);
            $customerId = $job->customer_id;
            $subTotalAmounts = $this->getTotalSubAmount($jobTasks, $generalId);
            $transferGroup = TransferGroup::find($transferGroupId);

            $contractorProfit =
                $totalAmount - $stripeFee - $jemmsonFee - $subTotalAmounts;

            $transferToGeneral = $this->transferAmountToGeneral($contractorProfit, $generalId, $chargeId);
            $this->transferAmountToSubs(
                $jobTasks, $generalId, $chargeId, $contractorProfit, $stripeFee, $customerId, $transferGroup);

            $customer = $this->getCustomer($customerId);
            $this->createStripeCustomer($customer, $generalId);

            JobTask::markTasksAsPaid($jobTasks);

            $this->notifyGeneralAndSubs($jobTasks, $generalId);


        } else {
            return response()->json([
                "error" => "Cannot Charge Tasks Must Total Greater Than 0"
            ], 200);
        }

    }

    public function notifyGeneralAndSubs($jobTasks, $generalId)
    {
        $general = User::find($generalId);
        foreach ($jobTasks as $jobTask) {
            $task = $jobTask->task()->get()->first();
            if ($this->isASub($general->id, $jobTask->contractor_id)) {
                $sub_contractor = User::find($jobTask->contractor_id);
                $sub_contractor->notify(new CustomerPaidForTask($task, $sub_contractor));
            }
            $general->notify(new CustomerPaidForTask($task, $general));
        }
    }

    public function getGeneral($generalId)
    {
        return User::find($generalId);
    }

    public function getCustomer($customerId)
    {
        return User::where('id', '=', $customerId)->get()->first();
    }

    public function updateTransferGroupTable($attributes, $transferGroupId)
    {
        $transferGroup = TransferGroup::where("id", "=", $transferGroupId->id)->get()->first();
        $transferGroup->general_amount = $attributes['general_amount'];
        $transferGroup->sub_amount = $attributes['sub_amount'];
        $transferGroup->stripe_amount = $attributes['stripe_amount'];
        $transferGroup->job_task_id = $attributes['job_task_id'];
        $transferGroup->sub_id = $attributes['sub_id'];
        $transferGroup->customer_id = $attributes['customer_id'];

        try {
            $transferGroup->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }
    }

    public function isASub($generalId, $jobTaskContractorId)
    {
        return $generalId !== $jobTaskContractorId;
    }

    public static function getAPIKey($accountId)
    {
        return StripeExpress::where('stripe_user_id', '=', $accountId)->get()->first()->access_token;
    }

    public function transferAmountToSubs(
        $jobTasks,
        $generalId,
        $chargeId,
        $generalAmount,
        $stripeAmount,
        $customerId,
        $transferGroupId
    )
    {
        foreach ($jobTasks as $jobTask) {

            $subId = $jobTask->contractor_id;

            $subAmount = 0;

            if ($this->isASub($generalId, $subId)) {
                $stripeExpressId = $this->getStripeExpressId($subId);
                $amount = $jobTask->sub_final_price;
                $subAmount = $jobTask->sub_final_price;

                if (!$this->hasTransferCapability($stripeExpressId)) {
                    $this->updateTransferCapability($stripeExpressId);
                }

                $this->transfer($amount, $stripeExpressId, $chargeId);

            }

            $attributes = [
                "general_amount" => $generalAmount,
                "sub_amount" => $subAmount,
                "stripe_amount" => $stripeAmount,
                "job_task_id" => $jobTask->id,
                "sub_id" => $subId,
                "customer_id" => $customerId
            ];
            $this->updateTransferGroupTable($attributes, $transferGroupId);
        }
    }

    public function getStripeExpressId($contractorId)
    {
        return StripeExpress::where('contractor_id', '=', $contractorId)
            ->get()->first()->stripe_user_id;
    }

    public function transferAmountToGeneral($amount, $generalId, $chargeId)
    {
        $general = $this->getGeneral($generalId);
        if ($this->hasTransferCapability($general->stripe_id)) {
            $stripeId = $this->getStripeExpressId($generalId);
            return $this->transfer($amount, $stripeId, $chargeId);
        } else {
            $this->updateTransferCapability($generalId);
        }

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

    public function hasTransferCapability($stripeId)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        \Stripe\Stripe::$apiVersion = '2019-08-14';
        $transferCapability = \Stripe\Account::retrieveCapability(
            $stripeId,
            'transfers'
        );

        return $transferCapability != 'unrequested' && $transferCapability->status != 'inactive';

    }

    public function updateTransferCapability($stripeId)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        \Stripe\Stripe::$apiVersion = '2019-08-14';

        $transferCapability = \Stripe\Account::updateCapability(
            $stripeId,
            'transfers',
            ['requested' => true]
        );

        return $transferCapability->status == 'inactive' || $transferCapability->status == 'unrequested';

    }

    public function getJemmsonFee($jobId)
    {
        if (JobTask::atLeastOnTaskIsPaid($jobId)) {
            return 0;
        } else {
            return env('JEMMSON_FLAT_RATE');
        }
    }

    public function getTotalSubAmount($jobTasks, $generalId)
    {
        $amount = 0;

        foreach ($jobTasks as $jobTask) {
            if ($generalId !== $jobTask->contractor_id) {
                $amount = $amount + $jobTask->sub_final_price;
            }
        }

        return $amount;
    }


    public static function makePayment($jobTask)
    {
        // get modals and relevant variables

        $task = $jobTask->task()->first();
        $sub_contractor_id = $jobTask->contractor_id;
        $general_contractor_id = $task->contractor_id;

        // if not payable how'd you get here?
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

    public function createStripeCustomer($customer, $generalId)
    {
        if ($this->stripeCustomerDoesNotExist($customer)) {
            return $this->addStripeToCustomer($customer, $generalId);
        } else {
            return $this->retrieveCustomer($customer->stripe_id);
        }
    }

    /*
     * All customers are Jemmson customers and not customers of the contractors
     *  as far as stripe is concerned
     */
    public function stripeCustomerDoesNotExist($customer)
    {
        return $customer->stripe_id == null;
    }

    public function retrieveCustomer($customerStripeId)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        \Stripe\Stripe::$apiVersion = '2019-08-14';

        return \Stripe\Customer::retrieve($customerStripeId);
    }

    public function addStripeToCustomer($customer, $generalId)
    {

        $stripeCustomer = $this->addCustomerToStripe($customer);
        $this->addStripeIdToCustomer($customer, $stripeCustomer, $generalId);

        return $stripeCustomer;

    }

    public function addCustomerToStripe($customer)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        \Stripe\Stripe::$apiVersion = '2019-08-14';

        return \Stripe\Customer::create([
            "address" => [
                "line1" => $customer->billing_address,
                "line2" => $customer->billing_address_line_2,
                "city" => $customer->billing_city,
                "state" => $customer->billing_state,
                "postal_code" => $customer->billing_zip,
                "country" => 'US'
            ],
            "name" => $customer->name,
            "email" => $customer->email,
            "phone" => $customer->phone
        ]);
    }

    public function addStripeIdToCustomer($customer, $stripeCustomer, $generalId)
    {
        $customer->stripe_id = $stripeCustomer->id;

        // TODO :: Should throw an exception for when a customer should be saved

        try {
            $customer->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }


        $cc = ContractorCustomer::where('contractor_user_id', '=', $generalId)
            ->where('customer_user_id', '=', $customer->id)
            ->get()->first();

        $cc->stripe_id = $stripeCustomer->id;

        try {
            $cc->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

    }

}
