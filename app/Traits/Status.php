<?php

namespace App\Traits;

use App\JobStatus;
use App\JobTaskStatus;
use App\SubStatus;

trait Status {

    public function setJobStatus($job_id, $status)
    {
        $js = new JobStatus();
        $js->setStatus($job_id, $status);
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

    public function setCancelJobStatuses($job, $status)
    {
        $this->setJobStatus($job->id, $status);

        $jts = $job->jobTasks()->get();

        foreach ($jts as $jt) {
            $this->setJobTaskStatus($jt->id, $status);

            $ss = $jt->bidContractorJobTasks()->get();

            foreach($ss as $s){
                $this->setSubStatus($s->id, $jt->id, $status);
            }
        }
    }

    public function setJobTasksAndSubStatuses($job, $status)
    {
        $jts = $job->jobTasks()->get();

        foreach ($jts as $jobTask){
            if ($job->contractor_id !== $jobTask->contractor_id) {
                $this->setSubStatus($jobTask->contractor_id, $jobTask->id, $status);
            }
            $this->setJobTaskStatus($jobTask->id, $status);
        }

    }

}