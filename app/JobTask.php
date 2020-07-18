<?php

namespace App;

use App\Job;
use App\Notifications\TaskFinished;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\JobTaskStatus;
use Illuminate\Support\Facades\Log;
use App\User;
use App\Task;

use \App\Traits\ConvertPrices;


class JobTask extends Model
{
    use ConvertPrices;
    use SoftDeletes;

    protected $table = "job_task";
    protected $guarded = [];

    /*
     * *********************************
     * Relationships
     * **********************************/

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function jobTaskStatuses()
    {
        return $this->hasMany(JobTaskStatus::class);
    }

    public function subStatuses()
    {
        return $this->hasMany(SubStatus::class);
    }

    public function taskMessages()
    {
        return $this->hasMany(TaskMessage::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function contractor()
    {
        return $this->belongsTo(User::class)->with('contractor');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bidContractorJobTasks()
    {
        return $this->hasMany(BidContractorJobTask::class, 'job_task_id');
    }

    public function images()
    {
        return $this->hasMany(TaskImage::class, 'job_task_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function transferGroup()
    {
        return $this->belongsTo(TransferGroup::class);
    }

    /*
 * *********************************
 * Methods
 * **********************************/

    public function setStatusToInitiated()
    {
        if (empty($this->checkStatus('initiated'))) {
            $jts = new JobTaskStatus();
            $jts->setStatus($this->id, 'initiated');
        }
    }

    private function checkStatus($status)
    {
        return JobStatus::where("status", "=", $status)
            ->where("job_task_id", "=", $this->id)->get()->first();
    }

    public function updateTaskStartDate($date)
    {
        $this->start_date = $date;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('update job task start date: ' . $e->getMessage());
            return response()->json([
                "message" => "couldn't update job task start date.",
                "errors" => ["error" => [$e->getMessage()]]], 404);
        }

    }

    static function findEarliestStartDate($jobId)
    {


        if (empty(JobTask::select('start_date')
            ->where("job_id", "=", $jobId)
            ->oldest('start_date')->get()->first()->start_date)) {
            $now = Carbon::now();
            return $now->date;
        }
        return JobTask::select('start_date')
            ->where("job_id", "=", $jobId)
            ->oldest('start_date')->get()->first()->start_date;

    }

    public function addJobTask($request, $taskId)
    {
        return self::createJobTask(
            $request->jobId,
            $taskId,
            $request->contractorId,
            $request->qty,
            $request->taskPrice,
            $request->subTaskPrice,
            $request->customer_message,
            $request->sub_message,
            $request->useStripe
        );
    }

    public function addJobTaskFromNewJob($jobId, $taskId, $request)
    {
        self::createJobTask(
            $jobId,
            $taskId,
            $request->contractorId,
            $request->qty,
            $request->taskPrice,
            $request->subTaskPrice,
            $request->customer_message,
            $request->sub_message,
            $request->useStripe
        );
    }

    public function createJobTask(
        $jobId,
        $taskId,
        $contractorId,
        $qty,
        $taskPrice,
        $subTaskPrice,
        $customer_message,
        $sub_message,
        $useStripe
    )
    {
        // standard task column = new column value
        $this->job_id = $jobId;
        $this->task_id = $taskId;
        $this->status = 'bid_task.initiated';
        $this->contractor_id = $contractorId;
        $this->cust_final_price = $this->convertToCents($qty * $taskPrice);
        $this->sub_final_price = $this->convertToCents($subTaskPrice);
        if (empty($start_date)) {
            $this->start_date = \Carbon\Carbon::now();
        } else {
            $this->start_date = $start_date;
        }
        $this->customer_message = $customer_message;
        $this->sub_message = $sub_message;
        $this->stripe = $useStripe;
        $this->qty = (int)$qty;
        $this->unit_price = $this->convertToCents($taskPrice);

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Add Job Task: ' . $e->getMessage());
            return response()->json([
                "message" => "Couldn't add Job Task.",
                "errors" => ["error" => [$e->getMessage()]]], 404);
        }
    }

    public function setLocation(Job $job)
    {
        if ($job->location_id != null) {
            $this->location_id = $job->location_id;
            $this->save();
        }
    }

    /**
     *
     *
     * @param String $id stripe transfer id
     * @return void
     */
    public function paid(String $id)
    {
        if ($id === null || $id === '' || $id === ' ') {
            return;
        }

        $this->stripe_transfer_id = $id;
        $this->status = __("bid_task.customer_sent_payment");

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Update JobTask: ' . $e->getMessage());
        }
    }

    /**
     *
     *
     * @param String $id
     * @return void
     */
    public function setStripeTransferId(String $id)
    {
        if ($id === null || $id === '' || $id === ' ') {
            return;
        }

        $this->stripe_transfer_id = $id;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Update JobTask: ' . $e->getMessage());
        }
    }


    public function makePayment()
    {
        return StripeExpress::makePayment($this);
    }


    public function subFinishesJobTask()
    {
        $task = $this->getTheTaskFromTheJobTask($this);
        $sub = $this->getTheSubFromTheJobTask($this);
        $job = $this->getTheJobFromTheJobTask($this);
        $status = __("bid_task.finished_by_sub");
        if (!$this->updateStatus($status)) {
            return response()->json(["message" => "Couldn't update job task.", "errors" => ["error" => ['']]], 422);
        }
        $this->setSubsFinishedStatus($this);
        $this->notifyTheGeneralContractor(
            $task,
            $sub,
            $job
        );
        $this->resetDeclinedMessage();
    }

    public function getTheJobTask($id)
    {
        return JobTask::find($id);
    }

    public function getTheTaskFromTheJobTask($jobTask)
    {
        return $jobTask->task()->first();
    }

    public function getTheSubFromTheJobTask($jobTask)
    {
        return User::find($jobTask->contractor_id);
    }

    public function getTheJobFromTheJobTask($jobTask)
    {
        return Job::find($jobTask->job_id);
    }

    public function setsGeneralsFinishedStatus($jobTask)
    {
        $this->setJobTaskStatus($jobTask->id, 'general_finished_work');
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

    public function setSubsFinishedStatus($jobTask)
    {
        $this->setJobTaskStatus($jobTask->id, 'sub_finished_work');
        $this->setSubStatus($jobTask->contractor_id, $jobTask->id, 'finished_job');
    }

    public function notifyTheGeneralContractor(
        $task,
        $sub,
        $job
    )
    {
        $generalContractor = $this->getTheGeneralContractor($job->contractor_id);
        $generalContractor->notify(new TaskFinished(
            $task,
            null,
            $sub,
            $generalContractor,
            $this,
            $job
        ));
    }

    public function setJobTaskStatus($job_task_id, $status)
    {
        $jts = new JobTaskStatus();
        $jts->setStatus($job_task_id, $status);
    }

    public function setSubStatus($user_id, $job_task_id, $status)
    {
        $ss = new SubStatus();
        $ss->setStatus($user_id, $job_task_id, $status);
    }

    public function getTheGeneralContractor($contractor_id)
    {
        return User::find($contractor_id);
    }


    public function updateLocation($request)
    {
        if ($this->location_id === null) {
            $location = new Location();
            $location->address_line_1 = $request->address_line_1;
            $location->address_line_2 = $request->address_line_2;
            $location->city = $request->city;
            $location->state = $request->state;
            $location->zip = $request->zip;
        } else {
            $location = $this->location()->first();
            $location->address_line_1 = $request->address_line_1;
            $location->address_line_2 = $request->address_line_2;
            $location->city = $request->city;
            $location->state = $request->state;
            $location->zip = $request->zip;
        }

        try {
            $location->save();
            $this->location_id = $location->id;
            $this->save();
            return $location;
        } catch (\Exception $e) {
            Log::error('Saving Location: ' . $e->getMessage());
            return $e;
        }
    }

    public function toggleStripe()
    {
        $this->stripe = !$this->stripe;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Toggle Stripe: ' . $e->getMessage());
            return false;
        }
        return true;
    }

    public function updateStatus($status)
    {
        if (!$this->updatable($status)) {
            return false;
        }

        $this->status = $status;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Updating JobStatus Status: ' . $e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * Can the status be changed to what its
     * trying to be changed to
     *
     * @param string $status
     * @return bool
     */
    public function updatable(string $status)
    {
        switch ($status) {
            case 'bid_task.customer_sent_payment':
                return $this->isPayable();
                break;
            default:
                return true; // TODO: testing, should be false
                break;
        }
    }

    /**
     *
     * @param String $msg
     * @return bool
     */
    public function setDeclinedMessage(String $msg)
    {
        $this->declined_message = $msg;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Set Declined Message: ' . $e->getMessage());
            return false;
        }
        return true;
    }

    public function add(Request $request)
    {
        // standard task column = new column value
        $this->job_id = $request->jobId;
        $this->task_id = $request->taskId;
        $this->contractor_id = $request->contractorId;
        $this->cust_final_price = $this->convertToCents($request->qty * $request->taskPrice);
        $this->sub_final_price = 0;
        $this->start_when_accepted = $request->customer_message;
        if (empty($request->start_date)) {
            $this->start_date = \Carbon\Carbon::now();
        } else {
            $this->start_date = $request->start_date;
        }
        $this->customer_message = $request->customer_message;
        $this->sub_message = $request->sub_message;
        $this->qty = $request->qty;
        $this->unit_price = $this->convertToCents($request->taskPrice);

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Add/Update Task: ' . $e->getMessage());
            return response()->json([
                "message" => "Couldn't add task.",
                "errors" => ["error" => [$e->getMessage()]]], 404);
        }
    }

    public function resetDeclinedMessage()
    {
        $this->declined_message = null;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Reset Declined Message: ' . $e->getMessage());
            return false;
        }
        return true;
    }

    private function isPayable()
    {
        return $this->status === 'bid_task.finished_by_general' || $this->status === 'bid_task.approved_by_general';
    }

    public static function getAllTaskIdsForAcceptedJobs(Array $jobIds)
    {
        return JobTask::whereIn('job_id', $jobIds)
            ->where('status', '=', 'bid_task.accepted')
            ->select(['task_id'])->get()->toArray();
    }

    public static function totalAmountForAllPayableTasks($jobTasks)
    {
        $amount = 0;

        foreach ($jobTasks as $jobTask) {
            $amount = $amount + $jobTask->cust_final_price;
        }

        return $amount;
    }

    public static function allTasksArePaid($jobId)
    {
        $allJobTasks = JobTask::select('id')->where('job_id', '=', $jobId)->get();

        $totalTasks = count($allJobTasks);

        $paidTasks = 0;

        foreach ($allJobTasks as $jobTask) {
            $jts = JobTaskStatus::where('job_task_id', '=', $jobTask->id)->where('status', '=', 'paid')->get()->first();
            if ($jts) {
                $paidTasks += 1;
            }
        }

        return $totalTasks == $paidTasks;
    }

    public static function atLeastOnTaskIsPaid($jobId)
    {
        $allJobTasks = JobTask::select('id')->where('job_id', '=', $jobId)->get();

        foreach ($allJobTasks as $jobTask) {
            $jts = JobTaskStatus::where('job_task_id', '=', $jobTask->id)->where('status', '=', 'paid')->get()->first();
            if ($jts) {
                return true;
            }
        }

        return false;
    }

    public static function markTasksAsPaid($jobTasks)
    {
        foreach ($jobTasks as $jobTask) {
            $jts = new JobTaskStatus();
            $jts->job_task_id = $jobTask->id;
            $jts->status = 'paid';
            $jts->status_number = 12;
            $jts->sent_on = Carbon::now();
            $jts->save();
        }

        $jobTaskId = $jobTasks[0]->id;
        $jobId = JobTask::where('id', '=', $jobTaskId)->get()->first()->job_id;
        $job = Job::find($jobId);

        if (JobTask::allTasksArePaid($jobId)) {
            $job->markJobAsPaid($jobId);
        }


        User::markSubJobTasksAsPaid($jobTasks, $jobId);
    }
}
