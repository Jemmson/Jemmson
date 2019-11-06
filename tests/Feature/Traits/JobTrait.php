<?php

namespace Tests\Feature\Traits;

use App\Contractor;
use App\Customer;
use App\Location;
use App\User;
use App\Job;
use Illuminate\Foundation\Testing\WithFaker;

trait JobTrait
{

    use WithFaker;
    use UtilitiesTrait;

    public function create_a_job($customer_id, $contractor_id, $location_id, $status)
    {
        $params = [
            "customer_id" => $customer_id,
            "contractor_id" => $contractor_id,
            "location_id" => $location_id,
            "status" => $status
        ];
        return factory(Job::class)->create($params);
    }

}