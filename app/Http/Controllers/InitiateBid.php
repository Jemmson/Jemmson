<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InitiateBid extends Controller
{
    //
    public function index()
    {
        return view('/contractors/initiateBid');
    }

    public function send(Request $request)
    {
        dd($request);

        // send a passwordless link if the email is not in the system
        // this link will then redirect them to the bid page

        // send a notification along with the passwordless link if the customer or sub is in the system

    }
}
