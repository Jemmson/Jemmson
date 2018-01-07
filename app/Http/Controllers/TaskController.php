<?php

namespace App\Http\Controllers;

use App\Notifications\NotifySubOfTaskToBid;
use App\Notifications\NotifySubOfAcceptedBid;
use App\Notifications\NotifyCustomerThatBidIsFinished;
//use Illuminate\Notifications\Notifiable;
use App\Task;
use App\Job;
use App\Contractor;
use App\Customer;
use App\User;
use Illuminate\Http\Request;
use App\Services\RandomPasswordService;
use Illuminate\Support\Facades\DB;

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
        //

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
        //
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

        $contractor = $user->contractors()->get()[0];

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
        DB::table('bid_contractor_job_task')
            ->where('job_id', $jobId)
            ->where('task_id', $taskId)
            ->update(['accepted' => 0]);
        DB::table('bid_contractor_job_task')
            ->where('id', $bidId)
            ->update(['accepted' => 1]);


        // set the sub price in the job task table
        $job = Job::find($jobId);
        $task = $job->tasks()->get()->where('id', '=', $taskId)->first();
        $task->pivot->sub_final_price = $price;
        $task->pivot->save();

        $data = ["price" => $price, "taskId" => $taskId];
        $data = json_encode($data);
        return $data;
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

        $jobId = $request->jobId;
        $taskId = $request->taskId;
        $taskPrice = $request->taskPrice;
        $contractorId = $request->contractorId;
        $taskName = $request->taskName;
        $subTaskPrice = $request->subTaskPrice;

        if ($request->taskExists) {
            // 1. add the task to the job task table
            $job = Job::find($jobId);
            $task = Task::find($taskId);
            $job->tasks()->attach($task);

            $task = $this->updateTaskWithNewValuesIfValuesAreDifferent($task, $subTaskPrice, $taskPrice);

            $this->updateJobTaskTable($job, $taskId, $jobId, $taskPrice, $contractorId);

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

            $this->updateJobTaskTable($job, $task->id, $jobId, $taskPrice, $contractorId);

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

    public function updateJobTaskTable($job, $taskId, $jobId, $taskPrice, $contractorId)
    {
        $jt = $job->tasks()->where("task_id", "=", $taskId)->where("job_id", "=", $jobId)->get()[0];
        $jt->pivot->status = config('app.taskIsInitiated');
        $jt->pivot->cust_final_price = $taskPrice;
        $jt->pivot->sub_final_price = 0;
        $jt->pivot->contractor_id = $contractorId;
        $jt->pivot->save();
    }
}
