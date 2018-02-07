<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Log;

use App\JobActions;

class Job extends Model
{   
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
        $tasks = $this->tasks()->get();
        $subs = [];
        foreach ($tasks as $task) {
            $subs[] = $task->jobTask()->first()->contractor();
        }
        return $subs;
    }

    public function tasks()
    {
        return $this->belongsToMany('App\Task')
            ->withTimestamps();
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

    /**
     * Helper function to create a JobActions
     * related to this Job model
     *
     * @return void
     */
    public function createJobActions() {

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
        $this->status = $status;

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error('Updating Job Status: ' . $e->getMessage());
            return false;
        }
        return true;
    }
    
}
