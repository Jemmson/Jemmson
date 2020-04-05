<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    //

    public function plan(Request $request)
    {
//        set plan name in users table
//        set plan start date in users table
//        is this their initial setup then create transfer right away
//              could be an issue if I have to wait for a job to be completed
//              for them to have cash in their account
    }

    public function bill()
    {
//        run this once a day
//        check user table for all day of months of start date
//        transfer from their stripe accounts to mine
//        if they do not have the funds then set their plan to null and their stripe_id to null
//        they will have to go through setting up stripe again if they do not have the funds
//        should be alot quicker since their account is setup already
    }

}
