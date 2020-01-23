<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Spark\Services\Stripe;

class StripePaymentGatewayTest extends TestCase
{
    public function test_charges_with_a_valid_payment_gateway_are_successful()
    {

        $paymentGateway = new StripePaymentGateway;

        $token = \Stripe\Token::create([
            "number" => "4242424242424242",
            "exp_month" => "1",
            "exp_year" => date('Y') + 1,
            "cvc" => "123"
        ], ['api_key' => config('services.stripe.secret')])->id;

    }

    /**  @test */
    function that_can_connect_using_the_oauth_link()
    {
        //

        $clientId = 'ca_Cb5HGB6tDEhW7HxWGxFjoyiR7ds1S9ca';
        $redirect_uri = 'http://localhost://9500';
        $responseType = 'code';
        $scope = 'read_write';
        $stripeOauthEndpoint = 'https://connect.stripe.com/oauth/authorize';
        $state = uniqid() . "-" . uniqid() . "-" . uniqid() . "-" . uniqid() ; // prevent CSRF attacks

//        ***************************
//        ** Step 1
//        ** Oauth link

        echo "$stripeOauthEndpoint?response_type=$responseType&client_id=$clientId&scope=$scope&state=$state";
//        Standard
//        https://dashboard.stripe.com/oauth/authorize?response_type=code&client_id=ca_Cb5HGB6tDEhW7HxWGxFjoyiR7ds1S9ca&scope=read_write

//        Express
//        https://dashboard.stripe.com/express/oauth/authorize?response_type=code&client_id=ca_Cb5HGB6tDEhW7HxWGxFjoyiR7ds1S9ca&scope=read_write

//        ***************************
//        ** Step 2
//        ** User connects their info to Stripe


//        ***************************
//        ** Step 3
//        ** User is redirected back to my site
//        ** https://gemsub.com/stripe/express/auth?scope=read_write&code={AUTHORIZATION_CODE}
//        ** An Error will return if there is an issue with the Oauth -> error=
//        ** https://gemsub.com/stripe/express/auth?error=access_denied&error_description=The%20user%20denied%20your%20request
//**
//        http://localhost:9500/stripe/express/auth?
//**        state=5e2903fd2b391-5e2903fd2b393-5e2903fd2b394-5e2903fd2b395
//**        &scope=read_write
//**        &code=ac_Gb7cK64Oo5CW9YeXWrkMEzL1mnodWH2V




//        ***************************
//        ** Step 4
//        ** Fetch the user's credentials from Stripe

        // Set your secret key: remember to change this to your live secret key in production
        // See your keys here: https://dashboard.stripe.com/account/apikeys
                \Stripe\Stripe::setApiKey('sk_test_ebg7SjOI3rsZkeV5SZsUkOon');

                $response = \Stripe\OAuth::token([
                    'grant_type' => 'authorization_code',
                    'code' => 'ac_123456789',
                ]);

        // Access the connected account id in the response
//                $connected_account_id = $response->stripe_user_id;


    }

}
