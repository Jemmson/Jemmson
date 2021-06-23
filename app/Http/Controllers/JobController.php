<?php

namespace App\Http\Controllers;

use App\BidContractorJobTask;
use App\Job;
use App\JobTaskStatus;
use App\Notifications\NotifyCustomerOfArrival;
use App\SubStatus;
use App\TaskImage;
use App\TaskMessage;
use App\User;
use App\Task;
use App\Customer;
use App\Contractor;
use App\JobTask;
use http\Env\Response;
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
use App\Traits\Status;
use App\Location;
use App\JobStatus;
use Illuminate\Validation\ValidationException;


class JobController extends Controller
{

    use ConvertPrices;
    use Status;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->middleware('further.info', ['only' => 'edit']);
    }

    public function onyourway(Request $request)
    {
        $companyName = $request->companyName;
        try {
            $customer = User::find($request->customerId);
            $customer->notify(new NotifyCustomerOfArrival($companyName));
        } catch (\Exception $e) {
            return response()->json([
                'error' => true
            ], 200);
        }
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

    public function updateLocation(Request $request)
    {
//        validate that all required fields exist
        try {
            $this->validate($request, [
                'address_line_1' => 'required',
                'city' => 'required',
                'state' => 'required',
                'zip' => 'required'
            ]);
        } catch (ValidationException $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ]);
        }

//        does the location exist
        $locationExists = self::locationExists($request);
        $job = Job::find($request->jobId);
        $jobId = $request->jobId;
        if ($locationExists) {
            if (!self::jobAlreadyAssociatedToLocation($job->location_id, $locationExists->id)) {
//           if so then update the id of the job to the id of the existing location
                self::updateJobLocation($jobId, $locationExists->id);
                self::updateAllJobTasksLocation($jobId, $locationExists->id);
            }
        } else {
//        if not then create a new location and then update the job with the id of the new location
            $locationId = self::addNewLocation($request);
            self::updateJobLocation($jobId, $locationId);
            self::updateAllJobTasksLocation($jobId, $locationId);
        }

        return response()->json([
            'success' => true
        ]);
    }

    public function updateAllJobTasksLocation($jobId, $locationId)
    {
        $jobTasks = self::getAllJobTasksAssociatedToTheJob($jobId);
        foreach ($jobTasks as $jobTask) {
            $jobTask->location_id = $locationId;
            try {
                $jobTask->save();
            } catch (\Exception $e) {
                Log::error('unable to save loaction Id to job task' . $e->getMessage());
            }
        }
    }

    public function getAllJobTasksAssociatedToTheJob($jobId)
    {
        return JobTask::where('job_id', '=', $jobId)->get();
    }

    public function jobAlreadyAssociatedToLocation($currentJobLocationId, $existingLocationId)
    {
        return $currentJobLocationId == $existingLocationId;
    }

    public function updateJobLocation($jobId, $locationId)
    {
        $job = Job::find($jobId);
        $job->location_id = $locationId;

        try {
            $job->save();
        } catch (\Exception $e) {
            Log::error('jobLocationError ' . $e->getMessage());
        }
    }

    public function addNewLocation($request)
    {
        $address = strtolower($request->address_line_1);
        $address2 = empty($request->address_line_2) ? null : strtolower($request->address_line_2);
        $city = strtolower($request->city);
        $state = strtolower($request->state);
        $zip = self::formatZip(strtolower($request->zip));
        $userId = strtolower($request->userId);
        $newLocation = new Location();
        $addedLocation = $newLocation->addNewLocation($address, $address2, $city, $state, $zip, $userId);
        return $addedLocation->id;
    }

    public function locationExists($request)
    {
        $address = strtolower($request->address_line_1);
        $city = strtolower($request->city);
        $state = strtolower($request->state);
        $zip = self::formatZip(strtolower($request->zip));
        return Location::where('address_line_1', '=', $address)
            ->where('city', '=', $city)
            ->where('state', '=', $state)
            ->where('zip', '=', $zip)
            ->get()->first();
    }

    public function formatZip($zip)
    {
        return rtrim($zip, '-');
    }

    public function getJobs()
    {

        $jobs = Job::where(
            'contractor_id', '=', Auth::user()->getAuthIdentifier())->where('deleted_at', '=', null)
            ->get();

        foreach ($jobs as $job) {
            $job['status'] = $job->jobStatuses()->get();
        }

        $activeJobs = [];

        foreach ($jobs as $job) {
            $status = $job['status'][count($job['status']) - 1]->status;
            if ($status != 'paid') {
                array_push($activeJobs, $job);
            }
        }

        return $activeJobs;
    }

    public function getTasks()
    {
        return JobTask::where('contractor_id', '=', Auth::user()
            ->getAuthIdentifier())
            ->where('deleted_at', '=', null)
            ->where('status', '!=', 'job.approved')->with('task')
            ->get();
    }

    public function getGeneralJobs()
    {
        $invoices = [];

        $invoices['jobs'] = Auth::user()->jobs()->select([
            'id',
            'job_name',
            'completed_bid_date',
            'contractor_id',
            'customer_id',
            'bid_price'
        ])->get();

        foreach ($invoices['jobs'] as $job) {
            $contractor = Contractor::where('user_id', '=', $job->contractor_id)->select([
                'company_name'
            ])->get()->first();
            $job['contractor'] = $contractor;

            $customer = User::where('id', '=', $job->customer_id)->select([
                'name'
            ])->get()->first();
            $job['customer'] = $customer;

            $jobStatuses = $job->jobStatuses()->get();
            $job['statuses'] = $jobStatuses;
        }

        return $invoices;
    }

    public function getSubJobs()
    {
        $invoices = [];

        $invoices['jobTasks'] = JobTask::where('contractor_id', '=', Auth::user()
            ->getAuthIdentifier())
            ->select([
                'id',
                'qty',
                'sub_final_price',
                'job_id'
            ])->get();

        foreach ($invoices['jobTasks'] as $jobTask) {

            $job = Job::find($jobTask->job_id);

            $general = Contractor::where('user_id', '=', $job->contractor_id)->select([
                'company_name'
            ])->get()->first();

            $jobTask['general'] = $general;
            $jobTask['job_name'] = $job->job_name;
            $jobTask['job_id'] = $job->id;

            $customer = User::where('id', '=', $job->customer_id)->select([
                'name'
            ])->get()->first();
            $jobTask['customer'] = $customer;

            $jobTaskStatuses = $jobTask->jobTaskStatuses()->get();
            $jobTask['statuses'] = $jobTaskStatuses;
        }

        return $invoices;
    }

    public function getCustomerJobs()
    {
        $invoices = [];

        $invoices['customerJobs'] = Auth::user()->jobs()->select([
            'id',
            'job_name',
            'contractor_id',
            'customer_id',
            'bid_price'
        ])->get();

        foreach ($invoices['customerJobs'] as $job) {
            $contractor = Contractor::where('user_id', '=', $job->contractor_id)->select([
                'company_name'
            ])->get()->first();
            $job['contractor'] = $contractor;

            $customer = User::where('id', '=', $job->customer_id)->select([
                'name'
            ])->get()->first();
            $job['customer'] = $customer;

            $jobStatuses = $job->jobStatuses()->get();
            $job['statuses'] = $jobStatuses;
        }

        return $invoices;
    }

    /**
     * Invoices
     *
     * @return \Illuminate\Http\Response
     */
    public function getInvoices()
    {

        if (Auth::user()->usertype === 'customer') {
            //        As A Customer
            $customerJobs = $this->getCustomerJobs();
            return response()->json([
                $customerJobs
            ], 200);
        } else {
            //        As A General
            $generalJobs = $this->getGeneralJobs();
            //        As A Sub
            $subJobs = $this->getSubJobs();
            return response()->json([
                $generalJobs,
                $subJobs
            ], 200);
        }

    }

    public function getInvoice(Job $job)
    {

        $user = Auth::user();

        if ($user->usertype == 'customer') {
            return $this->customerInvoice($job);
        } else if ($user->usertype === 'contractor') {
            if ($user->id == $job->contractor_id) {
                return $this->generalInvoice($job);
            } else {
                return $this->subInvoice($job);
            }
        }
    }

    public function customerInvoice($job)
    {
        $invoice = [];
        $invoice['job'] = [
            'bid_price' => $job->bid_price,
            'job_name' => $job->job_name
        ];

        $invoice['job']['location'] = Location::find($job->location_id);
        $invoice['job']['status'] = JobStatus::where('job_id', '=', $job->id)->get();
        $user = Auth::user();
        $invoice['job']['customer'] = [
            "id" => $user->id,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name
        ];

        $invoice['contractor'] = $job->contractor()->select([
            'id',
            'name'
        ])->get()->first();

        $invoice['contractor']['company'] =
            $job->contractor()->get()->first()
                ->contractor()->select([
                    'company_name'
                ])->get()->first();

        $jobTasks = $job->jobTasks()->select([
            'qty',
            'cust_final_price',
            'task_id',
            'contractor_id',
            'unit_price'
        ])->get();

        $invoice['job']['job_tasks'] = $jobTasks;

        foreach ($jobTasks as $jt) {
            $jt['task'] = $jt->task()->select([
                'name'
            ])->get()->first();

            if ($jt->contractor_id != $job->contractor_id) {
                $jt['sub'] = User::where('id', '=', $jt->contractor_id)->select([
                    'name'
                ])->get()->first();
                $jt['sub']['company'] = Contractor::where('user_id', '=', $jt->contractor_id)
                    ->select(['company_name'])->get()->first();
            } else {
                $jt['general'] = User::where('id', '=', $jt->contractor_id)->select([
                    'id',
                    'name'
                ])->get()->first();
                $jt['general']['company'] = Contractor::where('user_id', '=', $jt->contractor_id)
                    ->select(['company_name'])->get()->first();
            }
        }

        $invoice['user'] = 'customer';

        return $invoice;
    }

    public function generalInvoice($job)
    {
        $invoice = [];
        $invoice['job'] = [
            'bid_price' => $job->bid_price,
            'job_name' => $job->job_name
        ];


        $invoice['job']['location'] = Location::find($job->location_id);
        $invoice['job']['status'] = JobStatus::where('job_id', '=', $job->id)->get();
        $customer = User::find($job->customer_id);
        $invoice['job']['customer'] = [
            "id" => $customer->id,
            "first_name" => $customer->first_name,
            "last_name" => $customer->last_name
        ];

        $invoice['contractor'] = $job->contractor()->select([
            'id',
            'name'
        ])->get()->first();
        $invoice['contractor']['company'] =
            $job->contractor()->get()->first()
                ->contractor()->select([
                    'company_name'
                ])->get()->first();

        $jobTasks = $job->jobTasks()->select([
            'qty',
            'sub_final_price',
            'cust_final_price',
            'task_id',
            'contractor_id',
            'unit_price'
        ])->get();

        $invoice['job']['job_tasks'] = $jobTasks;

        foreach ($jobTasks as $jt) {
            $jt['task'] = $jt->task()->select([
                'name'
            ])->get()->first();

            if ($jt->contractor_id != $job->contractor_id) {
                $jt['sub'] = User::where('id', '=', $jt->contractor_id)->select([
                    'id',
                    'name'
                ])->get()->first();
                $jt['sub']['company'] = Contractor::where('user_id', '=', $jt->contractor_id)
                    ->select(['company_name'])->get()->first();
            } else {
                $jt['general'] = User::where('id', '=', $jt->contractor_id)->select([
                    'id',
                    'name'
                ])->get()->first();
                $jt['general']['company'] = Contractor::where('user_id', '=', $jt->contractor_id)
                    ->select(['company_name'])->get()->first();
            }
        }

        $invoice['user'] = 'general';

        return $invoice;
    }

    public function subInvoice($job)
    {

        $invoice = [];

        $invoice['job'] = [
            'job_name' => $job->job_name
        ];

        $invoice['job']['location'] = Location::find($job->location_id);
        $invoice['job']['status'] = JobStatus::where('job_id', '=', $job->id)->get();
        $customer = User::find($job->customer_id);
        $invoice['job']['customer'] = [
            "id" => $customer->id,
            "first_name" => $customer->first_name,
            "last_name" => $customer->last_name
        ];
        $invoice['contractor'] = $job->contractor()->select([
            'id',
            'name'
        ])->get()->first();
        $invoice['contractor']['company'] =
            $job->contractor()->get()->first()
                ->contractor()->select([
                    'company_name'
                ])->get()->first();

        $jobTasks = $job->jobTasks()->select([
            'qty',
            'sub_final_price',
            'task_id',
            'contractor_id'
        ])->get();

        $invoice['job']['job_tasks'] = $jobTasks;

        foreach ($jobTasks as $jt) {
            $jt['task'] = $jt->task()->select([
                'name'
            ])->get()->first();

            if ($jt->contractor_id != $job->contractor_id) {
                $jt['sub'] = User::where('id', '=', $jt->contractor_id)->select([
                    'name',
                    'id'
                ])->get()->first();
                $jt['sub']['company'] = Contractor::where('user_id', '=', $jt->contractor_id)
                    ->select(['company_name'])->get()->first();
            } else {
                $jt['general'] = User::where('id', '=', $jt->contractor_id)->select([
                    'id',
                    'name'
                ])->get()->first();
                $jt['general']['company'] = Contractor::where('user_id', '=', $jt->contractor_id)
                    ->select(['company_name'])->get()->first();
            }
        }

        $invoice['user'] = 'sub';

        return $invoice;
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
            ($job->status != 'bid.sent' && $job->status != 'job.approved' && $job->status != 'job.completed');
    }

    private function isCustomerWithSubmittedBid($job)
    {
        return Auth::user()->id == $job->customer_id &&
            ($job->status == 'bid.sent' || $job->status == 'job.approved' || $job->status == 'job.completed');
    }

    private function getCustomersJobLocation($job)
    {
        $locationResults = [];
        $location = Location::where('id', '=', $job->location_id)->get()->first();
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
            "stripe_id" => !empty($contractorUser->customer_stripe_id),
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
        if (!empty($customer)) {
            array_push($customerResults, [
                "notes" => $customer->notes
            ]);
            return $customerResults[0];
        } else {
            return null;
        }


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

        $messages = $job->jobStatuses()->get();
        $job['messages'] = $messages;

        foreach ($jobTasks as $jobTask) {

            $taskResults = [];
            $task = Task::where('id', '=', $jobTask->task()->get()->first()->id)->get()->first();

            array_push($taskResults, [
                "name" => $task->name,
                "description" => $task->description,
                "fully_qualified_name" => $task->fully_qualified_name,
                "customer_instructions" => $task->customer_instructions,
            ]);

            $customerUser = User::where('id', '=', $job->customer_id)->get()->first();

            $customer = Customer::where('user_id', '=', $job->customer_id)->get()->first();

            $customerUserResults = [];
            $customerResults = [];

            array_push($customerResults, [
                "id" => $customer->id,
                "user_id" => $customer->user_id,
                "location_id" => $customer->location_id,
                "email_method_of_contact" => $customer->email_method_of_contact,
                "phone_method_of_contact" => $customer->phone_method_of_contact,
                "sms_method_of_contact" => $customer->sms_method_of_contact,
                "notes" => $customer->notes,
                "deleted_at" => $customer->deleted_at,
                "created_at" => $customer->created_at,
                "updated_at" => $customer->updated_at

            ]);
            array_push($customerUserResults, [
                "id" => $customerUser->id,
                "location_id" => $customerUser->location_id,
                "name" => $customerUser->name,
                "email" => $customerUser->email,
                "usertype" => $customerUser->usertype,
                "password_updated" => $customerUser->password_updated,
                "photo_url" => $customerUser->photo_url,
                "logo_url" => $customerUser->logo_url,
                "uses_two_factor_auth" => $customerUser->uses_two_factor_auth,
                "phone" => $customerUser->phone,
                "two_factor_reset_code" => $customerUser->two_factor_reset_code,
                "current_team_id" => $customerUser->current_team_id,
                "stripe_id" => $customerUser->customer_stripe_id,
                "current_billing_plan" => $customerUser->current_billing_plan,
                "billing_state" => $customerUser->billing_state,
                "trial_ends_at" => $customerUser->trial_ends_at,
                "last_read_announcements_at" => $customerUser->last_read_announcements_at,
                "deleted_at" => $customerUser->deleted_at,
                "created_at" => $customerUser->created_at,
                "updated_at" => $customerUser->updated_at,
                "first_name" => $customerUser->first_name,
                "last_name" => $customerUser->last_name,
                "customer" => $customerUser->customer,
                "tax_rate" => $customerUser->tax_rate
            ]);

            $location = Location::where('id', '=', $jobTask->location_id)->get()->first();

            $contractorResults = [];
            $contractor = Contractor::where('user_id', '=', $jobTask->contractor_id)->get()->first();
            $contractorUser = User::where('id', '=', $jobTask->contractor_id)->get()->first();
            array_push($contractorResults, [
                "id" => $contractorUser->id,
                "company_name" => $contractor->company_name,
                "first_name" => $contractorUser->first_name,
                "last_name" => $contractorUser->last_name,
                "phone" => $contractorUser->phone,
            ]);


            $generalResults = [];
            $general = Contractor::where('user_id', '=', $task->contractor_id)->get()->first();
            $generalUser = User::where('id', '=', $task->contractor_id)->get()->first();
            array_push($generalResults, [
                "id" => $generalUser->id,
                "company_name" => $general->company_name,
                "first_name" => $generalUser->first_name,
                "last_name" => $generalUser->last_name,
                "phone" => $generalUser->phone,
            ]);

            $jobTaskMessages = TaskMessage::where('job_task_id', '=', $jobTask->id)->get();

            $jts = JobTaskStatus::where('job_task_id', '=', $jobTask->id)->get();
            $ss = SubStatus::where('job_task_id', '=', $jobTask->id)->get();
            array_push($jobTasksResults, [
                "id" => $jobTask->id,
                "task_id" => $jobTask->task_id,
                "qty" => $jobTask->qty,
                "location_id" => $jobTask->location_id,
                "status" => $jobTask->status,
                "unit_price" => $this->convertToDollars($jobTask->unit_price),
                "cust_final_price" => $this->convertToDollars($jobTask->cust_final_price),
                "start_date" => $jobTask->start_date,
                "declined_message" => $jobTask->declined_message,
                "customer_message" => $jobTask->customer_message,
                "location" => $location,
                "customer" => $customerUserResults[0],
                "task" => $taskResults[0],
                "job_task_status" => $jts,
                "sub_status" => $ss,
                "messages" => $jobTaskMessages,
                "contractor" => $contractorResults[0],
                "general" => $generalResults[0],
                "job" => [
                    "id" => $job->id,
                    "job_statuses" => $messages
                ]
            ]);

        }

        return $jobTasksResults;
    }

    private
    function customerJobInformation($job, $location, $contractorUser, $customerUser, $images, $jobTasks = [])
    {
        $jt = JobStatus::where('job_id', '=', $job->id)->get();

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
            "payment_type" => $job->payment_type,
            "location" => $location,
            "images" => $images,
            "contractor" => $contractorUser,
            "customer" => $customerUser,
            "job_tasks" => $jobTasks,
            "job_status" => $jt
        ]);

        return $result[0];
    }

    private
    function isGeneralContractor($job)
    {
        return Auth::user()->getAuthIdentifier() == $job->contractor_id;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Job $job
     * @return \Illuminate\Http\Response
     */
    public
    function show(Job $job)
    {

        if ($this->isCustomerBidNotSent($job)) {

            $images = TaskImage::where('job_id', '=', $job->id)->get();

            $location = $this->getCustomersJobLocation($job);

            $contractor = $this->getContractorInfoForCustomer($job);

            $contractorUser = $this->getContractorUserInformation($job, $contractor);

            $customer = $this->getCustomerTableInformation($job);

            $customerUser = $this->getCustomerUserInformation($job, $customer);

            return response()->json([
                $this->customerJobInformation($job, $location, $contractorUser, $customerUser, $images)
            ], 200);

        } else if ($this->isCustomerWithSubmittedBid($job)) {

            $images = TaskImage::where('job_id', '=', $job->id)->get();

            $location = $this->getCustomersJobLocation($job);

            $contractor = $this->getContractorInfoForCustomer($job);

            $contractorUser = $this->getContractorUserInformation($job, $contractor);

            $customer = $this->getCustomerTableInformation($job);

            $customerUser = $this->getCustomerUserInformation($job, $customer);

            $jobTasks = $this->getJobTasks($job);

            return response()->json([
                $this->customerJobInformation($job, $location, $contractorUser, $customerUser, $images, $jobTasks)
            ], 200);

        } else if ($this->isGeneralContractor($job)) {
            $job->load(
                [
                    'jobTasks.task',
                    'jobTasks.job',
                    'jobTasks.job.jobStatuses',
                    'jobTasks.job.customer',
                    'jobTasks.jobTaskStatuses',
                    'jobTasks.subStatuses',
                    'jobTasks.taskMessages',
                    'jobTasks.task.contractor',
                    'jobTasks.bidContractorJobTasks',
                    'jobTasks.bidContractorJobTasks.contractor',
                    'jobTasks.bidContractorJobTasks.contractor.contractor',
                    'location',
                    'images',
                    'jobTasks.location',
                    'jobTasks.images',
                    'jobStatuses',
                    'contractor',
                    'customer' => function ($query) {
                        $query->select('id', 'name');
                    }
                ]
            );

            $job->bid_price = $this->convertToDollars($job->bid_price);

            foreach ($job->jobTasks as $jt) {

                $contractorResults = [];
                $contractor = Contractor::where('user_id', '=', $jt->contractor_id)->get()->first();
                $contractorUser = User::where('id', '=', $jt->contractor_id)->get()->first();
                array_push($contractorResults, [
                    "company_name" => $contractor->company_name,
                    "first_name" => $contractorUser->first_name,
                    "last_name" => $contractorUser->last_name,
                    "phone" => $contractorUser->phone,
                ]);
                $jt->cust_final_price = $this->convertToDollars($jt->cust_final_price);
                $jt->sub_final_price = $this->convertToDollars($jt->sub_final_price);
                $jt->unit_price = $this->convertToDollars($jt->unit_price);
                $jt->task->proposed_cust_price = $this->convertToDollars($jt->task->proposed_cust_price);
                $jt->task->proposed_sub_price = $this->convertToDollars($jt->task->proposed_sub_price);
                $jt->contractor = $contractorResults[0];

                foreach ($jt->bidContractorJobTasks as $bcjt) {
                    $bcjt->bid_price = $this->convertToDollars($bcjt->bid_price);
                }

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
    public
    function edit(Job $job)
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

    public
    function updateJobDate(Request $request)
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
    public
    function update(Request $request, Job $job)
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
    public function destroy(Request $request)
    {

//        need to delete any bidcontrcontractorrecords
//        need to delete any job tasks


        $job = Job::find($request->id);

        $this->setCancelJobStatuses($job, 'canceled_by_general');

//        are there any tasks associated to the job
        $jobTasks = JobTask::where('job_id', '=', $request->id)->get();

        if (!empty($jobTasks)) {
            foreach ($jobTasks as $jt) {
//                are there any tasks being bid on
                $bcjt = BidContractorJobTask::where('job_task_id', '=', $jt->id)->get();
                if (!empty($bcjt)) {
                    foreach ($bcjt as $subBid) {
                        try {
                            $subBid->delete();
                        } catch (\Exception $e) {
                            return response()->json([
                                'message' => $e->getMessage(),
                                'code' => $e->getCode()
                            ], 200);
                        }
                    }
                }

                try {
                    $jt->delete();
                } catch (\Exception $e) {
                    return response()->json([
                        'message' => $e->getMessage(),
                        'code' => $e->getCode()
                    ], 200);
                }
            }
        }

        $job->delete();

//        $user = Auth::user();

//        $user->notify(new CustomerUnableToSendPaymentWithStripe());


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

        $address = [
            "addressLine1" => $request->address_line_1,
            "addressLine2" => $request->address_line_2,
            "city" => $request->city,
            "state" => $request->state,
            "zip" => $request->zip
        ];

        $job->approveJob(
            $address,
            $request->agreed_start_date,
            Auth::user()->customer()->first()->location_id,
            $request->job_location_same_as_home
        );

        //TODO: need to charge the general 1 dollar for the job if the job is a cash job
        // will use cashier for this
        // will need to be a stripe customer to charge for a cash job
        // will need to have been signed up for the site
        // if using free jobs then the dollar will not apply

        $contractor = Contractor::find($job->contractor_id)->first();
        $contractorUser = User::find($job->contractor_id)->first();

        if ($contractor->free_jobs === 0 && !\is_null($contractorUser->stripe_id)) {
            if ($job->payment_type == 'cash') {
                $contractorUser->invoiceFor($job->job_name, 100);
            }
        }


//        // TODO: what date needs to be updated here?
//        $job->agreed_start_date = $request->agreed_start_date;
//        $job->status = __('job.approved');
//
//        $location_id = Auth::user()->customer()->first()->location_id;
//
//        DB::transaction(function () use ($job, $request, $location_id) {
//            if ($request->job_location_same_as_home) {
//                $job->location_id = $location_id;
//            } else {
//                $job->newLocation($request);
//            }
//            $job->save();
//            // approve all tasks associated with this job, any exceptions?
//            JobTask::where('job_id', $job->id)
//                //->where('bid_id', '!=', 'NULL') // update unless no bid connected to the job task
//                ->update(['status' => __('bid_task.approved_by_customer')]);
//            JobTask::where('job_id', $job->id)
//                ->where('start_when_accepted', true)
//                ->update(['start_date' => Carbon::now()]);
//        });

        $this->notifyAll($job);

        $this->setJobTasksAndSubStatuses($job, 'approved_by_customer');

        $this->setJobStatus($job->id, 'approved');


        return response()->json($job, 200);
    }

    public function getLatestJobNumber()
    {
        $year = $this->getYear();

        $latest = Job::withTrashed()->where('contractor_id', '=', Auth::user()->getAuthIdentifier())
            ->where('created_at', 'LIKE', "$year%")->select('id')
            ->get()->count();

        return response()->json([
            'latest' => $latest
        ]);
    }

    public function getYear()
    {
        $c = Carbon::now();
        return $c->year;
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


    public function saveJobName(Request $request)
    {
        $job = Job::find($request->jobId);
        $job->job_name = $request->jobName;
        $job->save();

        return response()->json([
            'jobName' => $job->job_name
        ], 200);

    }

    public
    function getJobsForCustomer()
    {
        $jobs = Auth::user()->jobs()->select([
            'job_name',
            'id',
        ])->get();

        foreach ($jobs as $j) {
            $j['job_statuses'] = $j->jobStatuses()->get();
        }

        return $jobs;
    }

    private function getJobStatus($job, $status)
    {
        return $job->jobStatuses()->select('status') == $status;
    }

    /**
     * Get all jobs associated with user
     *
     * @return void
     */
    public
    function jobs()
    {
        // load jobs and all their tasks along with those tasks relationships

        if ($this->isCustomer()) {
            // only load tasks on jobs that are approved or need approval

            $jobs = Job::where('customer_id', '=', Auth::user()->getAuthIdentifier())->get();

            foreach ($jobs as $j) {
                if (
                    $this->getJobStatus($j, 'approved')
                    || $this->getJobStatus($j, 'sent')
                    || $this->getJobStatus($j, 'declines_finished_task')
                    || $this->getJobStatus($j, 'paid')
                    || $this->getJobStatus($j, 'changed')
                ) {
                    $j['job_tasks'] = $j->job_tasks()->get();
                    foreach ($j['job_tasks'] as $jt) {
                        $jt['job_task_status'] = JobTaskStatus::where('job_task_id', '=', $jt->id)->get();
                        $jt['sub_status'] = SubStatus::where('job_task_id', '=', $jt->id)->get();
                        $jt['messages'] = $jt->taskMessages()->get();
                    }
                    $j['job_statuses'] = $j->jobStatuses()->get();
                } else {
                    $j['job_statuses'] = $j->jobStatuses()->get();
                }
            }
        } else {

            $jobs = Job::where('contractor_id', '=', Auth::user()
                ->getAuthIdentifier())
                ->select([
                    'status',
                    'job_name',
                    'payment_type',
                    'id'
                ])->get();

            foreach ($jobs as $j) {
                $j['job_statuses'] = $j->jobStatuses()->get();
                $j['job_tasks'] = $j->jobTasks()->select(['id'])->get();
                $j['job_tasks_length'] = count($j['job_tasks']);
                foreach ($j['job_tasks'] as $jt) {
                    $j['job_tasks']['messages'] = $jt->taskMessages()->get();
                    $j['job_tasks']['bid_contractor_job_tasks'] =
                        $jt->bidContractorJobTasks()->select('contractor_id')->get();
                }
            }
        }

        return response()->json($jobs, 200);
    }


    /**
     * Get all jobs associated with user
     *
     * @return void
     */
    public
    function jobsPage()
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
        $customer = User::find($job->customer_id);

        if ($job->updateStatus(__('bid.declined'))) {
            $contractor->notify(new JobBidDeclined($job, $contractor, $message, $customer));
            $job->setJobDeclinedMessage($message);
            $this->setJobStatus($job->id, 'changed');

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
    public
    function acceptJob(Request $request)
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
    public
    function declineJob(Request $request)
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
    public
    function finishedBidNotification(Request $request)
    {
        $jobId = $request->jobId;
        $customerId = $request->customerId;


        $user = User::find($customerId);
        $job = Job::find($jobId);

        $this->switchJobStatusToInProgress($job, __('bid.sent'));
        $companyName = $job->contractor()->get()->first()->contractor->company_name;

        $user->notify(new NotifyCustomerThatBidIsFinished($job, $user, $companyName));

        $this->setJobTasksAndSubStatuses($job, 'waiting_for_customer_approval');

        $this->setJobStatus($job->id, 'sent');

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

        $general = User::find($job->contractor_id);
        $contractors = JobTask::where('job_id', '=', $job->id)->get()->pluck('contractor_id');
        $customer = User::find($job->customer_id);

        $general->notify(new JobCanceled($job->job_name));
        $customer->notify(new JobCanceled($job->job_name));

        foreach ($contractors as $subId) {
            if ($general->id != $subId) {
                $sub = User::find($subId);
                $sub->notify(new JobCanceled($job->job_name));
            }
        }

        try {
            $job->delete();
        } catch (\Exception $e) {
            Log::error('Updating Job Status: ' . $e->getMessage());
            return response()->json(['message' => "Couldn't cancel job, please try again."], 400);
            return false;
        }

        $this->setCancelJobStatuses($job, 'canceled_by_customer');

        return response()->json(['message' => 'Success'], 200);
    }

    /**
     * Put the job as completed
     *
     * @param Request $request
     * @return boolean
     */
    public
    function jobCompleted(Request $request)
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

    private
    function isCustomer()
    {
        return Auth::user()->usertype === 'customer';
    }

    public
    function switchJobStatusToInProgress($job, $message)
    {
        $job->status = $message;
        $job->save();
    }

    public
    function paidWithCashMessage(Request $request)
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

    public function jobImages($id)
    {
        $job = Job::find($id);
        $jobImages = $job->images()->get();
        return response()->json([
            "images" => $jobImages
        ], 200);
    }

}