<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BidListController extends Controller
{
  //
  public function index()
  {
      return view('/contractors/bidlist');
  }
}
