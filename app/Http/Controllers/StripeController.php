<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Stripe\Account;

use Auth;
use Redirect;
use Log;

use App\StripeExpress;
use App\Task;
use App\JobTask;
use App\User;
use App\Job;

use App\Notifications\CustomerUnableToSendPaymentWithStripe;
use App\Notifications\CustomerPaidForTask;

use Illuminate\Database\Eloquent\Collection;

class StripeController extends Controller
{

    /**
     * Your application sends the user to Stripe’s website to provide the necessary details, including banking 
     * and contact information.
     *
     * @return void
     */
    public function expressConnect()
    {
        if (Auth::user()->usertype === 'customer') {
            return response()->json(['message' => 'Not Allowed'], 422);
        }

        $link = "https://connect.stripe.com/express/oauth/authorize?redirect_uri" .
                 "=https://stripe.com/connect/default/oauth/test&client_id=" .
                 config('services.stripe.client_id') . "&state={STATE_VALUE}";
        return Redirect::to($link);
    }

    /**
     * When Stripe redirects the user back to the application, it appends the user’s authorization code as
     * a query parameter in the URL. To finalize the connection, send a POST request to Stripe with the authorization
     * code and store the returned account information for later use.
     *
     * @return void
     */
    public function expressAuth(Request $request)
    {
        // TODO: do we want to update the existing express model if
        // they for some reason connect again?

        $url = "https://connect.stripe.com/oauth/token";
        $data = [
            'client_secret' => config('services.stripe.secret'),
            'code' => $request->code,
            'grant_type' => "authorization_code"
        ];

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($data));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //execute post
        $result = json_decode(curl_exec($ch));
        //close connection
        curl_close($ch);

        //$re = '{ "access_token": "sk_test_SGRDUquogKpdiWrN2qqaUCsf", 
        //"livemode": false, 
        //"refresh_token": "rt_CEYH4vUDPftWVnOfQc85DiHOpwBxBE86eIfSkWJfRi4wIsWr", 
        //"token_type": "bearer", "stripe_publishable_key": "", 
        //"stripe_user_id": "acct_1Bq5AlHwoZeFcla2", "scope": "express" }';
        
        if (isset($result->error)) {
            Log::error('Stripe Express Auth: ' . $result->error_description);
            return redirect("/#/" . $request->state . "?error=" . $result->error_description);
        }

        $stripeExpress = new StripeExpress();
        $stripeExpress->access_token = $result->access_token;
        $stripeExpress->refresh_token = $result->refresh_token;
        $stripeExpress->stripe_user_id = $result->stripe_user_id;
        $stripeExpress->contractor_id = Auth::user()->id;

        try {
            $stripeExpress->save();
        } catch (\Exception $e) {
            Log::error('New StripeExpress: ' . $e->getMessage());
            return redirect("/#" . $request->state . "?error=Sorry we couldn't create your express account at this time");
        }

        return redirect("/#" . $request->state . "?success=You may now submit the bid.");
    }

    /**
     * Create one time link the user can use to 
     * access their express dashboard
     *
     * @return void
     */
    public function createExpressDashboardLink()
    {
        try {
            $account = Account::retrieve(Auth::user()->contractor()->first()->stripeExpress()->first()->stripe_user_id);
            $link = $account->login_links->create();
        } catch (\Exception $e) {
            Log::error('Creating Stripe Express Link: ' . $e->getMessage());
            return response()->json(['message' => "could't create link", 'errors' => ['error' => $e->getMessage()]]);
        }
        
        return response()->json($link, 200);
    }

    /**
     * Handles sending a payment for a task 
     * to the contractor connected to that task along with 
     * the general contractors cut
     *
     * @param Request $request
     * @return Response
     */
    public function sendExpressTaskPayment(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);
        
        // get modals and relevant variables 
        $jobTask = JobTask::find($request->id);
        $task = $jobTask->task()->first();
        $sub_contractor_id = $jobTask->contractor_id;
        $general_contractor_id = $task->contractor_id;

        // if not payable howd you get here?
        if (!$jobTask->updatable(__('bid_task.customer_sent_payment'))) {
            return response()->json(['message' => "Can't pay for this task yet."], 422);
        }

        // amounts
        $subAmount = (int) $jobTask->sub_final_price;
        $generalAmount = (int) $jobTask->cust_final_price - $subAmount;

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
                "amount" => (int) $generalAmount * 100,
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

    /**
     * Sets all payable tasks as paid
     *
     * @param Request $request
     * @return response
     */
    public function payAllPayableTasksWithCash(Request $request)
    {
        $this->validate($request, [
            'id' => 'required' // job id
        ]);

        // job tasks excluded from this payment [jobtask ids => true]
        $excluded = $request->excluded;

        // get all tasks that havent been paid for 
        $job = Job::find($request->id);
        $jobTasks = $job->jobTasks()->where('status', 'bid_task.finished_by_general')->orWhere('status', 'bid_task.approved_by_general')->get();

        if (count($jobTasks) < 1) {
            return response()->json(['message' => 'No Tasks'], 422);
        }

        $order = 'job.' . $job->id;
        $transfers = [];

        foreach ($jobTasks as $jobTask) {
            if (isset($excluded[$jobTask->id]) && $excluded[$jobTask->id]) {
                continue;
            }
            $order .= '.' . $jobTask->id;
            $transfers[$jobTask->id] = $order . '.cash';

            // notify contractors that tasks were paid
            $task = $jobTask->task()->first();
            $sub_contractor_id = $jobTask->contractor_id;
            $general_contractor_id = $task->contractor_id;

            $general_contractor = User::find($general_contractor_id);
            
            if ($sub_contractor_id != $general_contractor_id) {
                $sub_contractor = User::find($sub_contractor_id);
                $sub_contractor->notify(new CustomerPaidForTask($task, $sub_contractor));
            } 
            $general_contractor->notify(new CustomerPaidForTask($task, $general_contractor));
        }

        $this->updateJobTasksAsPaid($jobTasks, $transfers, $excluded);
        
        $job->setJobAsCompleted();

        return response()->json(['message' => "Payment Succesful"], 200);
    }

    /**
     * Pay all payable tasks
     * Adds up the total customer total
     * Charges that price once
     * Splits the payment for all general and subs involved with multiple transfers on stripes side
     *
     * @param Request $request
     * @return response
     */
    public function payAllPayableTasks(Request $request)
    {
        $this->validate($request, [
            'id' => 'required' // job id
        ]);

        // job tasks excluded from this payment [jobtask ids => true]
        $excluded = $request->excluded;

        $customerId = Auth::user()->stripe_id;

        if ($customerId === null) {
            return response()->json(['message' => 'No Card On File'], 422);
        }

        // get all tasks that havent been paid for 
        $job = Job::find($request->id);
        $jobTasks = $job->jobTasks()->where('status', 'bid_task.finished_by_general')->orWhere('status', 'bid_task.approved_by_general')->get();

        if (count($jobTasks) < 1) {
            return response()->json(['message' => 'No Tasks'], 422);
        }

        // customer total
        $total = 0;
        $order = 'job.' . $job->id;

        foreach ($jobTasks as $jobTask) {
            if (isset($excluded[$jobTask->id]) && $excluded[$jobTask->id]) {
                continue;
            }
            $total += $jobTask->cust_final_price;
            $order .= '.' . $jobTask->id;
        }

        if (!$this->allContractorsHaveExpressConnected($jobTasks)) {
            return response()->json(['message' => 'Not all contractors have Stripe'], 422);
        }

        $charge = $this->createDetachedCharge($total, $order, $customerId);
        if (gettype($charge) === 'string') {
            return response()->json(['message' => $charge], 422);
        }
        
        $transfers = $this->transferPaymentsToContractors($jobTasks, $charge->id, $excluded);
        if (gettype($transfers) === 'string' && true) {
            $this->refundDetachedCharge($charge->id);
            return response()->json(['message' => $transfers], 422);
        }

        $this->updateJobTasksAsPaid($jobTasks, $transfers, $excluded);
        
        $job->setJobAsCompleted();

        return response()->json(['message' => "Payment Succesful"], 200);
    }

    /**
     * Create a charge with no destination ie it goes to the platform (jemsub)
     * should be used along with @transferPaymentsToContractors
     *
     * @param Float $total
     * @param String $order
     * @param String $customerId
     * @return bool|String 
     */
    private function createDetachedCharge(Float $total, String $order, String $customerId)
    {
        if ($total <= 0) {
            return "Nothing To Pay";
        }
        try {
            $charge = \Stripe\Charge::create(array(
                "amount" => $total * 100,
                "currency" => "usd",
                "customer" => $customerId,
                "transfer_group" => $order,
            ));
        } catch(\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            Log::error('Stripe: ' . $e->getMessage());
            return $e->getMessage();        
        } catch (\Stripe\Error\RateLimit $e) {
            // Too many requests made to the API too quickly
            Log::error('Stripe: ' . $e->getMessage());
            return $e->getMessage();     
        } catch (\Stripe\Error\InvalidRequest $e) {
            // Invalid parameters were supplied to Stripe's API
            Log::error('Stripe: ' . $e->getMessage());
            return $e->getMessage();     
        } catch (\Stripe\Error\Authentication $e) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            Log::error('Stripe: ' . $e->getMessage());
            return $e->getMessage();     
        } catch (\Stripe\Error\ApiConnection $e) {
            // Network communication with Stripe failed
            Log::error('Stripe: ' . $e->getMessage());
            return $e->getMessage();     
        } catch (\Stripe\Error\Base $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
            Log::emergency('Stripe: ' . $e->getMessage());
            return $e->getMessage();     
        } catch (Exception $e) {
            // Something else happened, completely unrelated to Stripe
            Log::emergency('Stripe: ' . $e->getMessage());
            return $e->getMessage();     
        }
        return $charge;
    }

    /**
     * Refund detached charge
     *
     * @param String $chargeId
     * @return void
     */
    private function refundDetachedCharge(String $chargeId)
    {
        try {
            $re = \Stripe\Refund::create(array(
                "charge" => $chargeId
            ));
        } catch (\Excpetion $e) {
            Log::error('Stripe: ' . $e->getMessage());
        }
    }

    /**
     * Transfer payments to all subs and generals attached to the
     * passed in collection of PAID tasks
     *
     * @param Collection $jobTasks
     * @param String $order
     * @param Array $excluded
     * @return void
     */
    private function transferPaymentsToContractors(Collection $jobTasks, String $chargeId, Array $excluded)
    {
        $transfers = [];
        foreach ($jobTasks as $jobTask) {
            if (isset($excluded[$jobTask->id]) && $excluded[$jobTask->id]) {
                continue;
            }
            $task = $jobTask->task()->first();
            $sub_contractor_id = $jobTask->contractor_id;
            $general_contractor_id = $task->contractor_id;
            
            // amounts
            $subAmount = (int) $jobTask->sub_final_price;
            $generalAmount = (int) ($jobTask->cust_final_price) - $subAmount;

            Log::debug('Sub Amount: ' .  $subAmount);
            Log::debug('Gen Amount: ' .  $generalAmount);

            $sub_contractor = User::find($sub_contractor_id);
            $general_contractor = User::find($general_contractor_id);

            $sub_stripeExpress = $sub_contractor->contractor()->first()->stripeExpress()->first();
            $general_stripeExpress = $general_contractor->contractor()->first()->stripeExpress()->first();
            
            // transfer to sub
            if ($subAmount > 0) {
                try {
                    $transfer = \Stripe\Transfer::create(array(
                        "amount" => $subAmount * 100,
                        "currency" => "usd",
                        "destination" => $sub_stripeExpress->stripe_user_id,
                        "source_transaction" => $chargeId,
                    ));
                    $transfers[$jobTask->id] = $transfer->id;
                    $sub_contractor->notify(new CustomerPaidForTask($task, $sub_contractor));
                } catch(\Exception $e) {
                    Log::emergency('Transfering Payments Sub: ' . $e->getMessage());
                    $this->reverseTransfers($transfers);
                    return $e->getMessage();                
                }
            }

            // transfer to general
            if ($generalAmount > 0) {
                try {
                    $transfer = \Stripe\Transfer::create(array(
                        "amount" => $generalAmount * 100,
                        "currency" => "usd",
                        "destination" => $general_stripeExpress->stripe_user_id,
                        "source_transaction" => $chargeId,
                    ));
                    $transfers[$jobTask->id] = $transfer->id;
                    $general_contractor->notify(new CustomerPaidForTask($task, $general_contractor));
                } catch(\Exception $e) {
                    Log::emergency('Transfering Payments General: ' . $e->getMessage());
                    $this->reverseTransfers($transfers);
                    return $e->getMessage();                
                }
            }
        }

        return $transfers;
    }

    /**
     * Check That all contractors have stripe express for
     * the given collection
     *
     * @param Collection $jobTasks
     * @return bool
     */
    private function allContractorsHaveExpressConnected(Collection $jobTasks)
    {

        foreach ($jobTasks as $jobTask) {
            if (isset($excluded[$jobTask->id]) && $excluded[$jobTask->id]) {
                continue;
            }
            $task = $jobTask->task()->first();
            $sub_contractor_id = $jobTask->contractor_id;
            $general_contractor_id = $task->contractor_id;
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
                return false;
            }
        }

        return true;
    }

    /**
     * Reverses any transfers completed with the given transfer ids
     *
     * @param Array $transfers
     * @return void
     */
    private function reverseTransfers(Array $transfers)
    {
        foreach ($transfers as $transfer) {
            $tr = \Stripe\Transfer::retrieve($transfer['transfer_id']);
            try {
                $re = $tr->reversals->create();
            } catch (\Exception $e) {
                Log::emergency('Reversing Transfer @ transferPaymentsToContractors: ' . $e->getMessage());
            }
        }
    }

    /**
     * Update models with transfer id
     *
     * @param Collection $jobTasks
     * @param Array $transfers
     * @param Array $excluded
     * @return void
     */
    private function updateJobTasksAsPaid(Collection $jobTasks, Array $transfers, Array $excluded)
    {
        foreach ($jobTasks as $jobTask) {
            if (isset($excluded[$jobTask->id]) && $excluded[$jobTask->id]) {
                continue;
            }
            $jobTask->paid($transfers[$jobTask->id]);
        }
    }

    /**
     * Pay all tasks selected
     *
     * @param Request $request
     * @return Response
     */
    public function sendMultipleExpressTaskPayments(Request $request)
    {

    }

    /**
     * Charge a customer with their customer id
     * on the platform account, not express accounts
     *
     * @param Request $request
     * @return Response
     */
    public function chargeCustomer(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required'
        ]);
        
        $amount = $request->amount;
        $id = Auth::user()->stripe_id;

        if ($id === null) {
            return response()->json(['message' => 'Customer not in stripe yet'], 422);
        }

        try {
            // Charge the Customer
            $charge = \Stripe\Charge::create(array(
                "amount" => $amount,
                "currency" => "usd",
                "customer" => $id
            ));
        } catch (\Excpetion $e) {
            Log::error('Charging Customer: ' . $e->getMessage());
            return response()->json(['message' => "Couln't charge card", 'errors' => ['error' => $e->getMessage()]], 400);
        }
        return response()->json($charge, 200);
    }

    /**
     * Save a stripe customer so we can charge them later
     * without asking for cc again
     *
     * @param Request $request
     * @return Boolean
     */
    public function saveCustomer(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);

        // customer exists already returns its id
        if (Auth::user()->stripe_id !== null) {
            return response()->json(['id' => Auth::user()->stripe_id], 200);
        }

        try {
            // Create a Customer:
            $customer = \Stripe\Customer::create(array(
                "email" => Auth::user()->email,
                "source" => $request->id,
            ));
        } catch (\Excpetion $e) {
            Log::error('Creating Stripe Customer: ' . $e->getMessage());
            return response()->json(['message' => "Couldn't create customer"], 400);
        }

        if (Auth::user()->saveStripeId($customer->id) && Auth::user()->saveCardInformation($request->card)) {
            return response()->json(['id' => $customer->id], 200);
        } 
    }

    /**
     *Customer Paid with cash
     *
     * @param Request $request
     * @return Response
     */
    public function taskPaidWithCash(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);
        
        // get modals and relevant variables 
        $jobTask = JobTask::find($request->id);
        $task = $jobTask->task()->first();
        $jobTask = $task->jobTask()->first();
        $sub_contractor_id = $jobTask->contractor_id;
        $general_contractor_id = $task->contractor_id;

        // if not payable howd you get here?
        if (!$jobTask->updatable(__('bid_task.customer_sent_payment'))) {
            return response()->json(['message' => "Can't pay for this task yet."], 422);
        }

        // get contractors
        $sub_contractor = User::find($sub_contractor_id);
        $general_contractor = User::find($general_contractor_id);

        $general_contractor->notify(new CustomerPaidForTask($task, $general_contractor));
        
        // update task status
        $jobTask->updateStatus(__('bid_task.customer_sent_payment'));

        return response()->json(['message' => 'success'], 200);
    }

    public function deleteCard()
    {
        $user = Auth::user();

        $cards = \Stripe\Customer::retrieve($user->stripe_id)->sources->all(array(
        'limit'=> 1, 'object' => 'card'));
        $card = $cards->data[0];
        $customer = \Stripe\Customer::retrieve($user->stripe_id);
        $response = $customer->sources->retrieve($card->id)->delete();
        
        $user->deleteCard();
        
        return $response;
    }
}
