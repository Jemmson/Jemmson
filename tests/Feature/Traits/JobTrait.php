<?php

namespace Tests\Feature\Traits;

use App\Contractor;
use App\Customer;
use App\Location;
use App\User;
use App\Job;

trait JobTrait
{

    public function createJob($customer_id, $contractor_id, $location_id, $status)
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