<?php

namespace App\Http\Controllers;

use App\Job;
use App\User;
use App\Task;
use App\Customer;
use App\Contractor;
use App\JobTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use Validator;
use Auth;

use App\Notifications\NotifyJobHasBeenApproved;

class JobController extends Controller
{
    /**
     * Construct
     */
    public function __construct()
    {
        $this->middleware('further.info', ['only' => 'edit']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Job::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'agreed_start_date' => 'required',
            'agreed_end_date' => 'required',
            'bid_price' => 'required'
        ]);
        Log::info($request->all());
        if ($validator->fails()) {
            return response()->json(['error' => 'Missing fields'], 400);
        }
        
        $job = Job::create($request->all());

        return response()->json($job, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Job $job
     * @return \Illuminate\Http\Response
     */
    public function show(Job $job)
    {
        $contractor = User::find($job->contractor_id);
        return view('jobs.job')->with(['job' => $job, 'contractor' => $contractor->name]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Job $job
     * @return \Illuminate\Http\Response
     */
    public function edit(Job $job)
    {
        //dd($job);
        $bids = Task::getBidPrices($job->id);
        $contractor = User::with('contractor')->find($job->contractor_id);
        $customer = User::with('customer')->find($job->customer_id);
        
        if ($customer == null) {
            $customer = "[]";
        }
        
        $tasks = $job->tasks()->get();
        $userType = Auth::user()->usertype;
        return view('jobs.edit_job',
            compact('job', 'contractor', 'customer', 'tasks', 'userType', 'bids'));
    }

    public function updateJobDate(Request $request)
    {
        $dateType = $request->params["dateType"];
        $job = Job::find(intval($request->params["id"]));
        $date = new Carbon($request->params["date"]);
        $job->$dateType = $date;
        $job->save();
        return 'Date Is Saved';
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Job $job
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Job $job)
    {
        $job->update($request->all());

        return response()->json($job, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Job $job
     * @return \Illuminate\Http\Response
     */
    public function destroy(Job $job)
    {
        $job->delete();

        return response()->json(null, 204);
    }

    /**
     * Approve job
     *
     * @param Request $request
     * @param Job $job
     * @return void
     */
    public function approveJob(Request $request, Job $job)
    {
        $this->validate($request, [
            'agreed_start_date' => 'required|date',
            'city' => 'string',
            // bid needs to be in the 'Waiting on Approval' in order to approve jbo
            'status' => 'required|regex:/\bbid.sent\b/',
        ]);
        // TODO: what date needs to be updated here?
        $job->agreed_start_date = $request->agreed_start_date;
        $job->status = __('job.approved');
        
        // approve all tasks associated with this job, any exceptions?
        JobTask::where('job_id', $job->id)
                //->where('bid_id', '!=', 'NULL') // update unless no bid connected to the job task
                ->update(['status' => __('bid_task.approved_by_customer')]);

        try {
            $job->save();
        } catch (\Exception $e) {
            Log::error('Approve Job: ' . $e->getMessage());
            return response()->json(["message"=>"Couldn't approve job.","errors"=>["error" =>[$e->getMessage()]]], 400);
        }

        $this->notifyAll($job);

        return response()->json($job, 200);
    }

    /**
     * Notify all contractors and sub connected to the job
     * that have approved bids 
     *
     * @param Job                      $job
     * @return void
     */
    protected function notifyAll($job)
    {
        $generalContractor = $job->contractor()->first();
        $subContractors = $job->subs();
        
        // notify general
        $generalContractor->notify(new NotifyJobHasBeenApproved($job, $generalContractor));
        foreach ($subContractors as $sub) {
            $notification = new NotifyJobHasBeenApproved($job, $sub->first());
            $notification->setSub(true);
            $sub->first()->notify($notification);
        }
    }

    public function action(Request $request)
    {
        $job = Job::find($request->job_id);

        if ($job == null) {
            return ['status' => false, 'error' => 'Resource not found'];
        }

        // do required action
        switch ($request->action) {
            case 'accept':
                $job->acceptJob();
                break;
            case 'approve':
                $job->approveJob();
                break;
            case 'decline':
                $job->declineJob();
                break;
            default: 
                return response()->json(['error' => 'action required'], 400);       
                break;
        }

        return response()->json($job->jobActions(), 200);        
    }

    /**
     * Get all jobs associated with user
     *
     * @return void
     */
    public function jobs(Request $request)
    {
        // load jobs and all their tasks along with those tasks relationships
        if (Auth::user()->isCustomer()) {
          // only load tasks on jobs that are approved or need approval
          $jobsWithTasks = Auth::user()->jobs()
          ->where('status', __('bid.sent'))
          ->orWhere('status', __('job.approved'))
          ->with(
            [
              'tasks' => function ($query) {
                $query->select('tasks.id', 'tasks.name', 'tasks.contractor_id', 'tasks.job_id');
                $query->with(
                [
                  'jobTask' => function ($q) {
                    // TODO: need to only return need to know columns, returns all data right now
                    //$q->select('job_task.id', 'job_task.contractor_id', 'job_task.status', 'job_task.cust_final_price', 'job_task.start_date');
                  }
                ]);
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

        return response()->json($jobs, 200); 
    }
}
