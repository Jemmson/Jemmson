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
        $contractor_id, $task_array = []
    )
    {
        $params = [
            "contractor_id" => $contractor_id,
        ];

        count($task_array) > 0 ?
            $payload = $this->mergeArrays($params, $task_array) :
            $payload = [];

        return factory(Task::class)->create($payload);
    }

}