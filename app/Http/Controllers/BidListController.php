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
        if ($this->isCustomer()) {
          // only load tasks on jobs that are approved or need approval
          $jobsWithTasks = Auth::user()->jobs()
          ->where('status', __('bid.sent'))
          ->orWhere('status', __('job.approved'))
          ->with(
            [
              'tasks' => function ($query) {
                $query->with('jobTask', 'bidContractorJobTasks');
              }
            ])->get();
          $jobsWithoutTasks = Auth::user()->jobs()
          ->where('status', '!=', __('bid.sent'))
          ->where('status', '!=', __('job.approved'))
          ->get();
          $jobs = $jobsWithTasks->merge($jobsWithoutTasks);
        } else {
          $jobs = Auth::user()->jobs()->with('tasks.jobTask', 'tasks.bidContractorJobTasks')->get();
        }
        
        return view('/bid-list', compact('jobs'));
    }
    
    private function isCustomer()
    {
        return Auth::user()->usertype === 'customer';
    }

}
