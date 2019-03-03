<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Contractor extends Model
{
//    protected $fillable = [
//        'user_id',
//        'company_name',
//        'company_logo_name',
//        'email_method_of_contact',
//        'sms_method_of_contact',
//        'phone_method_of_contact',
//    ];

    protected $guarded = [];

    //

    public function jobs()
    {
        return $this->hasMany(Job::class, 'contractor_id', 'user_id');
    }

    public function jobTasks()
    {
        return $this->hasMany(JobTask::class, 'contractor_id', 'user_id');
    }

    public function time()
    {
        return $this->hasMany(Time::class, 'contractor_id', 'user_id');
    }

    public function numberOfJobsLeft()
    {
        return $this->free_jobs;
    }

    public function hasMoreFreeJobs()
    {
        return $this->free_jobs > 0;  // means there are more free jobs that exist for the contractor
    }

    public function isSubscribed()
    {
        return $this->user()->first()->current_billing_plan !== null; // means the contractor has subscribed
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function canCreateNewJob()
    {
        if ($this->isSubscribed()) {
            return true;
        } else if ($this->hasMoreFreeJobs()) {
            return true;
        } else {
            return false;

        }
    }

    public function subtractFreeJob()
    {
        if ($this->free_jobs <= 0) {
            return true;
        }
        $this->free_jobs -= 1;
        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Subtract Free Job: ' . $e->getMessage());
            return false;
        }
        return true;
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }

    public function stripeExpress()
    {
        return $this->hasOne(StripeExpress::class, 'contractor_id', 'user_id');
    }

    public function tasks()
    {
        return $this
            ->belongsToMany(Task::class, 'contractor_id', 'user_id')->withPivot('base_price')
            ->withTimestamps();
    }

    /**
     * Get all tasks sent to the
     * current contractor as bids
     *
     * @return void
     */
    public function bidContractorJobTasks()
    {
        return $this->hasMany(BidContractorJobTask::class, 'contractor_id', 'user_id');
    }

    public function addContractorToBidForJobTable($contractorId, $jobTaskId, $taskId)
    {
        $proposedBidPrice = Task::find($taskId)->proposed_sub_price;
        // TODO: update this
        DB::table('bid_contractor_job_task')->insert(
            ['contractor_id' => $contractorId, 'job_task_id' => $jobTaskId, "bid_price" => $proposedBidPrice]
        );

    }

    public function checkIfContractorSetBidForATask($contractorId, $jobTaskId)
    {
        if (empty(DB::table('bid_contractor_job_task')
            ->select('job_task_id')
            ->where('contractor_id', '=', $contractorId)
            ->where('job_task_id', '=', $jobTaskId)
            ->get()[0])) {
            return true;
        } else {
            return false;
        }
    }

    public function updateLocation($request)
    {

        if ($this->location_id === null) {
            $location = new Location();
            $location->user_id = $this->user_id;
            $location->default = true;
            $location->address_line_1 = $request->address_line_1;
            $location->address_line_2 = $request->address_line_2;
            $location->city = $request->city;
            $location->area = $request->city;
            $location->state = $request->state;
            $location->zip = $request->zip;
        } else {
            $location = $this->location()->first();
            $location->address_line_1 = $request->address_line_1;
            $location->address_line_2 = $request->address_line_2;
            $location->city = $request->city;
            $location->state = $request->state;
            $location->zip = $request->zip;
            $location->area = $request->city;
        }

        try {
            $location->save();
            $this->location_id = $location->id;
            $this->save();
        } catch (\Exception $e) {
            Log::error('Saving Location: ' . $e->getMessage());
        }



    }

    public function location()
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }
}

//DB::table('bid_contractor_job_task')->select('task_id')->where('contractor_id', '=', 5)->where('task_id', '=', 2)->where('job_id', '=', 1)->get()[0];