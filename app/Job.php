<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Log;

use App\JobActions;

class Job extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'zip',
        'status',
        'completed_bid_date',
        'bid_price',
        'agreed_start_date',
        'actual_end_date',
        'job_name'
    ];

//
//    public function time()
//    {
//        return $this->hasMany(Time::class);
//    }

    public function customer()
    {
        return $this->belongsTo(User::class)->with('customer');
    }

    public function contractor()
    {
        return $this->belongsTo(User::class)->with('contractor');
    }

    /**
     * All subs who have a job task on this job
     *
     * @return void
     */
    public function subs()
    {
        $jobTasks = $this->jobTasks()->get();
        $subs = [];
        foreach ($jobTasks as $jobTask) {
            $subs[] = $jobTask->contractor();
        }
        return $subs;
    }

    public function tasks()
    {
        return $this->belongsToMany('App\Task')
            ->withTimestamps();
    }

    public function jobTasks()
    {
        return $this->hasMany(JobTask::class, 'job_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(TaskImage::class, 'job_id');
    }

    /**
     * Return related JobActions Model - create it
     * if it doesn't exist
     *
     * @return JobActions model
     */
    public function jobActions()
    {
        $jobActions = $this->hasOne(JobActions::class);

        if ($jobActions->exists()) {
            $jobActions = $jobActions->first();
        } else {
            $jobActions = $this->createJobActions();
        }

        return $jobActions;
    }

    public function location()
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }

    /**
     * Accept the job
     *
     * @return bool did this action succeed
     */
    public function acceptJob()
    {
        if ($this->id == null) {
            return false;
        }

        $jobActions = $this->jobActions();

        $jobActions->job_accepted = true;
        $jobActions->job_accepted_updated_on = Carbon::now();
        try {
            $jobActions->save();
            return true;
        } catch (\Exception $e) {
            Log::error('JobActions: accept job - ' . $e->getMessage());
            return false;
        }
    }

    public function newLocation($request)
    {
        $location = new Location();
        $location->address_line_1 = $request->address_line_1;
        $location->address_line_2 = $request->address_line_2;
        $location->city = $request->city;
        $location->state = $request->state;
        $location->zip = $request->zip;

        try {
            $location->save();
            $this->location_id = $location->id;
            $this->save();
        } catch (\Exception $e) {
            Log::error('Saving Location: ' . $e->getMessage());
        }
    }

    public function updateStartDate($date)
    {
        $this->agreed_start_date = $date;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Updating Job Start Date was unsuccessful: ' . $e->getMessage());
        }
    }

    /**
     * Set job as completed if all child task are paid for
     *
     * @return void
     */
    public function setJobAsCompleted()
    {
        if ($this->getCountOfUnpaidTasks() >= 1) {
            return;
        }

        $this->status = __('job.completed');

        try {
            $this->save();    
        } catch(\Exception $e) {
            Log::error('Update Job' . $e->getMessage());
        }
    }

    public function getCountOfUnpaidTasks()
    {
        return count($this->jobTasks()->where('status', '!=', __('bid_task.customer_sent_payment'))->get());
    }

    public function getArea()
    {
//        dd($this->location);
        $location = $this->location()->first();
        if (!empty($location)){
            return $location->area;
        }
        return response()->json(["message" => "Could Not Save Get the Area"], 404);
    }



    // TODO: Refactor for best practice
    public function updateArea($area)
    {
        $location = $this->location()->first();

        $location->area = $area;

        try {
            $location->save();
            return response()->json($location, 200);
        } catch (\Exception $e) {
            return response()->json(["message" => "Could Not Save Area"], 404);
        }
    }

    /**
     * Decline the job
     *
     * @return bool did this action succeed
     */
    public function declineJob()
    {
        if ($this->id == null) {
            return false;
        }

        $jobActions = $this->jobActions();

        $jobActions->job_declined = true;
        $jobActions->job_declined_updated_on = Carbon::now();
        try {
            $jobActions->save();
            return true;
        } catch (\Exception $e) {
            Log::error('JobActions: decline job - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Approve the job
     *
     * @return bool did this action succeed
     */
    public function approveJob()
    {
        if ($this->id == null) {
            return false;
        }

        $jobActions = $this->jobActions();

        $jobActions->job_approved = true;
        $jobActions->job_approved_updated_on = Carbon::now();
        try {
            $jobActions->save();
            return true;
        } catch (\Exception $e) {
            Log::error('JobActions: approve job - ' . $e->getMessage());
            return false;
        }
    }

    public function changeJobStatus($job, $message)
    {
        $this->status = $message;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Add Task: ' . $e->getMessage());
            return response()->json([
                "message" => "Couldn't update job status message.",
                "errors" => ["error" => [$e->getMessage()]]], 404);
        }
    }

    public function jobTotal()
    {
        // for each task that is related to the job -> SUM(qty * cust_final_price)
        $jt = $this->jobTasks()->get();

        $bid_price = 0;

        foreach ($jt as $j){$bid_price = $bid_price + ($j->qty * $j->cust_final_price);}

        $this->bid_price = $bid_price;

        try {
            $this->save();
        } catch(\Exception $e) {
            Log::error('Job total could not be updated: ' . $e->getMessage());
            return false;
        }
        return true;


//        $j = Job::find(1);
//        $jt = $j->jobTasks()->get();
//        $bid_price = 0;
//        foreach ($jt as $t){$bid_price = $bid_price + ($t->qty * $t->cust_final_price);}
//        $j->bid_price = $bid_price;
//        $j->save();


    }

    public function addPrice($amount)
    {
        $this->bid_price += $amount;

        try {
            $this->save();
        } catch(\Exception $e) {
            Log::error('Adding Price To Job: ' . $e->getMessage());
            return false;
        }
        return true;
    }

    public function subtractPrice($amount)
    {
        Log::debug('existing amount ' . $this->bid_price);
        Log::debug('amount: ' . $amount);
        $this->bid_price -= (int) $amount;
        if ($this->bid_price < 0) {
            $this->bid_price = 0;
        }

        try {
            $this->save();
            Log::debug('Saved subtract job ' .  $this);
        } catch(\Exception $e) {
            Log::error('Subtracting Price To Job: ' . $e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * Helper function to create a JobActions
     * related to this Job model
     *
     * @return void
     */
    public function createJobActions()
    {

        if ($this->id == null) {
            return false;
        }

        $jobActions = new JobActions();
        $jobActions->job_id = $this->id;

        try {
            $jobActions->save();
            return $jobActions;
        } catch (\Exception $e) {
            Log::error('JobActions: creating new JobActions - ' . $e->getMessage());
        }
    }

    /**
     * Update Job status
     *
     * @param string $status
     * @return bool
     */
    public function updateStatus($status)
    {
        if (!$this->updatable($status)) {
            return false;
        }

        $this->status = $status;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Updating Job Status: ' . $e->getMessage());
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
            case 'bid.cancel':
                return $this->isCancellable();
                break;
            case 'job.completed':
                return $this->isCompletable();
                break;
            default:
                return true; // TODO: testing, should be false
                break;
        }
    }

    private function isCancellable()
    {
        return $this->status === 'bid.sent';
    }

    private function isCompletable()
    {
        return $this->status === 'job.approved' && $this->allJobTasksResolved();
    }

    private function allJobTasksResolved()
    {
        $totalTasks = count(DB::table('job_task')->where('job_id', $this->id)->where('deleted_at', null)->get());
        $totalTasksResolved = count(DB::table('job_task')->where('job_id', $this->id)->where('status','bid_task.customer_sent_payment')->get());
        return $totalTasks === $totalTasksResolved;
    }

    /**
     * Declined message
     *
     * @param String $message
     * @return void
     */
    public function setJobDeclinedMessage(String $message)
    {
        $this->declined_Message = $message;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Set Declined Job Message: ' . $e->getMessage());
            return false;
        }
        return true;
    }

}
