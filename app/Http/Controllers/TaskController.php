<?php

namespace App\Http\Controllers;

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
use App\QuickbooksItem;
use App\Task;
use App\Job;
use App\Contractor;
use App\Customer;
use App\User;
use App\BidContractorJobTask;
use App\JobTask;
use Illuminate\Support\Facades\Log;
use App\TaskImage;
use Illuminate\Http\Request;
use App\Services\RandomPasswordService;
use App\Services\SanatizeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{

    //    use Notifiable;

    /**
     * Display a listing of the resource.
     * NOTICE:
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        echo 'hello world';
    }

    public function bidContractorJobTasks()
    {
        $bidTasks = Auth::user()->contractor()->first()->bidContractorJobTasks()->
        with([
            'jobTask.job',
            'jobTask.task'
        ])->get();
        return view('tasks.index')->with(['tasks' => $bidTasks]);
    }

    /**
     * Get all bid tasks from the currently logged in contractor
     *
     * @return void
     */
    public function bidTasks()
    {
        if (Auth::user()->usertype == 'contractor') {
            $bidTasks = Auth::user()->
            contractor()->first()->
            bidContractorJobTasks()->with([
                'jobTask.job',
                'jobTask.task',
                'jobTask.images',
                'jobTask.location'
            ])->get();
            return response()->json($bidTasks, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function getJobTask($jobTaskId)
    {
//        Log::debug($jobTask);
//        dd($jobTask);

        Log::debug("Job Task Id: $jobTaskId");

        $jobTask = JobTask::find($jobTaskId);

        return $jobTask->load(['images', 'task']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        //$task->bid_price = $request->bid_price;

        try {
            $task->save();
        } catch (\Exception $e) {
            Log::error('Update Task:' . $e->getMessage());
        }
    }

    /**
     * Update jobtask location
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Task $task
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
     * @param  \App\Task $task
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
     * @param  \Illuminate\Http\Request $request
     * @param  \App\BidContractorJobTask $task
     * @return \Illuminate\Http\Response
     */
    public function updateBidContractorJobTask(Request $request, $id)
    {
        $bidContractorJobTask = BidContractorJobTask::find($id);

        if ($bidContractorJobTask == null) {
            return response()->json(["message" => "Couldn't find record.", "errors" => ["error" => ["Couldn't find record."]]], 404);
        } else if ($request->bid_price == 0) {
            return response()->json(["message" => "Price needs to be greater than 0.", "errors" => ["error" => [""]]], 412);
        }

        $bidContractorJobTask->bid_price = $request->bid_price;
        $bidContractorJobTask->status = 'bid_task.bid_sent';
        $bidContractorJobTask->payment_type = $request->paymentType;
        $jobTask = $bidContractorJobTask->jobTask()->first();

        // doesn't work since no default 'id' found
        // $jobTask->status = 'bid_task.sent';

        try {
            $bidContractorJobTask->save();
        } catch (\Exception $e) {
            Log::error('Update Bid Task:' . $e->getMessage());
            return response()->json(["message" => "Couldn't save record.", "errors" => ["error" => [$e->getMessage()]]], 404);
        }

        $jobTask->updateStatus(__('bid_task.bid_sent'));

        $gContractor = User::find($jobTask->task()->first()->contractor_id);
        $gContractor->notify(new NotifyContractorOfSubBid(Job::find($jobTask->job_id), User::find($bidContractorJobTask->contractor_id)->name, $gContractor));

        return response()->json(["message" => "Success"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // remove the task from the job
        $job = Job::find($request->jobId);
        $jobTask = JobTask::find($request->jobTaskId);

        try {
            $jobTask->delete();
        } catch (\Excpetion $e) {
            Log::error('Deleteing JobTask: ' . $e->getMessage());
            return response()->json(["errors" => ['error' => $e->getMessage()]], 422);
        }

//        $job->subtractPrice(($jobTask->cust_final_price * $jobTask->qty));
        $job->jobTotal();
        return response()->json(["message" => "Success"], 200);

    }

    public function checkIfSubContractorExits($name, $email, $phone)
    {
        $user = User::where('email', $email)->orWhere('phone', $phone)->first();
        $result = count($user);
        $userExists = false;

        if ($result !== 1) {
            $user = $this->createNewUser($name, $email, $phone);
            $userExists = false;
        } else {
            $userExists = true;
        }

        $userData = [$user, $userExists];

        return $userData;
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

    public function addBidEntryForTheSubContractor($contractor, $jobTaskId, $taskId)
    {
        if ($contractor->checkIfContractorSetBidForATask($contractor->user_id, $jobTaskId)) {
            $contractor->addContractorToBidForJobTable($contractor->user_id, $jobTaskId, $taskId);
            return true;
        } else {
            return false;
        }
    }

    public function checkIfNameIsDifferentButPhoneAndEmailExistInTheDatabase($name, $phone, $email)
    {
        $n = DB::select("select name from users where name = ?", [$name]);
        $e = DB::select("select email from users where email = ?", [$email]);
        $p = DB::select("select phone from users where phone = ?", [$phone]);
        if (empty($n) && (!empty($e) || !empty($p))) {
            return true;
        } else {
            return false;
        }
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
            // 'email' => 'required|email',
        ]);

        //
        $phone = SanatizeService::phone($request->phone);
        $email = $request->email;
        $jobTaskId = $request->jobTaskId;
        $name = $request->name;

        $user = User::where('phone', $phone)->orWhere('email', $email)->first();


        // TODO: Not sure about this logic. Should be if a user is not a contractor then create a contractor. If a user is a customer then that should not throw an error becuase a user can be both a contractor and a customer
        if ($user === null) {
            // if no user found create one
            $user = $this->createNewUser($name, $email, $phone);
        } else if ($user->usertype === 'customer') {
            // return if the user is a customer
            return response()->json(["message" => "This person is a customer in the system and can not also be a contractor", "errors" => ["error" => "No valid user."]], 422);
        }

        $contractor = $user->contractor()->first();
        $jobTask = JobTask::find($jobTaskId);

        // add an entry in to the contractor bid table so that the sub can bid on the task
        if ($this->addBidEntryForTheSubContractor($contractor, $jobTaskId, $jobTask->task_id) === false) {
            return response()->json(["message" => "Task Already Exists.", "errors" => ["error" => "Task Already Exists."]], 422);
        }

        // this code will redirect them to the page with information on the task
        // if so then send a notification to that contractor
        $user->notify(new NotifySubOfTaskToBid($jobTask->task_id, $user));


        $bidPrices = Task::getBidPrices($jobTask->job_id);

        return $bidPrices;
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

        //        return $user;

        $user->notify(new NotifySubOfAcceptedBid());
    }

    /**
     * Accept bid for job task from sub contractor
     *
     * @param Request $request
     * @return void
     */
    public function accept(Request $request)
    {
        $bidId = $request->bidId;
        $jobTaskId = $request->jobTaskId;
        $jobId = $request->jobId;
        $price = $request->price;
        $contractorId = $request->contractorId;

        // accept bid task
        $bidContractorJobTask = BidContractorJobTask::find($bidId);

        if ($bidContractorJobTask === false) {
            return response()->json(["message" => "Couldn't accept bid.", "errors" => ["error" => ["Couldn't accept bid."]]], 404);
        }

        // change statuses on bidContractorJobTask. need to change the statuses for each contractor that has this jobtaskid
        $allContractorsForJobTask = BidContractorJobTask::select()->where("job_task_id", "=", $jobTaskId)->get();


        $allContractorsForJobTask->map(function ($contractor) use ($bidId) {
            $c = BidContractorJobTask::find($contractor->id);
            if ($contractor->id === $bidId) {
                $c->accepted = true;
            } else {
                $c->accepted = false;
            }
            $c->save();
        });

        // set the sub price in the job task table
//        $job = Job::find($jobId);
        $jobTask = JobTask::find($jobTaskId);
        $task = $jobTask->task()->first();

        $allContractorsForJobTask = BidContractorJobTask::select()->where("job_task_id", "=", $jobTaskId)->get();

        $allContractorsForJobTask->map(function ($con) use ($bidId, $contractorId, $task) {
            if ($con->id != $bidId) {
                $con->accepted = 0;
                $con->save();
                $user = User::find($con->contractor_id);
                $user->notify(new NotifySubOfBidNotAcceptedBid($task, $user));
            } else {
                $con->accepted = 1;
                $con->save();
            }
        });

//        dd($allContractorsForJobTask);

        $jobTask->sub_final_price = $price;
        $jobTask->contractor_id = $contractorId;
        $jobTask->bid_id = $bidContractorJobTask->id; // accepted bid
        $jobTask->stripe = false;
        $jobTask->status = __('bid_task.accepted');

        try {
            $jobTask->save();
        } catch (\Exception $e) {
            Log::error('Updating Job Task: ' . $e->getMessage());
            return response()->json(["message" => "Couldn't accept Job Task bid.", "errors" => ["error" => ["Couldn't accept bid."]]], 404);
        }

        // notify sub contractor that his bid was approved
        $user = User::find($contractorId);
        $user->notify(new NotifySubOfAcceptedBid($task, $user));

        return response()->json(["message" => "Success"], 200);
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

        try {
            $jobTask->save();
        } catch (\Exception $e) {
            Log::error('Updating Task Status: ' . $e->getMessage());
            return response()->json(["message" => "Couldn't update status task.", "errors" => ["error" => [$e->getMessage()]]], 404);
        }

        $customer->notify(new TaskFinished($task, true, $customer));
        $subContractor->notify(new TaskApproved($task, $subContractor));

        return response()->json(["message" => "Success"], 200);
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
            $customer->notify(new TaskFinished($task, true, $customer));
        } else {
            $generalContractor->notify(new TaskFinished($task, false, $generalContractor));
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

        $general_contractor = User::find($general_contractor_id);
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


//        $jobTask = JobTask::find(12);
//        $job = Job::find($jobTask->job_id);
//        $user = Customer::select()->where("user_id", "=", $job->customer_id)->get()->first();


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

        try {
            $jobTask->qty = $quantity;
            $jobTask->cust_final_price = $quantity * $jobTask->unit_price;
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

        $price = $request->price;
        $taskId = $request->taskId;
        $jobId = $request->jobId;
        $job = Job::find($jobId);
        $jobTask = JobTask::find($request->jobTaskId);
        $oldPrice = $jobTask->cust_final_price;

        try {
            $jobTask->unit_price = $price;
            $jobTask->cust_final_price = $price * $jobTask->qty;
            $jobTask->save();
        } catch (\Excpetion $e) {
            Log::error('Updating JobTask: ' . $e->getMessage);
        }

        $job->jobTotal();

//        $job->addPrice($price - $oldPrice);

        $data = ["price" => $price, "taskId" => $taskId];
        $data = json_encode($data);
        return $data;
    }

    public function updateTaskName(Request $request)
    {
        $taskName = $request->taskName;
        $taskId = $request->taskId;

        DB::table('tasks')
            ->where('id', $taskId)
            ->update(['name' => $taskName]);

    }

    public function getTasks(Request $request)
    {

        $tasks = Task::select()->
        where('contractor_id', '=', Auth::user()->getAuthIdentifier())->
        where('name', 'like', $request->taskname . '%')->get();

        return $tasks;
    }

    public function addTask(Request $request)
    {

        Task::validate_new_task_input($request);

        $task = Task::find($request->taskId);
        $job = Job::find($request->jobId);
        $qb = new Quickbook();

        if (!empty($task)) {
            $jt = new JobTask();
            $jt->createJobTask($request);
            if($task->isTaskAQBLineItem($request->item_id)){
                if($job->hasAQuickbookEstimateBeenCreated()){
                    $estimate = $this->getQBEstimate($request);
                    $this->addTaskToQBEstimate($request, $estimate);
                }
              else {
                    $estimate = $qb->createEstimate($request);
//                    $this->addTaskToQBEstimate($request, $estimate);
                }
            }
        }

//        else {
//            $task = $this->createNewTask($request);
//            $this->addTaskToJobTaskTable($task);
//            $qb = new Quickbook();
//            if($qb->isContractorThatUsesQuickbooks()){
//                if($this->hasAQuickbookEstimateBeenCreated($request)){
//                    $estimate = $this->getQBEstimate($request);
//                    $this->addTaskToQBEstimate($request, $estimate);
//                } else {
//                    $estimate = $this->createQBEstimate($request);
//                    $this->addTaskToQBEstimate($request, $estimate);
//                }
//            }
//        }


//        if ($request->taskId != -1) {
//
//        }
//
//        $taskInput = Task::create_task_input_array($request);
//
//        if ($taskInput['taskPrice'] <= $taskInput['subTaskPrice']) {
//            return response()->json([
//                "message" => "Unit price for customer needs to be greater than or equal to Unit Price for Sub",
//                "errors" => ["error" => ['Unit price for customer needs to be greater than or equal to Unit Price for Sub']]], 422);
//        }
//
//        if ($request->updateTask && !$request->createNew) {
//            $task = Task::find($request->taskId);
//            $task->updateTask($request);
//            $jobTask = new JobTask;
//            $jobTask->createJobTask($request, $request->taskId);
////            $task->update_existing_standard_task_add_to_jobTask_table($request);
//        } else if (!$request->updateTask && !$request->createNew) {
//            // find the existing task but dont update the standard task table
//            $jobTask = new JobTask;
//            $jobTask->createJobTask($request, $request->taskId);
//
//            Log::debug('updateTask is false; CreateNew is false');
//
//        } else if (!$request->updateTask && $request->createNew) {
//            // create a new task and add it to the standard task table
//            // add a new task to the job task table
//
//            Log::debug('updateTask is false; CreateNew is true');
//
//            $task = new Task;
//            $task->createTask($request);
//
//            $jobTask = new JobTask;
//            $jobTask->createJobTask($request, $task->id);
//
//        }
//
//        $job = Job::find($request->jobId);
//
//        if ($job->location_id != null) {
//            $jobTask->location_id = $job->location_id;
//            $jobTask->save();
//        }
//
//        $job->changeJobStatus($job, __('bid.in_progress'));
//        $job->jobTotal();
//        $earliestDate = JobTask::findEarliestStartDate($request->jobId);
//        $job->updateJobAgreedStartDate($earliestDate);
//
//        return response()->json($job->tasks()->get(), 200);
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

        // notify
        if ($request->user_id !== $task->contractor_id) {
            $contractor = User::find($task->contractor_id);
            $contractor->notify(new TaskWasNotApproved($task, $contractor, $request->message));
        }

        if ($jobTask->contractor_id !== $task->contractor_id) {
            $subContractor = User::find($jobTask->contractor_id);
            $subContractor->notify(new TaskWasNotApproved($task, $subContractor, $request->message));
        }

        $jobTask->setDeclinedMessage($request->message);

        return response()->json($task, 200);
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

//    public function switchJobStatusToInProgress($job, $message)
//    {
//        $job->status = $message;
//        $job->save();
//    }

    public function updateJobTask($request, $task_id, $jobTask)
    {

        Log::info('quantity unit: ' . $request->qtyUnit);

//        if (!$request->sub_sets_own_price_for_job) {
//            $jobTask->sub_sets_own_price_for_job = 0;
//        } else {
//            $jobTask->sub_sets_own_price_for_job = 1;
//        }


        $jobTask->job_id = $request->jobId;
        $jobTask->task_id = $task_id;
        $jobTask->status = __('bid_task.initiated');
        $jobTask->cust_final_price = $request->taskPrice;
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
        // $this->validate($request, [
        //         'photo' => 'required|max:4012',
        //     ]);

        $file = $request->photo;

        $path = $file->hashName('tasks');

        $disk = Storage::disk('public');

        $disk->put(
            $path, $this->formatImage($file)
        );

        $url = $disk->url($path);

        $taskImage = new TaskImage;
        $taskImage->job_id = $request->jobId;
        $taskImage->job_task_id = $request->jobTaskId;
        $taskImage->url = $url;

        try {
            $taskImage->save();
        } catch (\Excpetion $e) {
            Log::error('Saving Task Image: ' . $e->getMessage());
            if (preg_match('/logos\/(.*)$/', $url, $matches)) {
                $disk->delete('tasks/' . $matches[1]);
            }
            return response()->json(['message' => 'error uploading image', errors => [$e->getMessage]], 400);
        }

        $job = Job::find($taskImage->job_id);
        $jobTask = JobTask::find($taskImage->job_task_id);

        $customer = $job->customer()->first();
        $contractor = $job->contractor()->first();


        if (Auth::user()->id !== $customer->id) {

            if ($job->status != 'bid.in_progress' && $job->status != 'bid.initiated') {
                $job->customer()->first()->notify(new UploadedTaskImage());
            }


        }
        if (Auth::user()->id !== $contractor->id) {
            $job->contractor()->first()->notify(new UploadedTaskImage());
        }


        if ($job->contractor_id !== $jobTask->contractor_id) {
            $jobTask->contractor()->first()->notify(new UploadedTaskImage());
        }

        return $url;
    }

    protected function formatImage($file)
    {
        $images = new ImageManager;
        return (string)$images->make($file->path())->encode();
    }

    public function deleteImage(TaskImage $taskImage)
    {
        if (preg_match('/tasks\/(.*)$/', $taskImage->url, $matches)) {
            $disk = Storage::disk('public');
            try {
                $disk->delete('tasks/' . $matches[1]);
            } catch (\Exception $e) {
                Log::error('Delete Image: ' . $e->getMessage());
                return response()->json('Something Went Wrong', 422);
            }

            $taskImage->delete();
        }

        $job = Job::find($taskImage->job_id);
        $jobTask = JobTask::find($taskImage->job_task_id);

        $customer = $job->customer()->first();
        $contractor = $job->contractor()->first();
        if (Auth::user()->id !== $customer->id) {

            if ($job->status != 'bid.in_progress' && $job->status != 'bid.initiated') {
                $job->customer()->first()->notify(new TaskImageDeleted());
            }

        }
        if (Auth::user()->id !== $contractor->id) {
            $job->contractor()->first()->notify(new TaskImageDeleted());
        }

        if ($job->contractor_id !== $jobTask->contractor_id) {
            $jobTask->contractor()->first()->notify(new TaskImageDeleted());
        }
    }
}
