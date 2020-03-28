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
        return factory(Job::class)->create([
            "customer_id" => 1,
            "contractor_id" => 2,
            "location_id" => 2,
            "status" => 'initiated'
        ]);
    }

    public function customerApprovesBid(
        $job,
        $address,
        $agreedStartDate,
        $customerLocationId,
        $jobLocationSameAsHome
    )
    {
        $job->approveJob(
            $address,
            $agreedStartDate,
            $customerLocationId,
            $jobLocationSameAsHome
        );
    }

}