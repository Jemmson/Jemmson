<?php

namespace Tests\Feature\Traits;

use App\Contractor;
use App\ContractorCustomer;
use App\Customer;
use App\Location;
use App\StripeExpress;
use App\User;

trait UserTrait
{

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

    public function createContractor(
        $userArray = [], $contractorArray = []
    )
    {

        $payload = [
            "usertype" => 'contractor',
            "password_updated" => 1
        ];

        $payload = $this->mergeArrays($payload, $userArray);
        $user = factory(User::class)->create($payload);

        $contractorPayload = [
            "user_id" => $user->id,
            "location_id" => $user->location_id
        ];
        $contractorPayload = $this->mergeArrays($contractorPayload, $contractorArray);
        factory(Contractor::class)->create($contractorPayload);

        $location = $this->createLocation($user->id);

        $user->location_id = $location->id;

        try {
            $user->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        $this->addingBillingInfo($user, $location);

        return $user;
    }

    public function createSub($general, $jobTaskId, $jobTask)
    {
        return $general->inviteSub(
            null,
            "6023508801",
            "kbattafarano@gmail.com",
            $jobTaskId,
            null,
            "Kristen",
            "Battafarano",
            "Garden Bud",
            "cash",
            $general->id,
            $jobTask
        );
    }

    public function setsUpStipe($user, $usertype)
    {
        if($usertype == 'general'){
            factory(StripeExpress::class)->create([
                "contractor_id" => $user->id
            ]);
        } else {
            factory(StripeExpress::class)->create([
                "contractor_id" => $user->id,
                'access_token' => 'sk_test_A66TTYqXidjivfiDYhomAfzd00M4efFmGy',
                'refresh_token' => 'rt_GcPUs4G4iKUtQ8EFSU3j3ds1YlAl2qGsxK53cu1ZKzCJ87xK',
                'stripe_publishable_key' => 'pk_test_X8lahyQoyHVpuFNxqVzbriLK00LpkFbEaW',
                'stripe_user_id' => 'acct_1CpJFSAA4Eqw07CC'
            ]);
        }
    }

    public function subSendsBidToGeneral(
        $sub,
        $bid_price,
        $paymentType,
        $generalId,
        $jobTask,
        $subId,
        $job
    )
    {
        $sub->subSendsBidToGeneral(
            $bid_price,
            $paymentType,
            $generalId,
            $jobTask,
            $subId,
            $job
        );
    }

    public function approvesSubsBid(
        $general, $jobTask, $subId, $jobTaskId, $price, $bidId
    )
    {
        $general->approvesSubsBid(
            $jobTask, $subId, $jobTaskId, $price, $bidId
        );
    }

    public function createCustomer(
        $userArray = [], $customerArray = []
    )
    {

        $payload = [
            "usertype" => 'customer',
            "password_updated" => 1
        ];

        $payload = $this->mergeArrays($payload, $userArray);
        $user = factory(\App\User::class)->create($payload);

        $customerPayload = [
            "user_id" => $user->id,
            "location_id" => $user->location_id
        ];
        $customerPayload = $this->mergeArrays($customerPayload, $customerArray);
        factory(customer::class)->create($customerPayload);

        $location = $this->createLocation($user->id);

        $user->location_id = $location->id;


        try {
            $user->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }

        $this->addingBillingInfo($user, $location);

        return $user;
    }

    public function addingBillingInfo($user, $location)
    {
        $user->billing_address = $location->address_line_1;
        $user->billing_address_line_2 = $location->address_line_2;
        $user->billing_address_city = $location->city;
        $user->billing_address_state = $location->state;
        $user->billing_address_zip = $location->zip;
        $user->billing_address_country = $location->country;

        try {
            $user->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }
    }

    public function createdmin_Contractor($user_params = [], $location_params = [], $contractor_params = [])
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
            return $this->actingAs($admin)->json('POST', '/task/notify', $params);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function addCustomerToContractorCustomerTable($customerId, $generalId)
    {
        $cc = new ContractorCustomer();
        $cc->contractor_user_id = $generalId;
        $cc->customer_user_id = $customerId;
        
        try {
            $cc->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 200);
        }
    }

}