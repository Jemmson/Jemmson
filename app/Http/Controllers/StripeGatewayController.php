<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StripeGatewayController extends Controller
{
    //

    public function getStripeOauthUrl($path)
    {
        $clientId = env('STRIPE_CLIENT_ID');
        $responseType = 'code';
        $scope = 'read_write';
        $stripeOauthEndpoint = 'https://connect.stripe.com/oauth/authorize';
        $state = "$path:" . uniqid() . "-" . uniqid() . "-" . uniqid() . "-" . uniqid();

        return "$stripeOauthEndpoint?response_type=$responseType&client_id=$clientId&scope=$scope&state=$state";
    }
}
