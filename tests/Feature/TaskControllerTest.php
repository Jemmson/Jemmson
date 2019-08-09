<?php

namespace Tests\Feature;

//use App\Classes\Quickbooks\FakeQuickbooksGateway;
//use App\Classes\Quickbooks\QuickbooksGateway;
use App\Contractor;
use App\JobTask;
//use App\Quickbook;
use Illuminate\Foundation\Auth\RegistersUsers;
use Tests\TestCase;
use App\User;
use App\Task;

//use App\Contractor;
//use App\Customer;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TaskControllerTest extends TestCase
{

    use RefreshDatabase;

    /**  @test */
    function shouldReturnTasksInDollars()
    {
        //

        $this->withoutExceptionHandling();

        $user = $this->createAUser('contractor', 1, 1);

        $this->createATask('hello', 1000, $user->id, [
            "proposed_sub_price" => 500
        ]);

        $response = $this->actingAs($user)->json('POST', '/search/task', [
            "taskname" => "hello",

        ]);

        $response->assertJson([
            [
                "id" => 1,
                "name" => "hello",
                "proposed_cust_price" => 10,
                "proposed_sub_price" => 5
            ]
        ]);

    }


    /************************************
     * PRIVATE METHODS
     ***********************************/


    private function createAUser($usertype, $passwordUpdated, $locationId, $array = [])
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

    private function createATask($name, $price, $userId, $array = [])
    {
        $payload = [
            "name" => $name,
            "proposed_cust_price" => $price,
            "contractor_id" => $userId
        ];

        $payload = $this->mergeArrays($payload, $array);

        return factory(Task::class)->create($payload);

    }

    private function mergeArrays($payload, $array)
    {
        if (!empty($array)) {
            foreach ($array as $k => $a) {
                $payload[$k] = $a;
            }
        }

        return $payload;
    }

    private function createAUserAndATask()
    {
        $user = $this->createAUser();

        $task = $this->createATask($user->id);

        return [
            "user" => $user,
            "task" => $task
        ];
    }

}
