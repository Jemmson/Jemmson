<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BidListController extends Controller
{
  //
  public function index()
  {
      $contractor = true;
      $bids = [];

      if($contractor){
        // get bids related to contractor from modal
        $bids = [
                  ['jobName' => 'Restaurant Work - Joe', 'status' => 'pending'],
                  ['jobName' => 'Restroom Work - Marina', 'status' => 'completed'],
                  ['jobName' => 'Paveway Work - Salina', 'status' => 'initiated'],
                  ['jobName' => 'Pool - Jon', 'status' => 'pending'],
                  ['jobName' => 'Roof Work - Jose', 'status' => 'completed'],
                  ['jobName' => 'Restaurant #2 Work - Joe', 'status' => 'pending'],
                  ['jobName' => 'Backyard Work - Sarah', 'status' => 'initiated']
                ];
        return view('/contractors/bidlist')->with(['bids' => $bids]);
      }else{
        // get bids related to customer from modal
        $bids = [
                  ['jobName' => 'Livingroom Work - John', 'status' => 'pending'],
                  ['jobName' => 'Restroom Work - John', 'status' => 'completed'],
                  ['jobName' => 'Paveway Work - John', 'status' => 'initiated'],
                ];
        return view('/customers/bidlist')->with(['bids' => $bids]);
      }
  }
}
