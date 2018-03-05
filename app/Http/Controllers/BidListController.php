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
          ->where(function ($query) {
            $query->where('status', __('bid.sent'))
              ->orwhere('status', __('job.approved'))
              ->orwhere('status', __('bid.declined'));
          })
          ->with(
            [
              'jobTasks' => function ($query) {
                //$query->select('id', 'task_id', 'stripe', 'contractor_id', 'status', 'cust_final_price', 'start_date');
                $query->with(
                [
                  'task' => function ($q) {
                      $q->select('tasks.id', 'tasks.name', 'tasks.contractor_id');
                  }
                ]);
              }
              // NOTICE: 'with' resets the original result to all jobs?! this fixes a customer seeing others customers jobs that have been approved 
            ])->get();

          $jobsWithoutTasks = Auth::user()->jobs()
          ->where('status', '!=', __('bid.sent'))
          ->where('status', '!=', __('job.approved'))
          ->Where('status', '!=',__('bid.declined'))
          ->get();
          $jobs = $jobsWithTasks->merge($jobsWithoutTasks);
        } else {
          $jobs = Auth::user()->jobs()->with('jobTasks.task', 'jobTasks.bidContractorJobTasks.contractor')->get();
        }
        
        return view('/bid-list', compact('jobs'));
    }
    
    private function isCustomer()
    {
        return Auth::user()->usertype === 'customer';
    }

}
