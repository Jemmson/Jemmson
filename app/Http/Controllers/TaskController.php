<?php

namespace App\Http\Controllers;

use App\Notifications\NotifySubOfTaskToBid;
use App\Notifications\NotifySubOfAcceptedBid;
use App\Notifications\NotifyCustomerThatBidIsFinished;
use App\Notifications\NotifyContractorOfAcceptedBid;
use App\Notifications\NotifyContractorOfDeclinedBid;
use App\Notifications\NotifyContractorOfSubBid;
use App\Notifications\TaskFinished;
use App\Notifications\TaskWasNotApproved;
use App\Notifications\TaskApproved;
//use Illuminate\Notifications\Notifiable;
use App\Task;
use App\Job;
use App\Contractor;
use App\Customer;
use App\User;
use App\BidContractorJobTask;
use App\JobTask;
use Illuminate\Http\Request;
use App\Services\RandomPasswordService;
use App\Services\SanatizeService;
use Illuminate\Support\Facades\DB;

use Auth;
use Log;

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
        $bidTasks = Auth::user()->contractor()->first()->bidContractorJobTasks()->with(['task.jobs', 'jobTask'])->get();
        return view('tasks.index')->with(['tasks' => $bidTasks]);
    }

    /**
     * Get all bid tasks from the currently logged in contractor
     *
     * @return void
     */
    public function bidTasks()
    {
        $bidTasks = Auth::user()->contractor()->first()->bidContractorJobTasks()->with(['task.jobs', 'jobTask'])->get();
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
        //
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
            return response()->json(["message"=>"Couldn't find record.","errors"=>["error" =>["Couldn't find record."]]], 404);
        }else if($request->bid_price == 0) {
            return response()->json(["message"=>"Price needs to be greater than 0.","errors"=>["error" =>[""]]], 412);
        }

        $bidContractorJobTask->bid_price = $request->bid_price;
        $jobTask = $bidContractorJobTask->jobTask()->first();
        // doesn't work since no default 'id' found
        // $jobTask->status = 'bid_task.sent';

        try {
            $bidContractorJobTask->save();
        } catch (\Exception $e) {
            Log::error('Update Bid Task:' . $e->getMessage());
            return response()->json(["message"=>"Couldn't save record.","errors"=>["error" =>[$e->getMessage()]]], 404);
        }

        try {
            \DB::table('job_task')
            ->where([['job_id', '=', $jobTask->job_id], ['task_id', '=', $jobTask->task_id]])
            ->update(['status' => __('bid_task.bid_sent')]);
        } catch (\Exception $e) {
            Log::error('Update Job Task: ' . $e->getMessage());
            return response()->json(["message"=>"Couldn't save record.","errors"=>["error" =>[$e->getMessage()]]], 404);
        }
        $gContractor = User::find($bidContractorJobTask->task()->first()->contractor_id);
        $gContractor->notify(new NotifyContractorOfSubBid(Job::find($jobTask->job_id), User::find($bidContractorJobTask->contractor_id)->name, $gContractor));

        return response()->json(["message"=>"Success"], 200);
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
        $jobTask = JobTask::where('job_id', $request->jobId)->where('task_id', $request->taskId)->firstOrFail();
        
        try {
            $jobTask->delete();
        } catch (\Excpetion $e) {
            Log::error('Deleteing JobTask: ' . $e->getMessage());
        }

        $job->subtractPrice($jobTask->cust_final_price);
    }

    public function checkIfSubContractorExits($email, $phone)
    {
        $user = User::where('email', $email)->orWhere('phone', $phone)->first();
        $result = count($user);
        $userExists = false;

        if ($result !== 1) {
            $user = $this->createNewUser($email, $phone);
            $userExists = false;
        } else {
            $userExists = true;
        }

        $userData = [$user, $userExists];

        return $userData;
    }

    public function createNewUser($email, $phone)
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
                'name' => explode('@', $email)[0],
                'email' => $email,
                'phone' => $phone,
                'usertype' => 'contractor',
                'password_updated' => false,
                'password' => bcrypt($pass),
            ]
        );

        Contractor::create(
            [
                'user_id' => $user->id
            ]
        );

        return $user;

    }

    public function addBidEntryForTheSubContractor($contractor, $taskId, $jobId)
    {
        if ($contractor->checkIfContractorSetBidForATask($contractor->user_id, $taskId, $jobId)) {
            $contractor->addContractorToBidForJobTable($contractor->user_id, $taskId, $jobId);
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
            'taskId' => 'required',
        ]);

        $phone = SanatizeService::phone($request->phone);
        $email = $request->email;
        $taskId = $request->taskId;
        $jobId = $request->jobId;
        $name = $request->name;

        if ($this
            ->checkIfNameIsDifferentButPhoneAndEmailExistInTheDatabase($name,
                $phone, $email)) {
            return response()->json(["message"=>"Contractor Exists Select Them From the Dropdown List.","errors"=>["error" => "error"]], 422);
        }

        // does the subcontractor exist?
        // if not then create a new one
        $userData = $this->checkIfSubContractorExits($email, $phone);


        $user = $userData[0];
        $userExists = $userData[1];

        $contractor = $user->contractor()->first();

        // add an entry in to the contractor bid table so that the sub can bid on the task
        if ($this->addBidEntryForTheSubContractor($contractor, $taskId, $jobId) === false) {
            return response()->json(["message"=>"Task Already Exists.","errors"=>["error" => "Task Already Exists."]], 422);
        }

        //   this code will redirect them to the page with information on the task
        // if so then send a notification to that contractor
        $user->notify(new NotifySubOfTaskToBid($taskId, $user));


        $bidPrices = Task::getBidPrices($jobId);


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
        $taskId = $request->taskId;
        $jobId = $request->jobId;
        $price = $request->price;
        $contractorId = $request->contractorId;
        
        // accept bid task
        $bidContractorJobTask = BidContractorJobTask::find($bidId);

        if ($bidContractorJobTask === false) {
            return response()->json(["message"=>"Couldn't accept bid.","errors"=>["error" =>["Couldn't accept bid."]]], 404);
        }

        // set the sub price in the job task table
        $job = Job::find($jobId);
        $task = $job->tasks()->get()->where('id', '=', $taskId)->first();
        $jobTask = $task->jobTask()->first();

        $jobTask->sub_final_price = $price;
        $jobTask->contractor_id = $contractorId;
        $jobTask->bid_id = $bidContractorJobTask->id; // accepted bid
        $jobTask->status = __('bid_task.accepted');

        try {
            $jobTask->save();
        } catch (\Exception $e) {
            Log::error('Updating Job Task: ' . $e->getMessage());
            return response()->json(["message"=>"Couldn't accept Job Task bid.","errors"=>["error" =>["Couldn't accept bid."]]], 404);
        }
        
        // notify sub contractor that his bid was approved
        $user = User::find($contractorId);
        $user->notify(new NotifySubOfAcceptedBid($bidContractorJobTask->task, $user));

        return response()->json(["message"=>"Success"], 200);
    }

    /**
     * Notify General Contractor of accepted job bid
     *
     * @param Request $request
     * @return void
     */
    public function acceptTask(Request $request)
    {
        $taskId = $request->taskId;
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
        $task = Task::find($request->id);
        $jobTask = $task->jobTask()->first();
        $jobTask->status = __("bid_task.approved_by_general");

        $customer = User::find(Job::find($jobTask->job_id)->customer_id);
        $subContractor = User::find($jobTask->contractor_id);

        try {
            $jobTask->save();
        } catch (\Exception $e) {
            Log::error('Updating Task Status: ' . $e->getMessage());
            return response()->json(["message"=>"Couldn't update status task.","errors"=>["error" =>[$e->getMessage()]]], 404);
        }

        $customer->notify(new TaskFinished($task, true, $customer));
        $subContractor->notify(new TaskApproved($task, $subContractor));

        return response()->json(["message"=>"Success"], 200);
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

        if ($request->task !== null) {
            // request comes from the bid task page
            // main object is not the task itself 
            $id = $request->task_id;
        } else {
            $id = $request->id;
        }

        $task = Task::find($id);
        $jobTask = $task->jobTask()->first();

        if ($request->current_user_id === $jobTask->contractor_id && $request->current_user_id === $task->contractor_id) {
            $finishedByGeneral = true;
            $jobTask->status = __("bid_task.finished_by_general");
        } else {
            $jobTask->status = __("bid_task.finished_by_sub");
        }

        $customer = User::find(Job::find($jobTask->job_id)->customer_id);
        $generalContractor = User::find($task->contractor_id);

        try {
            $jobTask->save();
        } catch (\Exception $e) {
            Log::error('Updating Task Status: ' . $e->getMessage());
            return response()->json(["message"=>"Couldn't update status task bid.","errors"=>["error" =>[$e->getMessage()]]], 404);
        }

        if ($finishedByGeneral) {
            // is general contractor
            $customer->notify(new TaskFinished($task, true, $customer));
        } else {
            $generalContractor->notify(new TaskFinished($task, false, $generalContractor));
        }

        return response()->json(["message"=>"Success"], 200);
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

        $task = Task::find($request->id);
        $task->updateStatus(__('bid_task.reopened'));

        return response()->json(['message' => 'task reopened'], 200);
        // change the status of the job to pending
    }


    public function acceptJob(Request $request)
    {
        $jobId = $request->jobId;
        $contractorId = $request->contractorId;

        $job = Job::find($jobId);
        $job->status = __('job.accepted');
        $job->save();

        $user = User::find($contractorId);

        $user->notify(new NotifyContractorOfAcceptedBid());
    }

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

    public function updateCustomerPrice(Request $request)
    {
        $price = $request->price;
        $taskId = $request->taskId;
        $jobId = $request->jobId;
        $job = Job::find($jobId);
        $jobTask = JobTask::where('job_id', $jobId)
                            ->where('task_id', $taskId)
                            ->firstOrFail();
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
        $jobId = $request->jobId;

        DB::table('tasks')
            ->where('id', $taskId)
            ->update(['name' => $taskName]);

//        $data = ["price" => $price, "taskId" => $taskId];
//        $data = json_encode($data);
//        return $data;
    }

    /**
     * TODO: move to job controller
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

    public function addTask(Request $request)
    {
        
        $this->validate($request, [
            'taskName' => 'required|regex:/^[a-zA-Z0-9 .\-#,]+$/i',
            'taskPrice' => 'required|numeric',
            'subTaskPrice' => 'required|numeric',
            'start_when_accepted' => 'required',
            'start_date' => 'required_if:start_when_accepted,false|date|after:today'
        ]);

        $job_id = $request->jobId;
        $name = strtolower($request->taskName);

        $job = Job::find($job_id);
            
        // find or create a task
        $task = Task::firstOrCreate(['name' => $name, 'job_id' => $job_id, 'contractor_id' => $request->contractorId]);
        
        $task->proposed_cust_price = $request->taskPrice;
        $task->proposed_sub_price = $request->subTaskPrice;
        
        try {
            $task->save();
        } catch (\Exception $e) {
            Log::error('Add/Update Task: ' . $e->getMessage());
            return response()->json(["message"=>"Couldn't add/update task.","errors"=>["error" =>[$e->getMessage()]]], 404);
        }
        
        // update or create job task for task
        $jobTask = JobTask::firstOrCreate(['job_id' => $job_id, 'task_id' => $task->id]);
        $this->updateJobTask($request, $task->id, $jobTask);
        // add to total job price
        $job->addPrice($request->taskPrice);
        
        $this->switchJobStatusToInProgress($job, __('bid.in_progress'));

        return response()->json($job->tasks()->get(), 200);
        // change the status of the job to pending
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
            'id' => 'required',
        ]);


        $task = Task::find($request->id);
        $jobTask = $task->jobTask()->first();

        $jobTask->updateStatus(__('bid_task.reopened'));

        // notify
        $contractor = User::find($task->contractor_id);
        $contractor->notify(new TaskWasNotApproved($task, $contractor, $request->message));

        if ($jobTask->contractor_id !== $task->contractor_id) {
            $subContractor = User::find($jobTask->contractor_id);
            $subContractor->notify(new TaskWasNotApproved($task, $subContractor, $request->message));
        }

        return response()->json($task, 200);
    }

    public function updateTaskWithNewValuesIfValuesAreDifferent ($task, $subTaskPrice, $taskPrice)
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
        $jobTask->job_id = $request->jobId;
        $jobTask->task_id = $task_id;
        $jobTask->status = __('bid_task.initiated');
        $jobTask->cust_final_price = $request->taskPrice;
        $jobTask->sub_final_price = 0;
        $jobTask->contractor_id = $request->contractorId;
        $jobTask->details = $request->details;
//        $jobTask->area = $request->area;
        if ($request->start_when_accepted) {
            $jobTask->start_when_accepted = true;
            $jobTask->start_date = \Carbon\Carbon::now();
        } else {
            $jobTask->start_date = $request->start_date;
        }
        $jobTask->save();
    }
}
