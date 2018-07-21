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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Validator;
use Auth;

use App\Notifications\NotifyJobHasBeenApproved;
use App\Notifications\JobBidDeclined;
use App\Notifications\NotifyCustomerThatBidIsFinished;
use App\Notifications\NotifyContractorOfDeclinedBid;
use App\Notifications\JobCanceled;



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
        // load jobs and all their tasks along with those tasks relationships
        if ($this->isCustomer()) {
            // only load tasks on jobs that are approved or need approval
            $jobsWithTasks = Auth::user()->jobs()
                ->where(function ($query) {
                    $query->where('status', __('bid.sent'))
                        ->orwhere('status', __('job.approved'))
                        ->orwhere('status', __('bid.declined'))
                        ->Where('status', '!=', __('job.completed'));
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
                        },
                        'jobTasks.location'
                        // NOTICE: 'with' resets the original result to all jobs?! this fixes a customer seeing others customers jobs that have been approved
                    ])->get();

            $jobsWithoutTasks = Auth::user()->jobs()
                ->where('status', '!=', __('bid.sent'))
                ->where('status', '!=', __('job.approved'))
                ->Where('status', '!=', __('bid.declined'))
                ->Where('status', '!=', __('job.completed'))
                ->get();
            $jobs = $jobsWithTasks->merge($jobsWithoutTasks);
        } else {

            $jobs = Auth::user()
                ->jobs()
                ->with(
                    [
                        'jobTasks.task',
                        'jobTasks.bidContractorJobTasks.contractor',
                        'jobTasks.location',
                        'customer' => function ($query) {
                            $query->select('id', 'name');
                        }
                    ]
                )
                ->where('status', '!=', __('job.completed'))
                ->get();
        }

        return response()->json($jobs, 200);
    }

    /**
     * Invoices
     *
     * @return \Illuminate\Http\Response
     */
    public function getInvoices()
    {
        if ($this->isCustomer()) {
            $invoices = Auth::user()->jobs()
                ->where(function ($query) {
                    $query->where('status', __('job.completed'));
                })
                ->with(
                    [
                        'jobTasks' => function ($query) {
                            $query->with(
                                [
                                    'task' => function ($q) {
                                        $q->select('tasks.id', 'tasks.name', 'tasks.contractor_id');
                                    }
                                ]);
                        }
                    ])->get();

        } else {
            $invoices = Auth::user()->jobs()->where('status', __('job.completed'))->with('jobTasks.task', 'jobTasks.bidContractorJobTasks.contractor')->get();
            $subInvoices = Auth::user()->contractor()->first()->jobTasks()->where('bid_id', '!=', null)->where('status', 'bid_task.customer_sent_payment')->with('task')->get();
            $invoices = $invoices->merge($subInvoices);
        }

        return response()->json($invoices, 200);
    }

    public function getInvoice(Job $job)
    {
        $job->load('location', 'jobTasks.task');
        return $job;
    }

    public function getSubInvoice(JobTask $jobTask)
    {
        $jobTask->load('task', 'location');
        return $jobTask;
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
            'bid_price' => 'required|numeric'
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
        $job->load(
            [
                'jobTasks.task',
                'jobTasks.bidContractorJobTasks.contractor',
                'location',
                'jobTasks.location',
                'jobTasks.images',
                'customer' => function ($query) {
                    $query->select('id', 'name');
                }
            ]
        );

        $job->jobTotal();

        return $job;
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
            'status' => 'required|regex:/\bbid.sent\b/',
        ]);

        if (!$request->job_location_same_as_home) {
            $this->validate($request, [
                'address_line_1' => 'required|min:2',
                'city' => 'required|min:2',
                'state' => 'required|min:2',
                'zip' => 'required|min:2',
            ]);
        }

        // TODO: what date needs to be updated here?
        $job->agreed_start_date = $request->agreed_start_date;
        $job->status = __('job.approved');

        $location_id = Auth::user()->customer()->first()->location_id;

        $result = DB::transaction(function () use ($job, $request, $location_id) {
            if ($request->job_location_same_as_home) {
                $job->location_id = $location_id;
            } else {
                $job->newLocation($request);
            }
            $job->save();
            // approve all tasks associated with this job, any exceptions?
            JobTask::where('job_id', $job->id)
                //->where('bid_id', '!=', 'NULL') // update unless no bid connected to the job task
                ->update(['status' => __('bid_task.approved_by_customer')]);
            JobTask::where('job_id', $job->id)
                ->where('start_when_accepted', true)
                ->update(['start_date' => Carbon::now()]);
        });

        $this->notifyAll($job);

        return response()->json($job, 200);
    }

    /**
     * Notify all contractors and sub connected to the job
     * that have approved bids
     *
     * @param Job $job
     * @return void
     */
    protected function notifyAll($job)
    {
        $generalContractor = $job->contractor()->first();
        $subContractors = $job->subs();
        $notified = [];
        // notify general
        $generalContractor->notify(new NotifyJobHasBeenApproved($job, $generalContractor));
        $notified[$generalContractor->id] = true;
        foreach ($subContractors as $sub) {
            $sub = $sub->first();
            if (!isset($notified[$sub->id])) {
                $notified[$sub->id] = true;
                $notification = new NotifyJobHasBeenApproved($job, $sub);
                $notification->setSub(true);
                $sub->notify($notification);
            }
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
        if ($this->isCustomer()) {
            // only load tasks on jobs that are approved or need approval
            $jobsWithTasks = Auth::user()->jobs()
                ->where(function ($query) {
                    $query->where('status', __('bid.sent'))
                        ->orwhere('status', __('job.approved'))
                        ->orwhere('status', __('bid.declined'))
                        ->Where('status', '!=', __('job.completed'));
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
                        },
                        'jobTasks.location'
                        // NOTICE: 'with' resets the original result to all jobs?! this fixes a customer seeing others customers jobs that have been approved
                    ])->get();

            $jobsWithoutTasks = Auth::user()->jobs()
                ->where('status', '!=', __('bid.sent'))
                ->where('status', '!=', __('job.approved'))
                ->Where('status', '!=', __('bid.declined'))
                ->Where('status', '!=', __('job.completed'))
                ->get();
            $jobs = $jobsWithTasks->merge($jobsWithoutTasks);
        } else {

            $jobs = Auth::user()
                ->jobs()
                ->with(
                    [
                        'jobTasks.task',
                        'jobTasks.bidContractorJobTasks.contractor',
                        'jobTasks.location',
                        'customer' => function ($query) {
                            $query->select('id', 'name');
                        }
                    ]
                )->where('status', '!=', __('job.completed'))->get();
        }

        return response()->json($jobs, 200);
    }

    /**
     * Customer did not approve of the job bid
     *
     * @param Request $request
     * @return Response
     */
    public function declineJobBid(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'message' => 'string|nullable'
        ]);

        if (isset($request->message) && $request->message != '') {
            $message = $request->message;
        } else {
            $message = '';
        }

        $job = Job::find($request->id);
        $contractor = User::find($job->contractor_id);

        if ($job->updateStatus(__('bid.declined'))) {
            $contractor->notify(new JobBidDeclined($job, $contractor, $message));
            $job->setJobDeclinedMessage($message);
            return response()->json(['message' => 'Success'], 200);
        }
        return response()->json(['message' => "Couldn't decline job, please try again."], 400);
    }

    /**
     * Customer has accepted a customers bid
     *
     * @param Request $request
     * @return void
     */
    public function acceptJob(Request $request)
    {
        $jobId = $request->jobId;
        $contractorId = $request->contractorId;

        $job = Job::find($jobId);
        $job->status = __('job.accepted');
        $job->save();                           // TODO: needs try catch here

        $user = User::find($contractorId);

        $user->notify(new NotifyContractorOfAcceptedBid());
    }

    /**
     * Customer has not accepted a customer bid
     *
     * @param Request $request
     * @return void
     */
    public function declineJob(Request $request)
    {
        $jobId = $request->jobId;
        $contractorId = $request->contractorId;

        $job = Job::find($jobId);
        $job->status = config('app.jobIsDeclined');
        $job->save();

        $user_id = Contractor::where('id', $contractorId)
            ->get()
            ->first()
            ->user_id;
        $user = User::where('id', $user_id)->get()->first();

        $user->notify(new NotifyContractorOfDeclinedBid());
    }

    /**
     * Notify customer that a contractor has finished
     * his bid for the specific job
     *
     * @param Request $request
     * @return void
     */
    public function finishedBidNotification(Request $request)
    {
        Log::debug('wtf');
        $jobId = $request->jobId;
        $customerId = $request->customerId;


        $user = User::find($customerId);
        $job = Job::find($jobId);

        $this->switchJobStatusToInProgress($job, __('bid.sent'));

        $user->notify(new NotifyCustomerThatBidIsFinished($job, $user));
    }

    public function updateArea(Request $request)
    {
        $job = Job::find($request->job_id);
        $job->updateArea($request->area);

    }

    public function getArea(Request $request)
    {
        $job = Job::find($request->job_id);
        return $job->getArea();
    }

    /**
     * Soft Deletes a job while its still in a bidding state
     *
     * @param Request $request
     * @return boolean
     */
    public function cancelJobBid(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);

        $job = Job::find($request->id);

        try {
            $job->delete();
        } catch (\Exception $e) {
            Log::error('Updating Job Status: ' . $e->getMessage());
            return response()->json(['message' => "Couldn't cancel job, please try again."], 400);
            return false;
        }

//        if ($job->updatable(__('bid.canceled'))) {
//            $job->updateStatus(__('bid.canceled'));
//            $job->delete();
//        } else {
//            return response()->json(['message' => "Couldn't cancel job, please try again."], 400);
//        }

        $currentUser = Auth::user()->id;
        if ($currentUser == $job->customer_id) {
            User::find($job->contractor_id)->notify(new JobCanceled());
        }
        if ($currentUser == $job->contractor_id) {
            User::find($job->customer_id)->notify(new JobCanceled());
        }

        return response()->json(['message' => 'Success'], 200);
    }

    /**
     * Put the job as completed
     *
     * @param Request $request
     * @return boolean
     */
    public function jobCompleted(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);

        $job = Job::find($request->id);

        if ($job->updatable(__('job.completed'))) {
            $job->updateStatus(__('job.completed'));
        } else {
            return response()->json(['message' => "Couldn't set the job as completed, All Tasks need to be resolved."], 400);
        }

        return response()->json(['message' => 'Success'], 200);
    }

    private function isCustomer()
    {
        return Auth::user()->usertype === 'customer';
    }

    public function switchJobStatusToInProgress($job, $message)
    {
        $job->status = $message;
        $job->save();
    }

}
