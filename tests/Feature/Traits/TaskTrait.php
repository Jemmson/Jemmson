<?php

namespace Tests\Feature\Traits;

use App\Contractor;
use App\Customer;
use App\Location;
use App\User;
use App\Task;
use Illuminate\Foundation\Testing\WithFaker;

trait TaskTrait
{

    use WithFaker;
    use UtilitiesTrait;

    public function create_a_task(
        $contractor_id
    )
    {
        $params = [
            "contractor_id" => $contractor_id,
        ];
        return factory(Task::class)->create($params);
    }

}