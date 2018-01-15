<?php

namespace App\Http\Controllers;

use App\Notifications\NotifySubOfTaskToBid;
use App\Notifications\NotifySubOfAcceptedBid;
use App\Notifications\NotifyCustomerThatBidIsFinished;
use App\Notifications\NotifyContractorOfAcceptedBid;
use App\Notifications\NotifyContractorOfDeclinedBid;
use App\Notifications\NotifyContractorOfSubBid;
//use Illuminate\Notifications\Notifiable;
use App\Task;
use App\Job;
use App\Contractor;
use App\Customer;
use App\User;
use App\BidContractorJobTask;
use Illuminate\Http\Request;
use App\Services\RandomPasswordService;
use Illuminate\Support\Facades\DB;

use Auth;
use Log;

class TaskController extends Controller
{

//    use Notifiable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        echo 'hello world';
    }

    public function bidContractorJobTasks()
    {
        $bidTasks = Auth::user()->contractor()->bidJobTasks()->get();
        return view('tasks.index')->with(['tasks' => $bidTasks]);
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
            ->update(['status' => 'bid_task.sent']);
        } catch (\Exception $e) {
            Log::error('Update Job Task: ' . $e->getMessage());
            return response()->json(["message"=>"Couldn't save record.","errors"=>["error" =>[$e->getMessage()]]], 404);
        }
        $gContractor = Contractor::find($bidContractorJobTask->task()->first()->contractor_id)->user()->first();
        $gContractor->notify(new NotifyContractorOfSubBid($gContractor, Contractor::find($bidContractorJobTask->contractor_id)->user()->first()->name));

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
        // TODO: fix this query so it uses eloqouent so that it does not face sql injection attacks
        // remove the task from the job
        $statement = "Delete from job_task where job_id = ".$request->jobId." AND task_id = ".$request->taskId;
        $totalDrugs = DB::delete($statement);
//        \App\Task::destroy($request->taskId);
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
        if ($contractor->checkIfContractorSetBidForATask($contractor->id, $taskId, $jobId)) {
            $contractor->addContractorToBidForJobTable($contractor->id, $taskId, $jobId);
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

    public function notify(Request $request)
    {
        $phone = $request->phone;
        $email = $request->email;
        $taskId = $request->taskId;
        $jobId = $request->jobId;
        $name = $request->name;


//        return gettype($jobId);

        // check the validation
        $validation = $this->validateRequest($email, $phone);
        if ($validation !== 'validationPassed') {
            return $validation;
        };

        if ($this
            ->checkIfNameIsDifferentButPhoneAndEmailExistInTheDatabase($name,
                $phone, $email)) {
            return "user may already exist in database";
        }

        // does the subcontractor exist?
        // if not then create a new one
        $userData = $this->checkIfSubContractorExits($email, $phone);


        $user = $userData[0];
        $userExists = $userData[1];

        $contractor = $user->contractor();

//        return $this->addBidEntryForTheSubContractor($contractor, $taskId, $jobId);

        // add an entry in to the contractor bid table so that the sub can bid on the task
        if ($this->addBidEntryForTheSubContractor($contractor, $taskId, $jobId) === false) {
            return "task already exists";
        }


        //   send a code in the notification to use when they sign up
        // generate token and save it
        $token = $user->generateToken(true);

        //   this code will redirect them to the page with information on the task
        // if so then send a notification to that contractor
        $user->notify(new NotifySubOfTaskToBid($taskId, $user, $token, $userExists));


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

    public function accept(Request $request)
    {
        $bidId = $request->bidId;
        $taskId = $request->taskId;
        $jobId = $request->jobId;
        $price = $request->price;
        $contractor_id = $request->contractorId;
        
        // accept bid task
        $bidContractorJobTask = $this->acceptBidContractorJobTask($bidId);

        if ($bidContractorJobTask === false) {
            return response()->json(["message"=>"Couldn't accept bid.","errors"=>["error" =>["Couldn't accept bid."]]], 404);
        }

        // set the sub price in the job task table
        $job = Job::find($jobId);
        $task = $job->tasks()->get()->where('id', '=', $taskId)->first();
        $task->pivot->sub_final_price = $price;
        // need to update the job task with the contractor who was chosen to do
        // that job task
        $task->pivot->contractor_id = $contractor_id;
        $task->pivot->status = 'bid_task.approved';
        $task->pivot->save();

        $data = ["price" => $price, "taskId" => $taskId];
        $data = json_encode($data);

        // notify sub contractor that his bid was approved
        $user = $bidContractorJobTask->contractor()->first()->user()->first();
        $user->notify(new NotifySubOfAcceptedBid($bidContractorJobTask->task));

        return $data;
    }

    public function acceptTask(Request $request)
    {
        $taskId = $request->taskId;
        $jobId = $request->jobId;
        $contractorId = $request->contractorId;

        // TODO: Determine if I want to accept each individual task and not just the job as a whole
//        DB::table('job_task')
//            ->where('job_id', $jobId)
//            ->where('task_id', $taskId)
//            ->where('contractor_id', $contractorId)
//            ->update(['status' => config('app.taskIsAccepted')]);

        $user_id = Contractor::where('id', $contractorId)
            ->first()
            ->user_id;
        $user = User::where('id', $user_id)->first();

        $user->notify(new NotifyContractorOfAcceptedBid());
    }

    public function acceptJob(Request $request)
    {
        $jobId = $request->jobId;
        $contractorId = $request->contractorId;

        $job = Job::find($jobId);
        $job->status = config('app.jobIsAccepted');
        $job->save();

        $user_id = Contractor::where('id', $contractorId)
            ->get()
            ->first()
            ->user_id;
        $user = User::where('id', $user_id)->get()->first();

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

        DB::table('job_task')
            ->where('job_id', $jobId)
            ->where('task_id', $taskId)
            ->update(['cust_final_price' => $price]);

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

    public function finishedBidNotification(Request $request)
    {
        $jobId = $request->jobId;
        $customerId = $request->customerId;

//        return $customerId;

        $user_id = Customer::where('id', $customerId)->get()->first()->user_id;
        $user = User::where('id', $user_id)->get()->first();

        $job = Job::find($jobId);
        $this->switchJobStatusToInProgress($job, config('app.bidIsFinished'));

//        return $user;

        $user->notify(new NotifyCustomerThatBidIsFinished());
    }

    public function addTask(Request $request)
    {
        $this->validate($request, [
            'taskName' => 'required|string',
            'taskPrice' => 'required|numeric',
            'subTaskPrice' => 'required|numeric',
            'start_date' => 'required|date'
        ]);
     
        $jobId = $request->jobId;
        $taskId = $request->taskId;
        $taskPrice = $request->taskPrice;
        $contractorId = $request->contractorId;
        $taskName = $request->taskName;
        $subTaskPrice = $request->subTaskPrice;
        $area = $request->area;
        $start_date = $request->start_date;

        // example of standard way to return errors for apis - we should standardize our errors to this
        //return response()->json(["message"=>"Couldn't save record.","errors"=>["error" =>[$e->getMessage()]]], 422);

        if ($request->taskExists) {
            // 1. add the task to the job task table
            $job = Job::find($jobId);
            $task = Task::find($taskId);
            $job->tasks()->attach($task);

            $task = $this->updateTaskWithNewValuesIfValuesAreDifferent($task, $subTaskPrice, $taskPrice);

            $this->updateJobTaskTable($job, $taskId, $jobId, $taskPrice, $contractorId, $area, $start_date);

            $this->switchJobStatusToInProgress($job, config('app.bidIsInProgress'));

            return $job->tasks()->where('id', '=', $taskId)->get()[0];
        } else {

            $task = Task::create(
                [
                    'name' => $taskName,
                    'standard_task_id' => null,
                    'contractor_id' => $contractorId,
                    'proposed_cust_price' => $taskPrice,
                    'proposed_sub_price' => $subTaskPrice
                ]
            );

            // Add the task to the task table for the given contractor
            $job = Job::find($jobId);
            $job->tasks()->attach($task);

            $this->updateJobTaskTable($job, $task->id, $jobId, $taskPrice, $contractorId, $area, $start_date);

            $this->switchJobStatusToInProgress($job, config('app.bidIsInProgress'));

            return $job->tasks()->where('id', '=', $task->id)->get()[0];
        }

        // change the status of the job to pending
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

    public function updateJobTaskTable($job, $taskId, $jobId, $taskPrice, $contractorId, $area, $start_date)
    {
        $jt = $job->tasks()->where("task_id", "=", $taskId)->where("job_id", "=", $jobId)->get()[0];
        $jt->pivot->status = config('app.taskIsInitiated');
        $jt->pivot->cust_final_price = $taskPrice;
        $jt->pivot->sub_final_price = 0;
        $jt->pivot->contractor_id = $contractorId;
        $jt->pivot->area = $area;
        $jt->pivot->start_date = $start_date;
        $jt->pivot->save();
    }

    protected function acceptBidContractorJobTask($id)
    {
        // get model
        $bidContractorJobTask = BidContractorJobTask::find($id);

        $bidContractorJobTask->accepted = true;

        try {
            $bidContractorJobTask->save();
        } catch (\Excpetion $e) {
            Log::error('Accept Bid: ' . $e->getMessage());
            return false;
        }
        return $bidContractorJobTask;
    }
}
