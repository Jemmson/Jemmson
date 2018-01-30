<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

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
}
