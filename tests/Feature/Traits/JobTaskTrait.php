<?php

namespace Tests\Feature\Traits;

use App\JobTask;

trait JobTaskTrait
{

    public function createJobTask(
        $job_id,
        $task_id,
        $location_id,
        $contractor_id,
        $status
    )
    {
        $params = [
            "job_id" => $job_id,
            "task_id" => $task_id,
            "bid_id" => $job_id,
            "location_id" => $location_id,
            "contractor_id" => $contractor_id,
            "status" => $status
        ];
        return factory(JobTask::class)->create($params);
    }

}