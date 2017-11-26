<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Log;

use App\JobActions;

class Job extends Model
{   
    //
//    public function time()
//    {
//        return $this->hasMany(Time::class);
//    }

    public function tasks()
    {
        return $this->belongsToMany('App\Task')
            ->withPivot(
                'cust_final_price',
                'sub_final_price',
                'sub_cont_proposed',
                'cont_sub_proposed',
                'cust_cont_proposed',
                'cont_cust_proposed',
                'sub_cont_accepted',
                'cont_sub_accepted',
                'cust_cont_accepted',
                'cont_cust_accepted'
            )
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
    
}
