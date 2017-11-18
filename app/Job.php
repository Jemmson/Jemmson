<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Log;

use App\JobActions;

class Job extends Model
{   
    //
    public function time()
    {
        return $this->hasMany(Time::class);
    }

    public function jobs()
    {
        return $this->belongsToMany(Job::class);
    }

    public function jobActions()
    {
        $jobActions = $this->hasOne(JobActions::class);
        
        if ($jobActions->exists()) {
            $jobActions = $jobActions->get();
        } else {
            $jobActions = $this->createJobActions();
        }

        return $jobActions;
    }

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
        } catch (\Exception $e) {
            Log::error('JobActions: ' . $e->getMessage());
        }
    }

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
        } catch (\Exception $e) {
            Log::error('JobActions: ' . $e->getMessage());
        }
    }
    
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
        } catch (\Exception $e) {
            Log::error('JobActions: ' . $e->getMessage());
        }
    }

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
            Log::error('JobActions: ' . $e->getMessage());
        }
    }
    
}
