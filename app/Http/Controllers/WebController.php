<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebController extends Controller
{

    public function index () {
      return view('app');
    }
    
    public function bids()
    {
        return view('/bid-list');
    }

}
