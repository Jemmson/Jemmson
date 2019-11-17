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

}