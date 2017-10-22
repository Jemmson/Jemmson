<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;

class BidListController extends Controller
{
  //
  public function index()
  {
      // get jobs related to contractor from modal
      $jobs = Auth::user()->jobs();
      return view('/bid-list')->with(['jobs' => $jobs]);

  }
}
