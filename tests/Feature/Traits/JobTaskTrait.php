<?php

namespace Tests\Feature\Traits;

use App\Contractor;
use App\Customer;
use App\Location;
use App\User;
use App\JobTask;
use Illuminate\Foundation\Testing\WithFaker;

trait JobTaskTrait
{

    use WithFaker;
    use UtilitiesTrait;

    public function create_a_job_task(
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