<?php

namespace App\Http\Controllers;

use App\ContractorSubcontractorPreferredPayment;
use App\JobStatus;
use App\Notifications\NotifyContractorThatCustomerChangesBid;
use App\Notifications\NotifySubOfTaskToBid;
use App\Notifications\NotifySubOfAcceptedBid;
use App\Notifications\NotifySubOfBidNotAcceptedBid;
use App\Notifications\NotifyContractorOfAcceptedBid;
use App\Notifications\NotifyContractorOfSubBid;
use App\Notifications\TaskFinished;
use App\Notifications\TaskWasNotApproved;
use App\Notifications\TaskApproved;
use App\Notifications\TaskReopened;
use App\Notifications\UploadedTaskImage;
use App\Notifications\TaskImageDeleted;
use App\Notifications\NotifyCustomerOfUpdatedMessage;
use App\Notifications\NotifySubOfUpdatedMessage;
use App\Quickbook;
use App\QuickbooksContractor;
use App\Location;
use App\QuickbooksItem;
use App\SubStatus;
use App\Task;
use App\JobTaskStatus;
use App\Job;
use App\Contractor;
use App\Customer;
use App\User;
use App\ContractorCustomer;
use App\BidContractorJobTask;
use App\JobTask;
use App\TaskMessage;
use http\Message;
use Illuminate\Support\Facades\Log;
use App\TaskImage;
use Illuminate\Http\Request;
use App\Services\RandomPasswordService;
use App\Services\SanatizeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use App\Traits\ConvertPrices;
use App\Traits\Status;
use Cloudinary;
use Cloudinary\Api;

class TaskController extends Controller
{

    use ConvertPrices;
    use Status;

    /**
     * Display a listing of the resource.
     * NOTICE:
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    public function bidContractorJobTasks()
    {

        $bidTasks = [];
        $bcjtasks = Auth::user()->contractor()->first()->bidContractorJobTasks()->where()->get();
        array_push($bidTasks, $bcjtasks);

        foreach ($bidTasks as $bcjtask) {
            $bcjtask['job_task'] = $bcjtask->jobTask()->select([
                'id',
                'location_id',
                'task_id',
                'job_id',
                'status',
                'contractor_id',
                'payment_type',
                'bid_id',
                'declined_message',
                'sub_message',
                'start_date',
            ]);
            $bcjtask['location'] = $bcjtask->jobTask()->get()->first()->location()->select([
                'id',
                'address_line_1',
                'address_line_2',
                'city',
                'state',
                'zip'
            ])->get()->first();
            $bcjtask['task'] = $bcjtask->jobTask()->get()->first()->task()->select([
                'id',
                'name'
            ])->get()->first();
            $bcjtask['job'] = $bcjtask->jobTask()->get()->first()->task()->select([
                'id',
                'job_name',
                'sub_status',
                'status',
                'contractor_id'
            ]);
            $bcjtask['job_task_status'] = $bcjtask->jobTask()->get()->first()->jobTaskStatuses()->get();
            $bcjtask['sub_status'] = $bcjtask->jobTask()->get()->first()->subStatuses()->get();
        }


//        $bidTasks = Auth::user()->contractor()->first()->bidContractorJobTasks()->
//        with([
//            'jobTask.job',
//            'jobTask.task',
//            'jobTask.jobTaskStatuses',
//            'jobTask.subStatuses',
//            'jobTask.task.contractor'
//        ])->get();
        return view('tasks.index')->with(['tasks' => $bidTasks]);
    }

    private function getBidContractorJobTasks()
    {
        $bcjtasks = Auth::user()->contractor()->first()->bidContractorJobTasks()->get();

        foreach ($bcjtasks as $bcjtask) {

            if (!\is_null($bcjtask->jobTask()->first())) {
                $bcjtask['job_task'] = $bcjtask->jobTask()->select([
                    'id',
                    'job_id',
                    'location_id',
                    'task_id',
                    'bid_id',
                    'contractor_id',
                    'declined_message',
                    'qty',
                    'status',
                    'start_date',
                    'sub_final_price',
                    'sub_message',
                    'unit_price'
                ])->get()->first();


                $bcjtask['job_task']['job'] = $bcjtask->jobTask()->get()->first()->job()->select([
                    'id',
                    'contractor_id',
                    'job_name',
                    'payment_type',
                    'status'
                ])->get()->first();

                $bcjtask['job_task']['job']['job_task_status'] =
                    $bcjtask->jobTask()->get()->first()->jobTaskStatuses()->get();

                $bcjtask['job_task']['job']['sub_status'] =
                    $bcjtask->jobTask()->get()->first()->subStatuses()->orderBy('updated_at', 'asc')->where('user_id', '=', Auth::user()->getAuthIdentifier())->get();

                $bcjtask['job_task']['task'] = $bcjtask->jobTask()->get()->first()->task()->select([
                    'id',
                    'proposed_cust_price',
                    'proposed_sub_price',
                    'name'
                ])->get()->first();

                $bcjtask['job_task']['task']['contractor'] = $bcjtask->jobTask()->get()->first()
                    ->task()->get()->first()
                    ->contractor()->select([
                        'id',
                        'user_id',
                        'location_id',
                        'company_name',
                        'company_name',
                        'company_logo_name',
                        'company_name',
                        'email_method_of_contact',
                        'sms_method_of_contact',
                        'phone_method_of_contact'
                    ])->get()->first();

                $bcjtask['job_task']['images'] = $bcjtask->jobTask()->get()->first()->images()->get();

                $bcjtask['job_task']['location'] = $bcjtask->jobTask()->get()->first()->location()->select([
                    'id',
                    'address_line_1',
                    'address_line_2',
                    'city',
                    'state',
                    'zip'
                ])->get()->first();
            }

        }

        return $bcjtasks;

    }

    /**
     * Get all bid tasks from the currently logged in contractor
     *
     * @return void
     */
    public function bidTasks()
    {
        $bidTasks = $this->getBidContractorJobTasks();
        if (!empty($bidTasks) && \sizeof($bidTasks) > 0) {
            foreach ($bidTasks[0] as $bt) {
                if (!empty($bt->job_task)) {
                    $bt->job_task->sub_final_price = $this->convertToDollars($bt->job_task->sub_final_price);
                    $bt->job_task->unit_price = $this->convertToDollars($bt->job_task->unit_price);
                }
                if (!empty($bt->job)) {
                    $bt->job->unit_price = $this->convertToDollars($bt->job->unit_price);
                }
                if (!empty($bt->task)) {
                    $bt->task->proposed_cust_price = $this->convertToDollars($bt->task->proposed_cust_price);
                    $bt->task->proposed_sub_price = $this->convertToDollars($bt->task->proposed_sub_price);
                }
            }
            return response()->json($bidTasks, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function getJobTask($jobTaskId)
    {
        Log::debug("Job Task Id: $jobTaskId");
        $jobTask = JobTask::find($jobTaskId);
        return $jobTask->load(['images', 'task']);

    }

    private function userIsAContractor($contractor_id)
    {
        return Auth::user()->getAuthIdentifier() == $contractor_id;
    }

    private function userIsACustomer($customer_id)
    {
        return Auth::user()->getAuthIdentifier() == $customer_id;
    }

    private function userIsASubContractor($contractor_id)
    {
        return Auth::user()->getAuthIdentifier() != $contractor_id;
    }

    private function deleteSubContractorTasks($jobTaskId)
    {
        $jobTasks = BidContractorJobTask::where('job_task_id', '=', $jobTaskId);

        foreach ($jobTasks as $jt) {
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

    private function deleteSubsBid($contractorId, $jobTaskId)
    {
        $subTask = BidContractorJobTask::where('job_task_id', '=', $jobTaskId)->
        where('contractor_id', '=', $contractorId)->get()->first();
        try {
            $subTask->delete();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }
    }

    public function deleteJobTask(Request $request)
    {


//        as contractor
//        1. remove from job
//        2, if there are subs remove bid contractor table
//        3. update job totals

        $jobTask = JobTask::find($request->id);

        $contractor_id = $jobTask->job()->get()->first()->contractor_id;
        $customer_id = $jobTask->job()->get()->first()->customer_id;
        $job_id = $jobTask->job()->get()->first()->id;

        $this->setSubStatus(Auth::user()->getAuthIdentifier(), $jobTask->id, 'canceled_bid_task');

        if ($this->userIsAContractor($contractor_id) || $this->userIsACustomer($customer_id)) {
            $this->deleteSubContractorTasks($jobTask->id);
            try {
                $jobTask->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ], 200);
            }
        } else if ($this->userIsASubContractor($contractor_id)) {
            $sub_contractor_id = Auth::user()->getAuthIdentifier();
            $this->deleteSubsBid($sub_contractor_id, $request->id);
        }

        $this->updateJobPrice($job_id);


//

//        as a subcontractor
//        1. remove from bidcontractor table
//        2. contractor should be notified that sub does not want the job


//        as a customer
//        1. remove from job
//        2, if there are subs remove bid contractor table
//        3. update job totals


    }

    public function updateJobPrice($job_id)
    {
        $jobTasks = JobTask::where('job_id', '=', $job_id)->get();
        $totalPrice = 0;

        foreach ($jobTasks as $jobTask) {
            $totalPrice = $totalPrice + $jobTask->cust_final_price;
        }

        $job = Job::find($job_id);
        $job->bid_price = $totalPrice;

        try {
            $job->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

    }

    public function getJobTaskForGeneral($jobTaskId, $userId)
    {

        $jobTasks = JobTask::with(
            [
                'task',
                'images',
                'location',
                'subStatuses',
                'job',
                'job.jobStatuses',
                'taskMessages',
                'job.customer',
                'jobTaskStatuses',
                'bidContractorJobTasks',
                'bidContractorJobTasks.contractor',
                'bidContractorJobTasks.contractor.contractor'
            ])->where('id', '=', $jobTaskId)->get();
        foreach ($jobTasks as $jt) {
            $jt->cust_final_price = $this->convertToDollars($jt->cust_final_price);
            $jt->sub_final_price = $this->convertToDollars($jt->sub_final_price);
            $jt->unit_price = $this->convertToDollars($jt->unit_price);
            foreach ($jt->bidContractorJobTasks as $bidContractorJobTask) {
                $bidContractorJobTask->bid_price = $this->convertToDollars($bidContractorJobTask->bid_price);
            }
        }
        return $jobTasks;
    }

    public function show(Request $request)
    {
        $this->validateRequest($request, [
            'message' => 'required|string',
            'jobTaskId' => 'required|numeric',
            'actor' => 'required|string'
        ]);

        $message = $request->message;
        $jobTaskId = $request->jobTaskId;
        $actor = $request->actor;

        $jobTask = JobTask::find($jobTaskId);
        $job = Job::find($jobTask->job_id);
        $customer = Customer::find($job->customer_id);

        // are there subs for this task?

        //has the job been accepted
        // check if the contractor_id on the jobtask table equals the contractor_id of the jobs table


        if ($actor == 'sub') {
            $b = new BidContractorJobTask();
            $contractors = $b->select('contractor_id')->where('job_task_id', '=', $jobTask->job_id)->get();
            Log::debug("actor: $actor");
            Log::debug("contractors: $contractors");
            $jobTask->sub_message = $message;
            Log::debug("job->contractor_id: $job->contractor_id");
            Log::debug("jobTask->contractor_id: $jobTask->contractor_id");
            Log::debug("jobTask->status: $jobTask->status");

            // checks if the job has been accepted then it sends the notification to just that contractor
            // if the job has not been accepted then the notification gets sent to all subs
            // who have been triggered to bid on the job
            if ($job->contractor_id != $jobTask->contractor_id && $jobTask->status == 'bid_task.accepted') {
                Log::debug("A single contractor has been called");
                Log::debug("jobTask->contractor_id: $jobTask->contractor_id");
                $contractor = User::find($jobTask->contractor_id);
                $contractor->notify(new NotifySubOfUpdatedMessage);
            } else if (!empty($contractors)) {
                Log::debug("multiple contractors are being called");
                Log::debug("contractors: $contractors");
                foreach ($contractors as $c) {
                    Log::debug("jobTask->contractor_id: $jobTask->contractor_id");
                    $contractor = User::find($c->contractor_id);
                    $contractor->notify(new NotifySubOfUpdatedMessage);
                }
            }
        } else {
            Log::debug("actor: $actor");
            $customer = User::find($job->customer_id);
            $jobTask->customer_message = $message;
            $customer->notify(new NotifyCustomerOfUpdatedMessage);
        }

        try {
            $jobTask->save();
        } catch (\Excpetion $e) {
            Log::error('Updating JobTask: ' . $e->getMessage);
            return 'Updating JobTask: ' . $e->getMessage;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {

        try {
            $task->save();
        } catch (\Exception $e) {
            Log::error('Update Task:' . $e->getMessage());
        }
    }

    /**
     * Update jobtask location
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function updateTaskLocation(Request $request)
    {
        $jobTask = JobTask::find($request->id);
        return $jobTask->updateLocation($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function toggleStripe(Request $request)
    {
        // remove the task from the job
        $jobTask = JobTask::find($request->id);

        if ($jobTask->toggleStripe()) {
            return response()->json(["message" => "Success"], 200);
        }

        return response()->json(["message" => "Price needs to be greater than 0.", "errors" => ["error" => [""]]], 412);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\BidContractorJobTask $task
     * @return \Illuminate\Http\Response
     */
    public function updateBidContractorJobTask(Request $request)
    {

        $sub = User::find($request->subId);

        $jobTask = JobTask::find($request->job_task_id);
        $job = Job::find($jobTask->job_id);

        $sub->subSendsBidToGeneral(
            $request->bid_price,
            $request->paymentType,
            $request->generalId,
            $jobTask,
            $request->subId,
            $job,
            $request->start_date
        );

//        $bidContractorJobTask = BidContractorJobTask::find($request->id);
//
//        if ($bidContractorJobTask == null) {
//            return response()->json(["message" => "Couldn't find record.", "errors" => ["error" => ["Couldn't find record."]]], 404);
//        } else if ($request->bid_price == 0) {
//            return response()->json(["message" => "Price needs to be greater than 0.", "errors" => ["error" => [""]]], 412);
//        }
//
//        $bidContractorJobTask->bid_price = $this->convertToCents($request->bid_price);
//        $bidContractorJobTask->status = 'bid_task.bid_sent';
//        $bidContractorJobTask->payment_type = $request->paymentType;
//        $jobTask = $bidContractorJobTask->jobTask()->first();
//
//        try {
//            $bidContractorJobTask->save();
//        } catch (\Exception $e) {
//            Log::error('Update Bid Task:' . $e->getMessage());
//            return response()->json(["message" => "Couldn't save record.", "errors" => ["error" => [$e->getMessage()]]], 404);
//        }
//
//        $jobTask->updateStatus(__('bid_task.bid_sent'));
//
//        $gContractor = User::find($jobTask->task()->first()->contractor_id);
//        $gContractor->notify(new NotifyContractorOfSubBid(Job::find($jobTask->job_id), User::find($bidContractorJobTask->contractor_id)->name, $gContractor));
//
//        $this->setSubStatus(Auth::user()->getAuthIdentifier(), $jobTask->id, 'sent_a_bid');
//
//        return response()->json(["message" => "Success"], 200);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // remove the task from the job
        $jobTask = JobTask::find($request->jobTaskId);

        $job = null;

        if (\is_null($request->jobId)) {
            $job = Job::find($jobTask->job_id);
        } else {
            $job = Job::find($request->jobId);
        }


        try {
            $jobTask->delete();
        } catch (\Excpetion $e) {
            Log::error('Deleting JobTask: ' . $e->getMessage());
            return response()->json(["errors" => ['error' => $e->getMessage()]], 422);
        }

//        remove all invites to subs
        $bcjts = BidContractorJobTask::where('job_task_id', '=', $request->jobTaskId)->get();

        foreach ($bcjts as $bcjt) {
            $bcjt->delete();
        }

        $job->jobTotal();
        return response()->json(["message" => "Success"], 200);

    }

    public function checkIfSubContractorExits($name, $email, $phone)
    {
        $user = User::where('email', $email)->orWhere('phone', $phone)->first();
        $result = count($user);

        if ($result !== 1) {
            $user = $this->createNewUser($name, $email, $phone);
            $userExists = false;
        } else {
            $userExists = true;
        }

        return [$user, $userExists];

    }


    public function createNewUserFirstNameAndLastName($first_name, $last_name, $email, $phone, $companyName)
    {
        if (empty($email)) {
            $email = null;
        }

        if (empty($phone)) {
            $phone = null;
        }

        $pass = RandomPasswordService::randomPassword();

        $user = User::create(
            [
                'name' => $first_name . " " . $last_name,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone' => $phone,
                'usertype' => 'contractor',
                'password_updated' => false,
                'password' => bcrypt($pass),
            ]
        );

        Contractor::create(
            [
                'user_id' => $user->id,
                'company_name' => $companyName
            ]
        );

        return $user;

    }


    public function createNewUser($name, $email, $phone)
    {
        if (empty($email)) {
            $email = null;
        }

        if (empty($phone)) {
            $phone = null;
        }

        $pass = RandomPasswordService::randomPassword();

        $user = User::create(
            [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'usertype' => 'contractor',
                'password_updated' => false,
                'password' => bcrypt($pass),
            ]
        );

        Contractor::create(
            [
                'user_id' => $user->id,
                'company_name' => $name
            ]
        );

        return $user;

    }

    public function addBidEntryForTheSubContractor($subcontractor, $jobTaskId, $taskId)
    {
        if ($subcontractor->checkIfContractorSetBidForATask($subcontractor->user_id, $jobTaskId)) {
            return $subcontractor->addContractorToBidForJobTable($subcontractor->user_id, $jobTaskId, $taskId);
        } else {
            return false;
        }
    }

    public function checkIfNameIsDifferentButPhoneAndEmailExistInTheDatabase($name, $phone, $email)
    {
        $n = DB::select("select name from users where name = ?", [$name]);
        $e = DB::select("select email from users where email = ?", [$email]);
        $p = DB::select("select phone from users where phone = ?", [$phone]);

        return empty($n) && (!empty($e) || !empty($p));
    }

    public function validateRequest($email, $phone)
    {
        if (empty($email) && empty($phone)) {
            return "allFieldsAreEmpty";
        } else if (empty($email)) {
            return "emailIsEmpty";
        } else if (empty($phone)) {
            return "phoneIsEmpty";
        } else {
            return "validationPassed";
        }
    }

    public function updateTaskStartDate(Request $request)
    {
        if (!empty($request->date)) {
            $jt = JobTask::find($request->jobTaskId);
            $jt->updateTaskStartDate($request->date);
            $earliestDate = JobTask::findEarliestStartDate($jt->job_id);
            $job = Job::find($jt->job_id);
            $job->updateJobAgreedStartDate($earliestDate);
        }
    }

    /**
     * Send an invite to a sub to bid for a task
     *
     * @param Request $request
     * @return void
     */
    public function notify(Request $request)
    {

        $this->validate($request, [
            'phone' => 'required|string|min:10|max:14',
        ]);

        $jobTask = JobTask::where('id', '=', $request->jobTaskId)->get()->first();

//        dd($request);
//        dd(User::all()); // persisted
//        dd(Job::all()); // did not persist
//        dd(Task::all());  // persisted
//        dd(JobTask::all()); // did not persist

        return Auth::user()->inviteSub(
            $request->id,
            $request->phone,
            $request->email,
            $request->jobTaskId,
            $request->quickbooksId,
            $request->firstName,
            $request->lastName,
            $request->companyName,
            $request->paymentType,
            Auth::user()->id,
            $jobTask
        );

    }

    public function notifyAcceptedBid(Request $request)
    {
        $bidId = $request->bidId;
        // find the sub that I am trying to notify

        $con = DB::select("select contractor_id 
                           from bid_contractor_job_task 
                           where id = ?", [$bidId]);
        $user_id = Contractor::where('id', $con[0]->contractor_id)
            ->get()
            ->first()
            ->user_id;
        $user = User::where('id', $user_id)->get()->first();

        $user->notify(new NotifySubOfAcceptedBid());
    }

    /**
     * Accept bid for job task from sub contractor
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function accept(Request $request)
    {
        $bidId = $request->bidId;
        $jobTaskId = $request->jobTaskId;
        $price = $request->price;
        $subId = $request->contractorId;
        $general = User::find($request->generalId);
        $jobTask = JobTask::find($jobTaskId);

        $general->approvesSubsBid(
            $jobTask, $subId, $jobTaskId, $price, $bidId
        );

        return response()->json(["message" => "Success"], 200);

//        // accept bid task
//        $bidContractorJobTask = BidContractorJobTask::find($bidId);
//
//        if ($bidContractorJobTask === false) {
//            return response()->json(["message" => "Couldn't accept bid.", "errors" => ["error" => ["Couldn't accept bid."]]], 404);
//        }


//        self::changeSubsStatuses(
//            $jobTaskId,
//            $bidId,
//            $sub
//        );


        // set the sub price in the job task table
//        $jobTask = JobTask::find($jobTaskId);
//        $task = $jobTask->task()->first();
//
//        $allContractorsForJobTask = BidContractorJobTask::select()->where("job_task_id", "=", $jobTaskId)->get();
//
//        $allContractorsForJobTask->map(function ($con) use ($bidId, $task, $jobTask) {
//            if ($con->id != $bidId) {
//                $con->accepted = 0;
//                $con->save();
//                $user = User::find($con->contractor_id);
//                $user->notify(new NotifySubOfBidNotAcceptedBid($task, $user));
//                $this->setSubStatus($user->id, $jobTask->id, 'denied');
//            } else {
//                $con->accepted = 1;
//                $con->save();
//            }
//        });
//        $this->setSubStatus($user->id, $jobTask->id, 'accepted');
//
//
//        $jobTask->sub_final_price = $price;
//        $jobTask->contractor_id = $contractorId;
//        $jobTask->bid_id = $bidContractorJobTask->id; // accepted bid
//        $jobTask->stripe = false;
//        $jobTask->status = __('bid_task.accepted');
//
//        try {
//            $jobTask->save();
//        } catch (\Exception $e) {
//            Log::error('Updating Job Task: ' . $e->getMessage());
//            return response()->json(["message" => "Couldn't accept Job Task bid.", "errors" => ["error" => ["Couldn't accept bid."]]], 404);
//        }
//
//        // notify sub contractor that his bid was approved
//        $user = User::find($contractorId);
//        $user->notify(new NotifySubOfAcceptedBid($task, $user));
//
//
//        return response()->json(["message" => "Success"], 200);

    }

    /**
     * Notify General Contractor of accepted job bid
     *
     * @param Request $request
     * @return void
     */
    public function acceptTask(Request $request)
    {
        $jobId = $request->jobId;
        $contractorId = $request->contractorId;

        // TODO: Determine if I want to accept each individual task and not just the job as a whole
        $user = User::find($contractorId)->first();

        $user->notify(new NotifyContractorOfAcceptedBid(User::find($jobId), $user));
    }

    /**
     * General Contractor
     * Approve task assigned to sub contractor
     * has been finished and notify relevant users
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function approveTaskHasBeenFinished(Request $request)
    {
        $jobTask = JobTask::find($request->id);
        $task = $jobTask->task()->first();
        $jobTask->status = __("bid_task.approved_by_general");

        $customer = User::find(Job::find($jobTask->job_id)->customer_id);
        $subContractor = User::find($jobTask->contractor_id);
        $job = Job::find($jobTask->job_id);
        $general = User::find($job->contractor_id);

        try {
            $jobTask->save();
        } catch (\Exception $e) {
            Log::error('Updating Task Status: ' . $e->getMessage());
            return response()->json(["message" => "Couldn't update status task.", "errors" => ["error" => [$e->getMessage()]]], 404);
        }

        $customer->notify(new TaskFinished(
            $task,
            $customer,
            $subContractor,
            $general,
            $jobTask,
            $job
        ));
        $subContractor->notify(new TaskApproved($task, $subContractor));

        $this->setJobTaskStatus($jobTask->id, 'approved_subs_work');
        $this->setSubStatus($jobTask->contractor_id, $jobTask->id, 'finished_job_approved_by_contractor');

        return response()->json(["message" => "Success"], 200);
    }


    public function taskFinishedBySubContractor(Request $request)
    {

        if (empty($request->job_task_id)) {
            $jobTask = JobTask::find($request->id);
        } else {
            $jobTask = JobTask::find($request->job_task_id);
        }

        $jobTask->subFinishesJobTask();

        return response()->json(["message" => "Success"], 200);
    }

    public function taskFinishedBGeneralContractor(Request $request)
    {


        $jobTask = JobTask::find($request->id);
        $task = $jobTask->task()->first();
        $subContractor = User::find($jobTask->contractor_id);
        $job = Job::find($jobTask->job_id);
        $general = User::find($job->contractor_id);

        $status = __("bid_task.finished_by_general");

        if (!$jobTask->updateStatus($status)) {
            return response()->json(["message" => "Couldn't update job task.", "errors" => ["error" => ['']]], 422);
        }

        $this->setsGeneralsFinishedStatus($jobTask);

        $this->notifyTheCustomer(
            $task,
            $subContractor,
            $general,
            $jobTask,
            $job
        );
        $jobTask->resetDeclinedMessage();

        return response()->json(["message" => "Success"], 200);
    }


    public function getJobTaskIdFromRequest($jobTaskId, $id)
    {
        if ($jobTaskId !== null) {
            // request comes from the bid task page
            // main object is not the task itself
            return $jobTaskId;
        } else {
            return $id;
        }
    }

    public function updateTheFinishedStatus($jobTask, $status)
    {

    }

    public function getTheJobTask($id)
    {
        return JobTask::find($id);
    }

    public function getTheTaskFromTheJobTask($jobTask)
    {
        return $jobTask->task()->first();
    }

    public function getTheGeneralContractor($contractor_id)
    {
        return User::find($contractor_id);
    }

    public function getTheCustomerFromTheJobTask($jobTask)
    {
        return User::find(Job::find($jobTask->job_id)->customer_id);
    }

    public function notifyTheCustomer(
        $task,
        $subContractor,
        $general,
        $jobTask,
        $job
    )
    {
        $customer = $this->getTheCustomerFromTheJobTask($jobTask);
        $customer->notify(new TaskFinished(
            $task,
            $customer,
            $subContractor,
            $general,
            $jobTask,
            $job
        ));
    }

    public function setSubsFinishedStatus($jobTask)
    {
        $this->setJobTaskStatus($jobTask->id, 'sub_finished_work');
        $this->setSubStatus($jobTask->contractor_id, $jobTask->id, 'finished_job');
    }

    public function setsGeneralsFinishedStatus($jobTask)
    {
        $this->setJobTaskStatus($jobTask->id, 'general_finished_work');
    }

    /**
     * General or Sub Contractor
     * Put task as finished and notify relevant users
     *
     * @param Request $request task or bidcontractorjobtask object
     * @return void
     */
    public function taskHasBeenFinished(Request $request)
    {
        $id = 0;
        $finishedByGeneral = false;

        if ($request->job_task_id !== null) {
            // request comes from the bid task page
            // main object is not the task itself 
            $id = $request->job_task_id;
        } else {
            $id = $request->id;
        }

        $jobTask = JobTask::find($id);
        $task = $jobTask->task()->first();

        $subContractor = User::find($jobTask->contractor_id);
        $job = Job::find($jobTask->job_id);

        if ($request->current_user_id === $jobTask->contractor_id && $request->current_user_id === $task->contractor_id) {
            $finishedByGeneral = true;
            $status = __("bid_task.finished_by_general");
        } else {
            $status = __("bid_task.finished_by_sub");
        }

        $customer = User::find(Job::find($jobTask->job_id)->customer_id);
        $generalContractor = User::find($task->contractor_id);

        if (!$jobTask->updateStatus($status)) {
            return response()->json(["message" => "Couldn't update job task.", "errors" => ["error" => ['']]], 422);
        }

        if ($finishedByGeneral) {
            // is general contractor
            $customer->notify(new TaskFinished(
                $task,
                null,
                $subContractor,
                $generalContractor,
                $this,
                $job
            ));
        } else {
            $generalContractor->notify(new TaskFinished(
                $task,
                $customer,
                $subContractor,
                $generalContractor,
                $jobTask,
                $job
            ));
        }

        if ($jobTask->contractor_id == $generalContractor->id) {
            $this->setJobTaskStatus($jobTask->id, 'general_finished_work');
        } else {
            $this->setJobTaskStatus($jobTask->id, 'sub_finished_work');
            $this->setSubStatus($jobTask->contractor_id, $jobTask->id, 'finished_job');
        }

        $jobTask->resetDeclinedMessage();

        return response()->json(["message" => "Success"], 200);
    }

    /**
     * changes the status of a task to reopened
     *
     * @param Request $request
     * @return void
     */
    public function reopenTask(Request $request)
    {

        $this->validate($request, [
            'id' => 'required',
        ]);

        $jobTask = JobTask::find($request->id);
        $jobTask->updateStatus(__('bid_task.reopened'));

        $task = $jobTask->task()->first();
        $sub_contractor_id = $jobTask->contractor_id;
        $general_contractor_id = $task->contractor_id;

        $customer = $jobTask->job()->first()->customer()->first();

        if ($sub_contractor_id != $general_contractor_id) {
            $sub_contractor = User::find($sub_contractor_id);
            $sub_contractor->notify(new TaskReopened($task));
        }

        $customer->notify(new TaskReopened($task));


        return response()->json(['message' => 'task reopened'], 200);
        // change the status of the job to pending
    }


    public function updateMessage(Request $request)
    {
        $this->validateRequest($request, [
            'message' => 'required|string',
            'jobTaskId' => 'required|numeric',
            'actor' => 'required|string'
        ]);

        $message = $request->message;
        $jobTaskId = $request->jobTaskId;
        $actor = $request->actor;

        $jobTask = JobTask::find($jobTaskId);
        $job = Job::find($jobTask->job_id);
        $customer = Customer::find($job->customer_id);

        // are there subs for this task?

        //has the job been accepted
        // check if the contractor_id on the jobtask table equals the contractor_id of the jobs table


        if ($actor == 'sub') {
            $b = new BidContractorJobTask();
            $contractors = $b->select('contractor_id')->where('job_task_id', '=', $jobTask->job_id)->get();
            Log::debug("actor: $actor");
            Log::debug("contractors: $contractors");
            $jobTask->sub_message = $message;
            Log::debug("job->contractor_id: $job->contractor_id");
            Log::debug("jobTask->contractor_id: $jobTask->contractor_id");
            Log::debug("jobTask->status: $jobTask->status");

            // checks if the job has been accepted then it sends the notification to just that contractor
            // if the job has not been accepted then the notification gets sent to all subs
            // who have been triggered to bid on the job
            if ($job->contractor_id != $jobTask->contractor_id && $jobTask->status == 'bid_task.accepted') {
                Log::debug("A single contractor has been called");
                Log::debug("jobTask->contractor_id: $jobTask->contractor_id");
                $contractor = User::find($jobTask->contractor_id);
                $contractor->notify(new NotifySubOfUpdatedMessage);
            } else if (!empty($contractors)) {
                Log::debug("multiple contractors are being called");
                Log::debug("contractors: $contractors");
                foreach ($contractors as $c) {
                    Log::debug("jobTask->contractor_id: $jobTask->contractor_id");
                    $contractor = User::find($c->contractor_id);
                    $contractor->notify(new NotifySubOfUpdatedMessage);
                }
            }
        } else {
            Log::debug("actor: $actor");
            $customer = User::find($job->customer_id);
            $jobTask->customer_message = $message;
            $customer->notify(new NotifyCustomerOfUpdatedMessage);
        }

        try {
            $jobTask->save();
            return response()->json([
                'success' => $jobTask
            ], 200);
        } catch (\Excpetion $e) {
            Log::error('Updating JobTask: ' . $e->getMessage);
            return response()->json([
                'error' => $e->getMessage
            ], 200);
        }

    }


    public function updateTaskQuantity(Request $request)
    {

        $this->validateRequest($request, [
            'quantity' => 'required|numeric',
            'taskId' => 'required|numeric'
        ]);

        $quantity = $request->quantity;
        $taskId = $request->taskId;

        $jobTask = JobTask::find($taskId);
        $job = Job::find($jobTask->job_id);

        $unit_price = $this->convertToCents($request->unit_price);

        try {
            $jobTask->qty = $quantity;
            $jobTask->cust_final_price = $quantity * $unit_price;
            $jobTask->save();
        } catch (\Excpetion $e) {
            Log::error('Updating JobTask: ' . $e->getMessage);
            return 'Updating JobTask: ' . $e->getMessage;
        }

        $job->jobTotal();

    }

    public function updateCustomerPrice(Request $request)
    {
        $this->validate($request, [
            'price' => 'required|numeric',
            'jobTaskId' => 'required|numeric',
            'jobId' => 'required|numeric'
        ]);

        $price = $this->convertToCents($request->price);
        $taskId = $request->taskId;

        $jobTask = JobTask::find($request->jobTaskId);
        try {
            $jobTask->unit_price = $price;
            $jobTask->cust_final_price = $price * $jobTask->qty;
            $jobTask->save();
        } catch (\ExceptionWithThrowable $e) {
            Log::error('Updating JobTask: ' . $e->getMessage);
        }

        $jobId = $request->jobId;
        $job = Job::find($jobId);
        $job->jobTotal();

        $data = ["price" => $price, "taskId" => $taskId];
        return json_encode($data);

    }

    public function updateTaskName(Request $request)
    {
        $taskName = $request->taskName;
        $taskId = $request->taskId;

        DB::table('tasks')
            ->where('id', $taskId)
            ->update(['name' => $taskName]);

    }

    public function getOneTask(Task $task)
    {
        $job = Job::find(1)->select(['id', 'job_name'])->get()->first();
        $job_tasks = JobTask::select(['id', 'cust_final_price', 'sub_final_price', 'qty', 'customer_message', 'sub_message'])->where('job_id', $job->id)->get()->first();
        $bcjt = BidContractorJobTask::select()->where('job_task_id', $job_tasks[0]->id)->get()->first();
        $images = TaskImage::select()->where('job_task_id', $job_tasks[0]->id)->get()->first();

        $ja = $job->toArray();
        $ja['job_tasks'] = $job_tasks->toArray();

        foreach ($job_tasks as $job_task) {
            foreach ($bcjt as $bc) {
                if ($job_task->id === $bc->job_task_id) {

                }
            }
        }

    }

    public function getTasks(Request $request)
    {

        $tasks = Task::select()->
        where('contractor_id', '=', Auth::user()->getAuthIdentifier())->
        where('name', 'like', $request->taskname . '%')->get();

        return $this->convertTasksCentsToDollars($tasks);

//        return response()->json([
//            $this->convertTasksCentsToDollars($tasks)
//        ]);

    }

    private function convertTasksCentsToDollars($tasks)
    {
        foreach ($tasks as $task) {
            $task->proposed_cust_price = $this->convertToDollars($task->proposed_cust_price);
            $task->proposed_sub_price = $this->convertToDollars($task->proposed_sub_price);
        }

        return $tasks;
    }


    public function addTask(Request $request)
    {

        Task::validate_new_task_input($request);

        $task = Task::where('name', '=', $request->taskName)
            ->where('contractor_id', '=', Auth::user()->getAuthIdentifier())
            ->get()->first();

//        $task = Task::find($request->taskId);
        $job = Job::find($request->jobId);
        $customer = User::find($request->customer_id);

        $contractorCustomer = ContractorCustomer::where('contractor_user_id', '=', $request->contractorId)
            ->where('customer_user_id', '=', $request->customer_id)->first();
        if ($contractorCustomer != null) {
            $customer_quickBooks_Id = $contractorCustomer->quickbooks_id;
        }

        if (!empty($task)) {
            $jobTask = new JobTask();
            $jobTask->addJobTask($request, $task->id);

            if ($task->isTaskAQBLineItem($request->item_id)) {
                if ($job->hasAQuickbookEstimateBeenCreated()) {
                    $job->updateQuickBooksEstimate($task, $job, $jobTask);
                } else {
                    $estimate = $job->createQuickBooksEstimate($customer, $task, $job, $jobTask, $customer_quickBooks_Id);
                    $job->qb_estimate_id = $estimate->Id;
                    $job->save();
                }
            }
            if ($request->updateBasePrice) {
                $task->proposed_cust_price = $request->taskPrice;
                $task->save();
            }
        } else {
            $task = new Task();
            $task->createTask($request);
            $jobTask = new JobTask();
            $jobTask->addJobTaskFromNewJob($job->id, $task->id, $request);

            $qb = new Quickbook();
            if ($qb->isContractorThatUsesQuickbooks()) {
                $item = $task->createItem($task, $request);
                $task->updateTaskWithQuickbooksItem($item);
                if ($job->hasAQuickbookEstimateBeenCreated()) {
                    $job->updateQuickBooksEstimate($task, $job, $jobTask);
                } else {
                    try {
                        $estimate = $job->createQuickBooksEstimate($customer, $task, $job, $jobTask, $customer_quickBooks_Id);
                        $job->qb_estimate_id = $estimate->Id;
                    } catch (\Throwable $th) {
                        Log::error($th->getMessage());
                    }
                    try {
                        $job->save();
                    } catch (\Exception $e) {
                        return response()->json([
                            'message' => $e->getMessage(),
                            'code' => $e->getCode()
                        ], 200);
                    }
                }
            }
        }

        $jobTask->setLocation($job);
        $job->changeJobStatus($job, __('bid.in_progress'));
        $job->jobTotal();
        $job->setEarliestStartDateToTask($jobTask);

        $this->setJobStatus($job->id, 'in_progress');
        $this->setJobTaskStatus($jobTask->id, 'initiated');

        return response()->json($job->tasks()->get(), 200);
    }

    /**
     * Deny the task has been properly finished
     *
     * @param Request $request
     * @return Response
     */
    public function denyTask(Request $request)
    {

        $this->validate($request, [
            'job_task_id' => 'required',
        ]);

        $jobTask = JobTask::find($request->job_task_id);
        $task = $jobTask->task()->first();
        $jobTask->updateStatus(__('bid_task.denied'));

        if ($this->iAmACustomer()) {
            $this->setJobTaskStatus($jobTask->id, 'customer_changes_finished_task');
            if ($this->taskUsesAsub($jobTask, $task)) {
                $this->setSubStatus($jobTask->contractor_id, $jobTask->id, 'customer_changes_finished_task');
                $this->notifyGeneral($jobTask, $request->message);
                $this->notifySub($jobTask->contractor_id, $jobTask, $request->message);
            } else {
                $this->notifyGeneral($jobTask, $request->message);
            }

        } else if ($this->iAmAGeneral($task)) {
            $this->notifySub($jobTask->contractor_id, $jobTask, $request->message);
            $this->setSubStatus($jobTask->contractor_id, $jobTask->id, 'finished_job_denied_by_contractor');
            $this->setJobTaskStatus($jobTask->id, 'declined_subs_work');
        }

        $jobTask->setDeclinedMessage($request->message);

        return response()->json($task, 200);
    }

    public function notifyGeneral($jobTask, $message)
    {
        $contractor = User::find($jobTask->contractor_id);
        $contractor->notify(new TaskWasNotApproved($jobTask, $contractor, $message));
    }

    public function notifySub($contractor_id, $jobTask, $message)
    {
        $subContractor = User::find($contractor_id);
        $subContractor->notify(new TaskWasNotApproved($jobTask, $subContractor, $message));
    }

    public function taskUsesASub($jobTask, $task)
    {
        return $jobTask->contractor_id !== $task->contractor_id;
    }

    public function iAmACustomer()
    {
        return Auth::user()->usertype == 'customer';
    }

    public function iAmAGeneral($task)
    {
        return Auth::user()->getAuthIdentifier() == $task->contractor_id;
    }

    public function iAmASub($jobTask, $task)
    {

    }

    public function updateTaskWithNewValuesIfValuesAreDifferent($task, $subTaskPrice, $taskPrice)
    {
        if ($task->proposed_cust_price != $taskPrice || $task->proposed_sub_price != $subTaskPrice) {
            $task->proposed_cust_price = $taskPrice;
            $task->proposed_sub_price = $subTaskPrice;
            $task->save();
        }
        return $task;
    }

    public function updateJobTask($request, $task_id, $jobTask)
    {

        Log::info('quantity unit: ' . $request->qtyUnit);

        $jobTask->job_id = $request->jobId;
        $jobTask->task_id = $task_id;
        $jobTask->status = __('bid_task.initiated');
        $jobTask->cust_final_price = $this->convertToCents($request->taskPrice * $request->qty);
        $jobTask->sub_final_price = 0;
        $jobTask->contractor_id = $request->contractorId;
        $jobTask->sub_message = $request->sub_message;
        $jobTask->customer_message = $request->customer_message;
        $jobTask->stripe = $request->useStripe;
        $jobTask->qty = $request->qty;
        if ($request->start_when_accepted) {
            $jobTask->start_when_accepted = true;
            $jobTask->start_date = \Carbon\Carbon::now();
        } else {
            $jobTask->start_date = $request->start_date;
        }
        $jobTask->save();
    }

    /**
     * Upload images and attach them to the task
     *
     * @param Request $request
     * @return void
     */
    public function uploadTaskImage(Request $request)
    {
        // get the file
        $file = $request->photo;

        // create a hash name for storage and retrieval
        $path = $file->hashName('tasks');

        $image = Cloudinary\Uploader::upload($file, [
            "public_id" => $path
        ]);

        if (empty($image)) {
            return response()->json(['message' => 'Error Uploading Image. Please Try Again'], 400);
        }

//         store the file
//        $disk = Storage::disk('public');
//        $disk->put(
//            $path, $this->formatImage($file)
//        );

        $url = $image['secure_url'];
        $taskImage = new TaskImage;
        $taskImage->job_id = $request->jobId;
        $taskImage->user_id = Auth::user()->getAuthIdentifier();
        $taskImage->url = $url;
        $taskImage->secure_url = $url;
        $taskImage->public_id = $image['public_id'];
        $taskImage->version = $image['version'];
        $taskImage->signature = $image['signature'];
        $taskImage->width = $image['width'];
        $taskImage->height = $image['height'];
        $taskImage->format = $image['format'];
        $taskImage->resource_type = $image['resource_type'];
        $taskImage->bytes = $image['bytes'];
        $taskImage->type = $image['type'];
        $taskImage->etag = $image['etag'];
        $taskImage->placeholder = $image['placeholder'];
//        $taskImage->overwritten = $image['overwritten'];
        $taskImage->original_filename = $image['original_filename'];

        if ($this->imageIsAttachedToAJobTask($request->jobTaskId)) {
            $taskImage->job_task_id = $request->jobTaskId;
        }
        try {
            $taskImage->save();
        } catch (\Exception $e) {
            Log::error('Saving Task Image: ' . $e->getMessage());
//            if (preg_match('/logos\/(.*)$/', $url, $matches)) {
//                $disk->delete('tasks/' . $matches[1]);
//            }
            return response()->json(['message' => 'error uploading image', errors => [$e->getMessage]], 400);
        }


        // notify the customers and the contractors of the uploaded file
        $job = Job::find($taskImage->job_id);
        $jobTask = null;

        if ($this->imageIsAttachedToAJobTask($request->jobTaskId)) {
            $jobTask = JobTask::find($taskImage->job_task_id);
        }

        $customer = $job->customer()->first();
        $contractor = $job->contractor()->first();

        if (
            (Auth::user()->id !== $customer->id) &&
            ($job->status != 'bid.in_progress' && $job->status != 'bid.initiated')
        ) {
            $job->customer()->first()->notify(new UploadedTaskImage());
        }
        if (Auth::user()->id !== $contractor->id) {
            $job->contractor()->first()->notify(new UploadedTaskImage());
        }

        if ($this->imageIsAttachedToAJobTask($request->jobTaskId)) {
            if ($job->contractor_id !== $jobTask->contractor_id) {
                $jobTask->contractor()->first()->notify(new UploadedTaskImage());
            }
        }

        return $url;
    }

    protected function imageIsAttachedToAJobTask($jobTaskId)
    {
        return $jobTaskId !== "null";
    }

    protected function formatImage($file)
    {
        $images = new ImageManager;
        return (string)$images->make($file->path())->encode();
    }

    public function deleteImage(TaskImage $taskImage)
    {
        $job = Job::find($taskImage->job_id);
        $currentUserId = Auth::user()->getAuthIdentifier();

        try {
            $taskImage->delete();
            $result = Cloudinary\Uploader::destroy($taskImage->public_id, $options = array());
            if ($result['result'] == "ok") {
                if ($this->isAJobImage($taskImage->job_task_id)) {
                    if ($this->userIsACustomerByJob($job->customer_id, $currentUserId)) {
                        $this->notifyGeneralOfDeletedTask($job);
                    } else {
                        $this->notifyCustomerOfDeletedTask($job);
                    }
                } else {
                    $jobTask = JobTask::find($taskImage->job_task_id);
                    if ($this->userIsACustomerByJob($job->customer_id, $currentUserId)) {
                        $this->notifyGeneralOfDeletedTask($job);
                        $this->notifySubOfDeletedTask($job, $jobTask, $currentUserId);
                    } else if ($this->userIsAGeneralByJob($job->contractor_id, $jobTask->contractor_id)) {
                        $this->notifyCustomerOfDeletedTask($job);
                        $this->notifySubOfDeletedTask($job, $jobTask, $currentUserId);
                    }
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage()
            ], 200);
        }

    }

    private function isAJobImage($jobTaskId)
    {
        return \is_null($jobTaskId);
    }

    private function userIsAGeneralByJob($jobContractorId, $jobTaskContractorId)
    {
        return $jobContractorId === $jobTaskContractorId;
    }

    private function userIsACustomerByJob($jobCustomerId, $userId)
    {
        return $jobCustomerId === $userId;
    }

    private function userIsASubByJob($jobContractorId, $jobTaskContractorId)
    {
        return $jobContractorId !== $jobTaskContractorId;
    }

    private function jobHasBeenSent($job)
    {
        return $job->status != 'bid.in_progress' && $job->status != 'bid.initiated';
    }

    private function notifyCustomerOfDeletedTask($job)
    {
        $job->customer()->first()->notify(new TaskImageDeleted());
    }

    private function notifyGeneralOfDeletedTask($job)
    {
        $job->contractor()->first()->notify(new TaskImageDeleted());
    }

    private function notifySubOfDeletedTask($job, $jobTask, $userId)
    {
        if ($this->jobTaskHasSub($jobTask->contractor_id, $userId)) {
            $jobTask->contractor()->first()->notify(new TaskImageDeleted());
        }
    }

    public function jobTaskHasSub($jobTaskContractorId, $userId)
    {
        return $jobTaskContractorId === $userId;
    }

    public function setChangeMessage(Request $request)
    {
        $message = new TaskMessage();
        $message->general_id = $request->general_id;
        $message->sub_id = $request->sub_id;
        $message->customer_id = $request->customer_id;
        $message->job_task_id = $request->job_task_id;
        $message->message = $request->message;

        try {
            $message->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        $jobTask = JobTask::find($request->job_task_id);
        $job = Job::find($jobTask->job_id);
        $this->setJobStatus($job->id, 'changed');
        $this->setJobTaskStatus($jobTask->id, 'changed');
        $customer = Auth::user();
        $sub = User::find($request->sub_id);
        $contractor = User::find($request->general_id);

        $task = $jobTask->task()->get()->first();

        if ($this->isASub($request->sub_id, $request->general_id)) {
            $this->notifyMessageChange($customer, $sub, $task, $message, $jobTask);
            $this->notifyMessageChange($customer, $contractor, $task, $message, $jobTask);
        } else {
            $this->notifyMessageChange($customer, $contractor, $task, $message, $jobTask);
        }

    }

    public function notifyMessageChange($customer, $contractor, $task, $message, $jobTask)
    {

        $customer->notify(new NotifyContractorThatCustomerChangesBid($task, $contractor, $customer, $jobTask, $message));

    }

    public function isASub($contractorId, $generalId)
    {
        return $contractorId != $generalId;
    }

    public function getAllTaskIdsForJob($jobId)
    {
        $jobTasks = Job::where('jobs.id', '=', $jobId)->get()->first()->jobTasks()->select('job_task.id', 'task_id')->get();
        foreach ($jobTasks as $jobTask) {
            $jobTask['name'] = $jobTask->task()->select('name')->get()->first()->name;
        };
        return $jobTasks;
    }
}
