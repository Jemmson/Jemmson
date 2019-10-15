<?php

namespace App\Http\Controllers;

use App\ContractorSubcontractorPreferredPayment;
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
use App\Task;
use App\Job;
use App\Contractor;
use App\Customer;
use App\User;
use App\ContractorCustomer;
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
use App\Traits\ConvertPrices;
use Cloudinary;
use Cloudinary\Api;

class TaskController extends Controller
{

    use ConvertPrices;

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
        $bidTasks = Auth::user()->contractor()->first()->bidContractorJobTasks()->
        with([
            'jobTask.job',
            'jobTask.task',
            'jobTask.task.contractor'
        ])->get();
        return view('tasks.index')->with(['tasks' => $bidTasks]);
    }

    private function getBidContractorJobTasks()
    {
        $bidContractorJobTasksResults = [];
        $bidContractorJobTasks = BidContractorJobTask::where('contractor_id', '=', Auth::user()->getAuthIdentifier())->get();

        foreach ($bidContractorJobTasks as $task) {
            $jtResults = [];
            $jt = JobTask::where('id', '=', $task->job_task_id)->get()->first();

            $jobResults = [];
            $job = Job::where('id', '=', $jt->job_id)->get()->first();
            array_push($jobResults, [
                "id" => $job->id,
                "customer_id" => $job->customer_id,
                "contractor_id" => $job->contractor_id,
                "location_id" => $job->location_id,
                "job_name" => $job->job_name,
                "status" => $job->status,
                "completed_bid_date" => $job->completed_bid_date,
                "agreed_start_date" => $job->agreed_start_date,
                "agreed_end_date" => $job->agreed_end_date,
                "actual_end_date" => $job->actual_end_date,
                "deleted_at" => $job->deleted_at,
                "created_at" => $job->created_at,
                "updated_at" => $job->updated_at,
                "declined_message" => $job->declined_message,
                "paid_with_cash_message" => $job->paid_with_cash_message
            ]);

            $taskResults = [];
            $singleTask = Task::where('id', '=', $jt->task_id)->get()->first();

            $contractorResults = [];




            $contractor = Contractor::where('user_id', '=', $job->contractor_id)->get()->first();
            array_push($contractorResults, [
                "id" => $contractor->id,
                "user_id" => $contractor->user_id,
                "location_id" => $contractor->location_id,
                "company_name" => $contractor->company_name,
                "company_logo_name" => $contractor->company_logo_name,
                "email_method_of_contact" => $contractor->email_method_of_contact,
                "sms_method_of_contact" => $contractor->sms_method_of_contact,
                "phone_method_of_contact" => $contractor->phone_method_of_contact
            ]);




            array_push($taskResults, [
                "id" => $singleTask->id,
                "name" => $singleTask->name,
                "contractor_id" => $singleTask->contractor_id,
                "sub_instructions" => $singleTask->sub_instructions,
                "customer_instructions" => $singleTask->customer_instructions,
                "contractor" => $contractorResults[0]
            ]);

            $imageResults = [];
            $images = TaskImage::where('job_task_id', '=', $task->job_task_id)->get();
            if (!empty($images->toArray())) {
                foreach ($images as $image) {

                    array_push($imageResults, [
                        "id" => $image->id,
                        "job_id" => $image->job_id,
                        "job_task_id" => $image->job_task_id,
                        "public_id" => $image->public_id,
                        "version" => $image->version,
                        "signature" => $image->signature,
                        "width" => $image->width,
                        "height" => $image->height,
                        "format" => $image->format,
                        "resource_type" => $image->resource_type,
                        "bytes" => $image->bytes,
                        "type" => $image->type,
                        "etag" => $image->etag,
                        "placeholder" => $image->placeholder,
                        "url" => $image->url,
                        "secure_url" => $image->secure_url,
                        "overwritten" => $image->overwritten,
                        "original_filename" => $image->original_filename,
                        "created_at" => $image->created_at,
                        "updated_at" => $image->updated_at,
                    ]);
                }
            }
            $locationResults = [];
            $location = Location::where('user_id', '=', $job->customer_id)->get()->first();
            array_push($locationResults, [
                "id" => $location->id,
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
                "long" => $location->long
            ]);

            array_push($jtResults, [
                "id" => $jt->id,
                "job_id" => $jt->job_id,
                "task_id" => $jt->task_id,
                "bid_id" => $jt->bid_id,
                "location_id" => $jt->location_id,
                "contractor_id" => $jt->contractor_id,
                "status" => $jt->status,
                "sub_final_price" => $jt->sub_final_price,
                "start_when_accepted" => $jt->start_when_accepted,
                "stripe" => $jt->stripe,
                "start_date" => $jt->start_date,
                "deleted_at" => $jt->deleted_at,
                "created_at" => $jt->created_at,
                "updated_at" => $jt->updated_at,
                "stripe_transfer_id" => $jt->stripe_transfer_id,
                "customer_message" => $jt->customer_message,
                "sub_message" => $jt->sub_message,
                "qty" => $jt->qty,
                "sub_sets_own_price_for_job" => $jt->sub_sets_own_price_for_job,
                "declined_message" => $jt->declined_message,
                "unit_price" => $jt->unit_price
            ]);


            if (!empty($jobResults)) {
                $jtResults[0]["job"] = $jobResults[0];
            } else {
                $jtResults[0]["job"] = null;
            }

            if (!empty($taskResults)) {
                $jtResults[0]["task"] = $taskResults[0];
            } else {
                $jtResults[0]["task"] = null;
            }

            if (!empty($imageResults)) {
                $jtResults[0]["images"] = $imageResults;
            } else {
                $jtResults[0]["images"] = [];
            }

            if (!empty($locationResults)) {
                $jtResults[0]["location"] = $locationResults[0];
            } else {
                $jtResults[0]["location"] = null;
            }


            array_push($bidContractorJobTasksResults, [
                "id" => $task->id,
                "contractor_id" => $task->contractor_id,
                "job_task_id" => $task->job_task_id,
                "bid_price" => $task->bid_price,
                "created_at" => $task->created_at,
                "updated_at" => $task->updated_at,
                "status" => $task->status,
                "proposed_start_date" => $task->proposed_start_date,
                "bid_description" => $task->bid_description,
                "accepted" => $task->accepted,
                "payment_type" => $task->payment_type,
                "job_task" => $jtResults[0]
            ]);
        }

        return $bidContractorJobTasksResults;

    }

    /**
     * Get all bid tasks from the currently logged in contractor
     *
     * @return void
     */
    public function bidTasks()
    {
        $bidTasks = $this->getBidContractorJobTasks();
        if (!empty($bidTasks)) {
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
                'job',
                'job.customer',
                'bidContractorJobTasks',
                'bidContractorJobTasks.contractor',
                'bidContractorJobTasks.contractor.contractor'
            ])->where('id', '=', $jobTaskId)->get();

        foreach ($jobTasks as $jt) {
            $jt->cust_final_price = $this->convertToDollars($jt->cust_final_price);
            $jt->sub_final_price = $this->convertToDollars($jt->sub_final_price);
            $jt->unit_price = $this->convertToDollars($jt->unit_price);
        }

        return $jobTasks;

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
    public function updateBidContractorJobTask(Request $request, $id)
    {
        $bidContractorJobTask = BidContractorJobTask::find($id);

        if ($bidContractorJobTask == null) {
            return response()->json(["message" => "Couldn't find record.", "errors" => ["error" => ["Couldn't find record."]]], 404);
        } else if ($request->bid_price == 0) {
            return response()->json(["message" => "Price needs to be greater than 0.", "errors" => ["error" => [""]]], 412);
        }

        $bidContractorJobTask->bid_price = $this->convertToCents($request->bid_price);
        $bidContractorJobTask->status = 'bid_task.bid_sent';
        $bidContractorJobTask->payment_type = $request->paymentType;
        $jobTask = $bidContractorJobTask->jobTask()->first();

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
     * @param \App\Task $task
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

        //
        $phone = SanatizeService::phone($request->phone);
        $email = $request->email;
        $jobTaskId = $request->jobTaskId;
        $name = $request;

        if (!empty($request->id)) {
            $user_sub = User::getUserByPhoneOrEmail($phone, $email);
        } else {
            $user_sub = User::find($request->id);
        }
        $qb = new Quickbook();
        // TODO: Not sure about this logic.
        // TODO: Should be if a user is not a contractor then create a contractor.
        // TODO: If a user is a customer then that should not throw an error
        //   becuase a user can be both a contractor and a customer
//           Have to resolve that a customer name does not have to be unique
//           in QB becuase more than one person can have the same name. Can
//           take into account middle name maybe. Can check that name is not
//           in the QB customer table for that contractor. Needs to be fixed though.
        if (empty($user_sub)) {
            // if no user found create one
            if ($qb->isContractorThatUsesQuickbooks()) {
                $qbc = QuickbooksContractor::ContractorExists($request);
                if ($qbc) {
                    if ($qbc->phone !== $phone) {
                        $qb->UpdateSubPhoneNumberInQuickbooks($phone, $request->quickbooksId);
                    }
                    $user = new User();
                    $user_sub = $user->addExistingQBContractorToJemTable($request);
                } else {
                    $resultingCustomerObj = Quickbook::addNewContractorToQuickBooks($request);
                    QuickbooksContractor::addContractorToQuickbooksContractorTable($request, $resultingCustomerObj);
                    $user = new User();
                    $user_sub = $user->addNewContractorToJemTable($request, $resultingCustomerObj->Id);
                }
            } else {
                $user_sub = $this->createNewUserFirstNameAndLastName(
                    $request->firstName, $request->lastName, $email, $phone, $request->companyName
                );
            }
        } else if ($user_sub->usertype === 'customer') {
            // return if the user is a customer
            return response()->json(["message" => "This person is a customer in the system and can not also be a contractor", "errors" => ["error" => "No valid user."]], 422);
        } else {
            if ($user_sub->phone !== $phone) {
                $user_sub->updatePhoneNumber($phone);
                if ($qb->isContractorThatUsesQuickbooks()) {
                    $qb->UpdateSubPhoneNumberInQuickbooks($phone, $request->quickbooksId);
                    $qbc = QuickbooksContractor::where('quickbooks_id', '=', $request->quickbooksId)->
                    where('contractor_id', '=', Auth::user()->getAuthIdentifier())->get()->first();
                    $qbc->primary_phone = $phone;
                    try {
                        $qbc->save();
                    } catch (\Exception $e) {
                        return response()->json([
                            'message' => $e->getMessage(),
                            'code' => $e->getCode()
                        ], 200);
                    }
                }
            }
        }

        // add bid entry for the sub
        // add an entry in to the contractor bid table so that the sub can bid on the task
        $contractor = $user_sub->contractor()->first();
        $jobTask = JobTask::find($jobTaskId);
        $subBid = $this->addBidEntryForTheSubContractor($contractor, $jobTaskId, $jobTask->task_id);
        if (!$subBid) {
            return response()->json(["message" => "Task Already Exists.", "errors" => ["error" => "Task Already Exists."]], 422);
        }

        // adding a preferred payment entry for contractor for a given task
        $ccspp = ContractorSubcontractorPreferredPayment::where('bid_contractor_job_task_id', '=', $subBid->id);
        if (empty($ccspp->id)) {
            $ccspp = new ContractorSubcontractorPreferredPayment();

            $ccspp->job_task_id = $jobTask->id;
            $ccspp->contractor_id = Auth::user()->getAuthIdentifier();
            $ccspp->sub_id = $user_sub->id;
            $ccspp->contractor_preferred_payment_type = $request->paymentType;

        } else {
            $ccspp->contractor_preferred_payment_type = $request->paymentType;
        }

        try {
            $ccspp->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        // this code will redirect them to the page with information on the task
        // if so then send a notification to that contractor
        $user_sub->notify(new NotifySubOfTaskToBid($jobTask->task_id, $user_sub));

        return Task::getBidPrices($jobTask->job_id);
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
     * @return void
     */
    public function accept(Request $request)
    {
        $bidId = $request->bidId;
        $jobTaskId = $request->jobTaskId;
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
                $c->status = 'bid_task.accepted';
            } else {
                $c->accepted = false;
                if ($c->status == 'bid_task.accepted') {
                    $c->status = 'bid_task.bid_sent';
                }
            }
            $c->save();
        });

        // set the sub price in the job task table
        $jobTask = JobTask::find($jobTaskId);
        $task = $jobTask->task()->first();

        $allContractorsForJobTask = BidContractorJobTask::select()->where("job_task_id", "=", $jobTaskId)->get();

        $allContractorsForJobTask->map(function ($con) use ($bidId, $task) {
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

        $task = Task::find($request->taskId);
        $job = Job::find($request->jobId);
        $customer = User::find($request->customer_id);

        $contractorCustomer = ContractorCustomer::where('contractor_user_id', '=', $request->contractorId)
            ->where('customer_user_id', '=', $request->customer_id)->first();
        if ($contractorCustomer != null) {
            $customer_quickBooks_Id = $contractorCustomer->quickbooks_id;
        }

        if (!empty($task)) {
            $jobTask = new JobTask();
            $jobTask->createJobTask($request);
            if ($task->isTaskAQBLineItem($request->item_id)) {
                if ($job->hasAQuickbookEstimateBeenCreated()) {
                    $job->updateQuickBooksEstimate($task, $job, $jobTask);
                } else {
                    $estimate = $job->createQuickBooksEstimate($customer, $task, $job, $jobTask, $customer_quickBooks_Id);
                    $job->qb_estimate_id = $estimate->Id;
                    $job->save();
                }
            }
        } else {
            $task = new Task();
            $task->createTask($request);
            $jobTask = new JobTask();
            $jobTask->addToJobTask($job->id, $task->id, $request);

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

    public function updateJobTask($request, $task_id, $jobTask)
    {

        Log::info('quantity unit: ' . $request->qtyUnit);

        $jobTask->job_id = $request->jobId;
        $jobTask->task_id = $task_id;
        $jobTask->status = __('bid_task.initiated');
        $jobTask->cust_final_price = $this->convertToCents($request->taskPrice);
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
        $taskImage->job_task_id = $request->jobTaskId;
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
        $jobTask = JobTask::find($taskImage->job_task_id);

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
//        if (preg_match('/tasks\/(.*)$/', $taskImage->url, $matches)) {
//            $disk = Storage::disk('public');
//            try {
//                $disk->delete('tasks/' . $matches[1]);
//            } catch (\Exception $e) {
//                Log::error('Delete Image: ' . $e->getMessage());
//                return response()->json('Something Went Wrong', 422);
//            }
//
        $taskImage->delete();
//        }


        $job = Job::find($taskImage->job_id);
        $jobTask = JobTask::find($taskImage->job_task_id);

        $customer = $job->customer()->first();
        $contractor = $job->contractor()->first();
        if (
            (Auth::user()->id !== $customer->id) &&
            ($job->status != 'bid.in_progress' && $job->status != 'bid.initiated')
        ) {
            $job->customer()->first()->notify(new TaskImageDeleted());

        }
        if (Auth::user()->id !== $contractor->id) {
            $job->contractor()->first()->notify(new TaskImageDeleted());
        }

        if ($job->contractor_id !== $jobTask->contractor_id) {
            $jobTask->contractor()->first()->notify(new TaskImageDeleted());
        }

        $result = Cloudinary\Uploader::destroy($taskImage->public_id, $options = array());
    }
}
