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
              'tasks' => function ($query) {
                $query->select('tasks.id', 'tasks.name', 'tasks.contractor_id', 'tasks.job_id');
                $query->with(
                [
                  'jobTask' => function ($q) {
                      $q->select('job_task.task_id', 'job_task.stripe', 'job_task.id', 'job_task.contractor_id', 'job_task.status', 'job_task.cust_final_price', 'job_task.start_date');
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
          $jobs = Auth::user()->jobs()->with('tasks.jobTask', 'tasks.bidContractorJobTasks.contractor')->get();
        }
        
        return view('/bid-list', compact('jobs'));
    }
    
    private function isCustomer()
    {
        return Auth::user()->usertype === 'customer';
    }

}
