<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Log;
use \App\Traits\ConvertPrices;


class JobTask extends Model
{
    use ConvertPrices;
    use SoftDeletes;

    protected $table = "job_task";
//    protected $fillable = [
//        'job_id',
//        'task_id',
//        'bid_id',
//        'location_id',
//        'contractor_id',
//        'status',
//        'cust_final_price',
//        'sub_final_price',
//        'start_when_accepted',
//        'stripe',
//        'start_date',
//        'stripe_transfer_is',
//        'customer_message',
//        'sub_message',
//        'qty',
//        'sub_sets_own_price',
//        'declined_message',
//        'unit_price'
//    ];
    protected $guarded = [];

    /**
     * Get the task this bid belongs to
     *
     * @return Task
     */

    /*
     * *********************************
     * Relationships
     * **********************************/

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function contractor()
    {
        return $this->belongsTo(User::class)->with('contractor');
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


    /*
 * *********************************
 * Methods
 * **********************************/


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

    public function createJobTask($request)
    {
        // standard task column = new column value
        $this->job_id = $request->jobId;
        $this->task_id = $request->taskId;
        $this->status = 'bid_task.initiated';
        $this->contractor_id = $request->contractorId;
        $this->cust_final_price = $this->convertToCents($request->qty * $request->taskPrice);
        $this->sub_final_price = $this->convertToCents($request->subTaskPrice);
        if (empty($request->start_date)) {
            $this->start_date = \Carbon\Carbon::now();
        } else {
            $this->start_date = $request->start_date;
        }
        $this->customer_message = $request->customer_message;
        $this->sub_message = $request->sub_message;
        $this->stripe = $request->useStripe;
        $this->qty = (int)$request->qty;
        $this->unit_price = $this->convertToCents($request->taskPrice);

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

    public function addToJobTask(String $jobId, String $taskId, $request)
    {
        // standard task column = new column value
        $this->job_id = $jobId;
        $this->task_id = $taskId;
        $this->status = 'bid_task.initiated';
        $this->contractor_id = $request->contractorId;
        $this->cust_final_price = $this->convertToCents($request->qty * $request->taskPrice);
        $this->sub_final_price = $this->convertToCents($request->subTaskPrice);
        if (empty($request->start_date)) {
            $this->start_date = \Carbon\Carbon::now();
        } else {
            $this->start_date = $request->start_date;
        }
        $this->customer_message = $request->customer_message;
        $this->sub_message = $request->sub_message;
        $this->stripe = $request->useStripe;
        $this->qty = (int)$request->qty;
        $this->unit_price = $this->convertToCents($request->taskPrice);

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Add Job Task: ' . $e->getMessage());
            return response()->json([
                "message" => "Couldn't add Job Task.",
                "errors" => ["error" => [$e->getMessage()]]], 404);
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
}
