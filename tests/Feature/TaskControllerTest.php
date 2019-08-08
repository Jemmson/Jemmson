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

        $userTask = $this->createAUserAndATask();

        $response = $this->actingAs($userTask['user'])->json('POST', '/search/task', [
            "taskname" => "hello",

        ]);

        $response->assertJson([
            [[
                "id" => 1,
                "name" => "hello",
                "proposed_cust_price" => 100
            ]]
        ]);

    }



    /**  @test */
    function () {
        // 
        
    }




    /************************************
     * PRIVATE METHODS
     ***********************************/


    private function createAUser()
    {
        $user = factory(User::class)->create([
            "usertype" => "contractor",
            "password_updated" => 1,
            "location_id" => 1
        ]);

        factory(Contractor::class)->create([
            "id" => $user->id
        ]);

        return $user;
    }

    private function createATask($userId)
    {
        return factory(Task::class)->create([
            "name" => "hello",
            "proposed_cust_price" => 10000,
            "contractor_id" => $userId
        ]);

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
