<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Stripe\Account;

use Auth;
use Redirect;
use Log;

use App\StripeExpress;

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
            return redirect('bid-list');
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
            return redirect('bid-list');
        }

        return redirect('bid-list');
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
     * Handles charging a customer on behalf of a contractor
     * with the the express stripe integration 
     *
     * @param Request $request
     * @return void
     */
    public function chargeCustomerExpress(Request $request)
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
            return response()->json(['message' => "Couldn't create customer"], 200);
        }

        if (Auth::user()->saveStripeId($customer->id) && Auth::user()->saveCardInformation($request->card)) {
            return response()->json(['id' => $customer->id], 200);
        } 
        
        return response()->json(['message' => "Coulnd't save customer"], 400);
    }
}
