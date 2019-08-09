<?php

namespace Tests\Feature;

use App\Contractor;
use App\Task;
use App\User;

trait Setup {
    
    public function createAUser($usertype, $passwordUpdated, $locationId, $array = [])
    {

        $payload = [
            "usertype" => $usertype,
            "password_updated" => $passwordUpdated,
            "location_id" => $locationId
        ];

        $payload = $this->mergeArrays($payload, $array);

        $user = factory(User::class)->create($payload);

        factory(Contractor::class)->create([
            "id" => $user->id
        ]);

        return $user;
    }

    public function createATask($name, $price, $userId, $array = [])
    {
        $payload = [
            "name" => $name,
            "proposed_cust_price" => $price,
            "contractor_id" => $userId
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