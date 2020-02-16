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
        $status,
        $bidAmount = 100
    )
    {
        $params = [
            "job_id" => $job_id,
            "task_id" => $task_id,
            "bid_id" => $job_id,
            "location_id" => $location_id,
            "contractor_id" => $contractor_id,
            "status" => $status,
            "cust_final_price" => $bidAmount
        ];
        return factory(JobTask::class)->create($params);
    }

    public function subFinishesJobTask($jobTask)
    {
        $jobTask->subFinishesJobTask($jobTask->id, $jobTask->id);
    }

    public function customerMakesPayment($jobTask)
    {
        $jobTask->makePayment();
    }

}