<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InitiateBidController extends Controller
{
    //
    public function index()
    {
        return view('/contractors/initiateBid');
    }

    public function send(Request $request)
    {
        dd($request);
        $request->job_name;
        // send a passwordless link if the email is not in the system
        // this link will then redirect them to the registration page

        // send a notification along with the passwordless link if the customer or sub is in the system

    }
}
