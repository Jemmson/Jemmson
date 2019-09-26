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

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Notifications\NotifyJobHasBeenApproved;
use App\Notifications\JobBidDeclined;
use App\Notifications\NotifyCustomerThatBidIsFinished;
use App\Notifications\NotifyContractorOfDeclinedBid;
use App\Notifications\JobCanceled;
use App\Traits\ConvertPrices;
use App\Location;


class JobController extends Controller
{

    use ConvertPrices;

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
                        'jobTasks.task',
                        'jobTasks.task.contractor',
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
                        'jobTasks.task.contractor',
                        'jobTasks.bidContractorJobTasks.contractor',
                        'jobTasks.bidContractorJobTasks.contractor.contractor',
                        // 'jobTasks.bidContractorJobTasks.contractorSubContractorPreferredPayment',
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
     * @param \Illuminate\Http\Request $request
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

    private function isAuthorizedToUseResource($job)
    {
        return Auth::user()->id == $job->contractor_id || Auth::user()->id == $job->customer_id;
    }

    private function isCustomerBidNotSent($job)
    {
        return Auth::user()->id == $job->customer_id &&
            ($job->status == 'bid.initiated' || $job->status == 'bid.in_progress');
    }

    private function isCustomerWithSubmittedBid($job)
    {
        return Auth::user()->id == $job->customer_id && $job->status == 'bid.sent';
    }

    private function getCustomersJobLocation($job)
    {
        $locationResults = [];
        $location = Location::where('user_id', '=', $job->customer_id)->get()->first();
        array_push($locationResults, [
            "user_id" => $location->user_id,
            "default" => $location->default,
            "address_line_1" => $location->address_line_1,
            "address_line_2" => $location->address_line_2,
            "city" => $location->city,
            "state" => $location->state,
            "zip" => $location->zip,
            "area" => $location->area,
            "country" => $location->country,
            "created_at" => $location->created_at,
            "updated_at" => $location->updated_at,
            "lat" => $location->lat,
            "long" => $location->long,
        ]);

        return $locationResults[0];
    }

    private function getContractorInfoForCustomer($job)
    {
        $contractorResults = [];
        $contractor = Contractor::where('user_id', '=', $job->contractor_id)->get()->first();
        array_push($contractorResults, [
            "company_name" => $contractor->company_name
        ]);

        return $contractorResults[0];
    }


    private function getContractorUserInformation($job, $contractor)
    {
        $contractorUserResults = [];
        $contractorUser = User::where('id', '=', $job->contractor_id)->get()->first();
        array_push($contractorUserResults, [
            "id" => $contractorUser->id,
            "name" => $contractorUser->name,
            "email" => $contractorUser->email,
            "photo_url" => $contractorUser->photo_url,
            "logo_url" => $contractorUser->logo_url,
            "phone" => $contractorUser->phone,
            "first_name" => $contractorUser->first_name,
            "last_name" => $contractorUser->last_name,
            "contractor" => $contractor
        ]);

        return $contractorUserResults[0];
    }

    private function getCustomerTableInformation($job)
    {
        $customerResults = [];
        $customer = customer::where('user_id', '=', $job->customer_id)->get()->first();
        array_push($customerResults, [
            "notes" => $customer->notes
        ]);

        return $customerResults[0];

    }

    private function getCustomerUserInformation($job, $customer)
    {
        $customerUserResults = [];
        $customerUser = User::where('id', '=', $job->customer_id)->get()->first();
        array_push($customerUserResults, [
            "id" => $customerUser->id,
            "name" => $customerUser->name,
            "email" => $customerUser->email,
            "photo_url" => $customerUser->photo_url,
            "logo_url" => $customerUser->logo_url,
            "phone" => $customerUser->phone,
            "first_name" => $customerUser->first_name,
            "last_name" => $customerUser->last_name,
            "customer" => $customer
        ]);

        return $customerUserResults[0];
    }

    private function getJobTasks($job)
    {
        $jobTasksResults = [];
        $jobTasks = JobTask::where('job_id', '=', $job->id)->get();

        foreach ($jobTasks as $jobTask) {

            $taskResults = [];
            $task = Task::where('id', '=', $jobTask->id)->get()->first();
            array_push($taskResults, [
                "name" => $task->name,
                "description" => $task->description,
                "fully_qualified_name" => $task->fully_qualified_name,
                "customer_instructions" => $task->customer_instructions,
            ]);

            array_push($jobTasksResults, [
                "id" => $jobTask->id,
                "task_id" => $jobTask->task_id,
                "location_id" => $jobTask->location_id,
                "status" => $jobTask->status,
                "unit_price" => $this->convertToDollars($jobTask->unit_price),
                "cust_final_price" => $this->convertToDollars($jobTask->cust_final_price),
                "start_date" => $jobTask->start_date,
                "declined_message" => $jobTask->declined_message,
                "task" => $taskResults[0]
            ]);
        }

        return $jobTasksResults;
    }

    private function customerJobInformation($job, $location, $contractorUser, $customerUser, $jobTasks = null)
    {
        $result = [];
        array_push($result, [
            "id" => $job->id,
            "location_id" => $job->location_id,
            "job_name" => $job->job_name,
            "status" => $job->status,
            "bid_price" => $this->convertToDollars($job->bid_price),
            "completed_bid_date" => $job->completed_bid_date,
            "agreed_start_date" => $job->agreed_start_date,
            "agreed_end_date" => $job->agreed_end_date,
            "actual_end_date" => $job->actual_end_date,
            "deleted_at" => $job->deleted_at,
            "created_at" => $job->created_at,
            "updated_at" => $job->updated_at,
            "declined_message" => $job->declined_message,
            "paid_with_cash_message" => $job->paid_with_cash_message,
            "location" => $location,
            "contractor" => $contractorUser,
            "customer" => $customerUser,
            "job_tasks" => $jobTasks
        ]);

        return $result[0];
    }

    private function isGeneralContractor($job)
    {
        return Auth::user()->getAuthIdentifier() == $job->contractor_id;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Job $job
     * @return \Illuminate\Http\Response
     */
    public function show(Job $job)
    {

        if ($this->isCustomerBidNotSent($job)) {


            $location = $this->getCustomersJobLocation($job);

            $contractor = $this->getContractorInfoForCustomer($job);

            $contractorUser = $this->getContractorUserInformation($job, $contractor);

            $customer = $this->getCustomerTableInformation($job);

            $customerUser = $this->getCustomerUserInformation($job, $customer);

            return response()->json([
                $this->customerJobInformation($job, $location, $contractorUser, $customerUser)
            ], 200);

        } else if ($this->isCustomerWithSubmittedBid($job)) {

            $location = $this->getCustomersJobLocation($job);

            $contractor = $this->getContractorInfoForCustomer($job);

            $contractorUser = $this->getContractorUserInformation($job, $contractor);

            $customer = $this->getCustomerTableInformation($job);

            $customerUser = $this->getCustomerUserInformation($job, $customer);

            $jobTasks = $this->getJobTasks($job);

            return response()->json([
                $this->customerJobInformation($job, $location, $contractorUser, $customerUser, $jobTasks)
            ], 200);

        } else if ($this->isGeneralContractor($job)) {


            $job->load(
                [
                    'jobTasks.task',
                    'jobTasks.job',
                    'jobTasks.job.customer',
                    'jobTasks.task.contractor',
                    'jobTasks.bidContractorJobTasks.contractor',
                    'jobTasks.bidContractorJobTasks.contractor.contractor',
                    // 'jobTasks.bidContractorJobTasks.contractorSubContractorPreferredPayment',
                    'location',
                    'jobTasks.location',
                    'jobTasks.images',
                    'contractor',
                    'customer' => function ($query) {
                        $query->select('id', 'name');
                    }
                ]
            );

            foreach ($job->jobTasks as $jt) {
                $jt->cust_final_price = $this->convertToDollars($jt->cust_final_price);
                $jt->sub_final_price = $this->convertToDollars($jt->sub_final_price);
                $jt->unit_price = $this->convertToDollars($jt->unit_price);
                $jt->task->proposed_cust_price = $this->convertToDollars($jt->task->proposed_cust_price);
                $jt->task->proposed_sub_price = $this->convertToDollars($jt->task->proposed_sub_price);
            }

            return $job;

        } //        else if ($this->isSubContractor()) {}
        else {
            return response()->json([
                'message' => 'Not Authorized to access this resource/api'
            ], 403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Job $job
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
     * @param \Illuminate\Http\Request $request
     * @param \App\Job $job
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
     * @param \App\Job $job
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

        DB::transaction(function () use ($job, $request, $location_id) {
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


    public function getJobsForCustomer()
    {
        return Auth::user()->jobs()->select([
            'job_name',
            'id',
            'status'
        ])->get();
    }


    /**
     * Get all jobs associated with user
     *
     * @return void
     */
    public function jobs()
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
                        'jobTasks.location',
                        'jobTasks.task',
                        'jobTasks.task.contractor'
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

//            dd(Auth::user()->usertype);
//            $jobs = Auth::user()->jobs();

            $jobs = Auth::user()
                ->jobs()
                ->with(
                    [
                        'jobTasks.task',
                        'jobTasks.task.contractor',
                        'jobTasks.bidContractorJobTasks.contractor',
                        'jobTasks.bidContractorJobTasks.contractor.contractor',
                        // 'jobTasks.bidContractorJobTasks.contractorSubContractorPreferredPayment',
                        'jobTasks.location',
                        'customer:id'
//                        'customer' => function ($query) {
//                            $query->select('id', 'name');
//                        }
                    ]
                )->where('status', '!=', __('job.completed'))->get();

//                $jobs = Job::with([
//                    'jobTasks:id,job_id',
//                    'jobTasks.bidContractorJobTasks:id,job_task_id',
//                    'jobTasks.images:id,job_task_id',
//                    'location.jobTask:id,location_id'])->
//                where('id','=',1)->get()
//                $jobs = Job::with(['jobTasks:id,job_id','jobTasks.bidContractorJobTasks:id,job_task_id','jobTasks.images:id,job_task_id','location.jobTask:id,location_id'])->where('id','=',1)->get()


        }

//        return $jobs;
        return response()->json($jobs, 200);
//        return response()->json(collect($jobs)->toArray(), 200);
    }


    /**
     * Get all jobs associated with user
     *
     * @return void
     */
    public function jobsPage()
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

//            dd(Auth::user()->usertype);
//            $jobs = Auth::user()->jobs();

//            $jobs = Auth::user()->jobs()->select(['id']);

//            dd(Auth::user()->getAuthIdentifier());
            $jobs = Auth::user()->jobs()->get();
//            $jobs = User::with('jobs')->get();

//            $jobs = Auth::user()->jobs()->
//                with('jobTasks:id,status')->get();


//            $jobs = Auth::user()
//                ->jobs()
//                ->with(
//                    [
//                        'jobTasks',
//                        'jobTasks.bidContractorJobTasks',
//                        // 'jobTasks.bidContractorJobTasks.contractorSubContractorPreferredPayment',
//                        'jobTasks.location',
//                        'customer:id'
////                        'customer' => function ($query) {
////                            $query->select('id', 'name');
////                        }
//                    ]
//                )->where('status', '!=', __('job.completed'))->get();
        }

//        return $jobs;
        return response()->json($jobs, 200);
//        return response()->json(collect($jobs)->toArray(), 200);
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

        try {
            $job->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

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

    public function paidWithCashMessage(Request $request)
    {
        $job = Job::find($request->jobId);
        $job->paid_with_cash_message = $request->paidWithCashMessage;

        try {
            $job->save();
            return response()->json([
                'message' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

    }

}
