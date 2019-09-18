<?php

namespace Tests\Feature;

use App\Contractor;
use App\Customer;
use App\Task;
use App\User;

trait Setup
{

    public function createAUser(
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

    public function createATask($name, $price, $contractorId, $array = [])
    {
        $payload = [
            "name" => $name,
            "proposed_cust_price" => $price,
            "contractor_id" => $contractorId
        ];

        $payload = $this->mergeArrays($payload, $array);

        return factory(Task::class)->create($payload);

    }

    public function mergeArrays($payload, $array)
    {
        if (!empty($array)) {
            foreach ($array as $k => $a) {
                $payload[$k] = $a;
            }
        }

        return $payload;
    }
}