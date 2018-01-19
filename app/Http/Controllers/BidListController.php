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
      // load jobs and all their tasks along with those tasks relationships 
      $jobs = Auth::user()->jobs()->with('tasks.jobTask', 'tasks.bidContractorJobTasks')->get();
      return view('/bid-list', compact('jobs'));
  }
}
