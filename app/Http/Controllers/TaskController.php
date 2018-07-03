<?php

namespace App\Http\Controllers;

use App\Notifications\NotifySubOfTaskToBid;
use App\Notifications\NotifySubOfAcceptedBid;
use App\Notifications\NotifyContractorOfAcceptedBid;
use App\Notifications\NotifyContractorOfSubBid;
use App\Notifications\TaskFinished;
use App\Notifications\TaskWasNotApproved;
use App\Notifications\TaskApproved;
use App\Notifications\TaskReopened;
use App\Notifications\UploadedTaskImage;
use App\Notifications\TaskImageDeleted;
use App\Task;
use App\Job;
use App\Contractor;
use App\Customer;
use App\User;
use App\BidContractorJobTask;
use App\JobTask;
use App\TaskImage;
use Illuminate\Http\Request;
use App\Services\RandomPasswordService;
use App\Services\SanatizeService;
use Illuminate\Support\Facades\DB;

use Auth;
use Log;
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
        $bidTasks = Auth::user()->contractor()->first()->bidContractorJobTasks()->with(['jobTask.job', 'jobTask.task'])->get();
        return view('tasks.index')->with(['tasks' => $bidTasks]);
    }

    /**
     * Get all bid tasks from the currently logged in contractor
     *
     * @return void
     */
    public function bidTasks()
    {
        $bidTasks = Auth::user()->contractor()->first()->bidContractorJobTasks()->with(['jobTask.job', 'jobTask.task', 'jobTask.images'])->get();
        return response()->json($bidTasks, 200);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function getJobTask(JobTask $jobTask)
    {
        Log::debug($jobTask);
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

        $job->subtractPrice(($jobTask->cust_final_price * $jobTask->qty));
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
            'email' => 'required|email',
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
            return response()->json(["message" => "Not a valid user.", "errors" => ["error" => "No valid user."]], 422);
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

        // set the sub price in the job task table
        $job = Job::find($jobId);
        $jobTask = JobTask::find($jobTaskId);
        $task = $jobTask->task()->first();

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
            $jobTask->cust_final_price = $price;
            $jobTask->save();
        } catch (\Excpetion $e) {
            Log::error('Updating JobTask: ' . $e->getMessage);
        }

        $job->addPrice($price - $oldPrice);

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

    public function addTask(Request $request)
    {

        // error handling

        $this->validate($request, [
            'taskName' => 'required|regex:/^[a-zA-Z0-9 .\-#,]+$/i',
            'taskPrice' => 'required|numeric',
            'subTaskPrice' => 'required|numeric',
            'start_when_accepted' => 'required',
            //            'sub_sets_own_price_for_job' => 'required',
            'start_date' => 'required_if:start_when_accepted,false|date|after:today',
            'qty' => 'numeric',
            'qtyUnit' => 'nullable|string'
        ]);

        if (!$this->isPriceGtE($request->taskPrice, $request->subTaskPrice)) {
            return response()->json([
                "message" => "Unit price for customer needs to be greater than or equal to Unit Price for Sub",
                "errors" => ["error" => ['Unit price for customer needs to be greater than or equal to Unit Price for Sub']]], 422);
        }

        // Get the job to add the task to, contractor and task name
        $job_id = $request->jobId;
        $job = Job::find($job_id);
        $name = strtolower($request->taskName);
        $contractor_id = $request->contractorId;


        // Does the task exist?
        // Does the task exist for the contractor?
        // if both are false then the task does not exist.

        // trying to find if the added task currently exists for the contractor
        $task = Task::whereRaw('LOWER(`name`) = ? ', $name)->where('contractor_id', $contractor_id)->first();
        Log::debug($task);

        // if the task does not exist then create a new task for the contractor.
        // it could be a new task that the contractor does not have assccoiated with them
        // it could be an existing task that the contractor has associated with him
        // it could also be an existing task that is not associated with the contractor

        // if the task is empty then then a new task for the contractor needs to be added
        // else the task for that contractor exists.
        // if the task exists for the contractor then was it selected from the drop down?
        // if it was selected from the drop down was the standard price changed?
        // if the standard price was changed then does the contractor want a new standard price?
        // if the contractor selected no then dont update the task tables price.
        // if the contractor selected yes then update the task tables price.


        // ****************************
        // NEW TASK
        // ****************************
        if (empty($task)) {
            $task = new Task;
            $task->name = $name;
            $task->contractor_id = $contractor_id;


            // ****************************
            // Set Task Price
            // ****************************
            $task->proposed_cust_price = $request->taskPrice;  // set customer price


        } else {
            // ****************************
            // TASK ALREADY EXISTS - Update Task Price
            // ****************************
            if ($request->changePrice) {
                $task->proposed_cust_price = $request->taskPrice;  // set customer price
            }

        }


        // ****************************
        // Set Job Price
        // ****************************
        if (!empty($request->qty)) {
            $job->addPrice(($request->taskPrice * $request->qty));
        } else {
            $job->addPrice(($request->taskPrice * 1));
        }


        // ****************************
        // Sub Sets own Price for job
        //
        // if you want to have the sub set their own price then
        // $request->sub_sets_own_price_for_job == true
        // if the subTaskPrice = 0 then $task->proposed_sub_price = 0;
        // ****************************
        if (!$request->sub_sets_own_price_for_job || $request->subTaskPrice == 0) {       // set sub price
            $task->proposed_sub_price = 0;
        } else {
            $task->proposed_sub_price = $request->subTaskPrice;
        }

        $task->job_id = $job_id;  // set job ID


        // ****************************
        // Save the Changes to the Task Table
        // ****************************
        try {
            $task->save();
        } catch (\Exception $e) {
            Log::error('Add/Update Task: ' . $e->getMessage());
            return response()->json([
                "message" => "Couldn't add/update task.",
                "errors" => ["error" => [$e->getMessage()]]], 404);
        }


        // ****************************
        // Set Job Task Price
        // ****************************

        // Once the Standard task has been updated the job task needs to be updated or created.
        // update or create job task for task
        // check if a jobtask has this job id and this task ID. if it does not then create a job task with these
        // values
        $jobTask = JobTask::firstOrCreate(['job_id' => $job_id, 'task_id' => $task->id]);
        $this->updateJobTask($request, $task->id, $jobTask);

        $this->switchJobStatusToInProgress($job, __('bid.in_progress'));

        return response()->json($job->tasks()->get(), 200);


        // not sure the point of this but it seems to get the first task of the job and then
        // subtracts the price of the contractors price times the quantity from the jobs total
        // not sure why this is helpful.
//        if ($request->taskId == -1) {
//            $jobTask = $task->jobTask()->first();
//            $job->subtractPrice(($jobTask->cust_final_price * $jobTask->qty));
//        }
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

    public function switchJobStatusToInProgress($job, $message)
    {
        $job->status = $message;
        $job->save();
    }

    public function updateJobTask($request, $task_id, $jobTask)
    {

        Log::info('quantity unit: ' . $request->qtyUnit);

        if (!$request->sub_sets_own_price_for_job) {
            $jobTask->sub_sets_own_price_for_job = 0;
        } else {
            $jobTask->sub_sets_own_price_for_job = 1;
        }

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
            $job->customer()->first()->notify(new UploadedTaskImage());
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
            $job->customer()->first()->notify(new TaskImageDeleted());
        }
        if (Auth::user()->id !== $contractor->id) {
            $job->contractor()->first()->notify(new TaskImageDeleted());
        }

        if ($job->contractor_id !== $jobTask->contractor_id) {
            $jobTask->contractor()->first()->notify(new TaskImageDeleted());
        }
    }

    /**
     *
     * @param Float $priceToCheck
     * @param Float $price
     * @return boolean
     */
    private function isPriceGtE(Float $priceToCheck, Float $price)
    {
        return $priceToCheck >= $price;
    }
}
