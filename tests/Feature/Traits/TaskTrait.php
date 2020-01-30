<?php

namespace Tests\Feature\Traits;

use App\Contractor;
use App\Customer;
use App\Location;
use App\User;
use App\Task;

trait TaskTrait
{

    public function createTask(
        $contractorId, $task_array = []
    )
    {
        $params = [
            "contractor_id" => $contractorId,
        ];

        count($task_array) > 0 ?
            $payload = $this->mergeArrays($params, $task_array) :
            $payload = $params;

        return factory(Task::class)->create($payload);
    }

}