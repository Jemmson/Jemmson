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

    use Setup;
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


    /**  @test */
    function that_i_am_pulling_the_right_tasks_back() {

        // GIVEN


        // ACTION


        // ASSERTION
//        $this->assertEquals([]);


    }

}
