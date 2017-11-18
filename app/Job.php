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
        return $this->hasOne(JobActions::class)->get();
    }

    public function acceptJob()
    {   
        if ($this->id == null) {
            return false;
        }

        $jobActions = $this->jobActions();
        
        if ($jobActions->id == null) {
            $jobActions = $this->createJobActions();
        }

        $jobActions->job_accepted = true;
        $jobActions->job_accpeted_updated_on = Carbon::now();
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

        if ($jobActions->id == null) {
            $jobActions = $this->createJobActions();
        }

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

        if ($jobActions->id == null) {
            $jobActions = $this->createJobActions();
        }

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
        } catch (\Exception $e) {
            Log::error('JobActions: ' . $e->getMessage());
        }
    }
    
}
