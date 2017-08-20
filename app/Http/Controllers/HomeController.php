<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('subscribed');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function show()
    {
        // this is the home page
        return view('home');
    }

    /**
     * Test different actions with a route
     * @return [type] [description]
     */
    public function test(Request $request){

    }
}
