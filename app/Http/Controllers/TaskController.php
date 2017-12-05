<?php

namespace App\Http\Controllers;

use App\Notifications\NotifySubOfTaskToBid;
//use Illuminate\Notifications\Notifiable;
use App\Task;
use App\Contractor;
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
    public function destroy(Task $task)
    {
        //
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

//    use App\Contractor; use App\Tasks; use App\User; use App\Task; $user = User::create(['name' => 'jim', 'email' => 'jim4@jim.com', 'phone' => '1234123127', 'usertype' => 'contractor', 'password_updated' => false, 'password' => bcrypt('1234'),]); $contractor = Contractor::create(['user_id' => $user->id]);$task = Task::create(["name" => "change orings2"]);$contractor->tasks()->attach($task);


        Contractor::create(
            [
                'user_id' => $user->id
            ]
        );

        return $user;

    }

    public function getBidPrices($jobId)
    {
        $bidPrices = DB::select("select 
                        bid_contractor_job_task.contractor_id, 
                        bid_contractor_job_task.task_id, 
                        bid_contractor_job_task.bid_price 
                      from 
                        contractors 
                      inner join 
                        bid_contractor_job_task 
                      on 
                        bid_contractor_job_task.contractor_id=contractors.id 
                      where 
                        bid_contractor_job_task.job_id=$jobId");

        $contractorNames = [];
//
        foreach ($bidPrices as $bidPrice) {
            $contractorName = DB::select("select
                        users.name
                        from users
                        inner join contractors
                        on users.id = contractors.user_id
                        where contractors.id = $bidPrice->contractor_id");
            array_push($contractorNames, $contractorName);
        }

        $bidPriceLength = sizeof($bidPrices);
        for ($i = 0; $i < $bidPriceLength; $i++) {
            $bidPrices[$i]->contractorName = $contractorNames[$i];
        }

        $bidPrices = json_encode($bidPrices);

        return $bidPrices;

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

//        return gettype($jobId);

        // check the validation
        $validation = $this->validateRequest($email, $phone);
        if ($validation !== 'validationPassed') {
            return $validation;
        };

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


        $bidPrices = $this->getBidPrices($jobId);

        return $bidPrices;
    }
}
