<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use App\Job;
use Illuminate\Support\Facades\DB;

class BidListController extends Controller
{
  public function index()
  {
//      TODO: this is pulling the wrong query. this query is calling this
//      TODO: "select * from `jobs` where `jobs`.`contractor_id` = '3' and `jobs`.`contractor_id` is not null"
//      TODO: "This should be - select * from jobs where contractor_id = 4 and contractor_id is not null"
      // load jobs and all their tasks along with those tasks relationships 
//      $jobs = Auth::user()->jobs()->with('tasks.jobTask', 'tasks.bidContractorJobTasks')->get();
      $jobs = DB::select('select * from jobs where contractor_id = 4 and contractor_id is not null');
//      dd($jobs);
//      dd(gettype($jobs));
//      return view('/bid-list')->with(['jobs' => $jobs]);
      return view('/bid-list', compact('jobs'));
  }
}
