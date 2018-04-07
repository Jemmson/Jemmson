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

use App\Notifications\CustomerUnableToSendPaymentWithStripe;
use App\Notifications\CustomerPaidForTask;


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
