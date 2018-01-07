<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use App\Job;

class BidListController extends Controller
{
  //
  public function index()
  {
//      dd(Auth::user()->contractor()->contractor['user_id']);
//      dd(Auth::user()->usertype);
//      dd(Auth::user()->customer()->first()->id);
//      dd(Auth::user()->customer()->jobs()->get());
//      dd(Auth::user()->contractor());
//      dd(Auth::user());
      // get jobs related to contractor from modal
      $jobs = '';
      if (Auth::user()->usertype == 'contractor') {
          $jobs = Job::where('contractor_id', '=', Auth::user()->contractor()->first()->id)->get();
//          $jobs = Auth::user()->contractor()->first()->jobs();
      } else {
          $jobs = Job::where('customer_id', '=', Auth::user()->customer()->first()->id)->get();
//          $jobs = Auth::user()->customer()->first()->jobs();
      }
      return view('/bid-list')->with(['jobs' => $jobs]);
  }
}
