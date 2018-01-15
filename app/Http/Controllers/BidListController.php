<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use App\Job;

class BidListController extends Controller
{
  public function index()
  {
      return view('/bid-list')->with(['jobs' => Auth::user()->jobs()->get()]);
  }
}
