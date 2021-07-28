<?php

namespace Tests\Feature;

use App\Contractor;
use App\Customer;
use App\Task;
use App\User;
use phpDocumentor\Reflection\Location;

trait Setup
{

    public function createUser(
        $usertype, $passwordUpdated, $locationId,
        $userArray = [], $contractorArray = [], $customerArray = []
    )
    {

        $payload = [
            "usertype" => $usertype,
            "password_updated" => $passwordUpdated,
            "location_id" => $locationId
        ];

        $payload = $this->mergeArrays($payload, $userArray);
        $user = factory(User::class)->create($payload);

        if ($usertype == 'contractor') {
            $contractorPayload = [
                "user_id" => $user->id,
                "location_id" => $user->location_id
            ];
            $contractorPayload = $this->mergeArrays($contractorPayload, $contractorArray);
            factory(Contractor::class)->create($contractorPayload);
        } else {
            $customerPayload = [
                "user_id" => $user->id,
                "location_id" => $user->location_id
            ];
            $customerPayload = $this->mergeArrays($customerPayload, $customerArray);
            factory(Customer::class)->create($customerPayload);
        }

        return $user;
    }
}