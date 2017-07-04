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
    }
}
