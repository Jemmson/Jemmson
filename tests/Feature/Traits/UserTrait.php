<?php

namespace Tests\Feature\Traits;

use App\Contractor;
use App\Customer;
use App\Location;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;

trait UserTrait
{

    use WithFaker;
    use UtilitiesTrait;

    public function createLocation($user_id, $params = [])
    {
        if (count($params) === 0) {
            $location = factory(Location::class)->create([
                "user_id" => $user_id,
                "address_line_1" => $this->faker->address,
                "city" => $this->faker->city,
                "state" => "AZ",
                "zip" => "90210",
                "area" => "baseline and southern",
                "country" => $this->faker->country
            ]);
        } else {
            $location = factory(Location::class)->create($params);
        }
        return $location;
    }

    public function createAdmin_Contractor($user_params = [], $location_params = [], $contractor_params = [])
    {

        if (count($user_params) != 0) {

            if (
                array_key_exists("password_updated", $user_params) &&
                $user_params["password_updated"] == 1
            ) {
                $payload = $user_params;
            } else {
                $user_params['password_updated'] = 1;
            }

        } else {
            $payload = [
                "usertype" => 'contractor',
                "password_updated" => 1,
            ];
        }
        $user = factory(User::class)->create($payload);


        if (count($location_params) == 0) {
            $location = $this->createLocation($user->id);
        } else {
            $location = $this->createLocation($user->id, $location_params);
        }
        $user->location_id = $location->id;
        $user->save();

        $baseArray = [
            "user_id" => $user->id,
            "location_id" => $location->id,
            "company_name" => $this->faker->company
        ];

        if (count($contractor_params) != 0) {
            $contractorPayload = $this->mergeArrays($contractor_params, $baseArray);
        } else {
            $contractorPayload = $baseArray;
        }

        factory(Contractor::class)->create($contractorPayload);
        return $user;

    }

    public function create_a_customer($user_params = [], $location_params = [], $customer_params = [])
    {
        if (count($user_params) != 0) {

            if (
                array_key_exists("password_updated", $user_params) &&
                $user_params["password_updated"] == 1
            ) {
                $payload = $user_params;
            } else {
                $user_params['password_updated'] = 1;
            }

        } else {
            $payload = [
                "usertype" => 'customer',
                "password_updated" => 1,
            ];
        }
        $user = factory(User::class)->create($payload);


        if (count($location_params) == 0) {
            $location = $this->createLocation($user->id);
        } else {
            $location = $this->createLocation($user->id, $location_params);
        }
        $user->location_id = $location->id;
        $user->save();

        $baseArray = [
            "user_id" => $user->id,
            "location_id" => $location->id,
        ];

        if (count($customer_params) != 0) {
            $customer_payload = $this->mergeArrays($customer_params, $baseArray);
        } else {
            $customer_payload = $baseArray;
        }

        factory(Customer::class)->create($customer_payload);
        return $user;
    }

    public function getNewlyCreatedUser($name)
    {
        return User::where('name', '=', $name)->get()->first();
    }

    public function invite_a_sub($admin, $taskId, $jobTaskId, $firstName = 'Kristen', $lastName = 'Battafarano')
    {
        $params = [
            "task_id" => $taskId,
            "email" => "kbattafarano@gmail.com",
            "phone" => "6023508801",
            "counter" => 0,
            "name" => $firstName . ' ' . $lastName,
            "firstName" => $firstName,
            "lastName" => $lastName,
            "givenName" => "",
            "familyName" => "",
            "quickbooksId" => "",
            "companyName" => "Garden Bud",
            "paymentType" => "stripe",
            "errors" => [
                "errors" => []
            ],
            "busy" => true,
            "successful" => false,
            "jobTaskId" => $jobTaskId
        ];

        $response = '';

        try {
            $response = $this->actingAs($admin)->json('POST', '/task/notify', $params);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return $response;
    }

}