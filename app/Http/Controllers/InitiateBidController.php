<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\PasswordlessBidPageLogin;
use Illuminate\Support\Facades\Mail;

class InitiateBidController extends Controller
{
    //
    public function index()
    {
        return view('/contractors/initiateBid');
    }

    public function send(Request $request)
    {
        //dd($request);
        // send a passwordless link if the email is not in the system
        // this link will then redirect them to the bid page

        //  TODO: create link
        $request->link = "jemmson.com/login/kjwoeijoijwe";

        // send mail
        Mail::to($request->email)
              ->queue(new PasswordlessBidPageLogin($request));

        // send a notification along with the passwordless link if the customer or sub is in the system
    }
}
