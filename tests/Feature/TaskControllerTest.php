<?php

namespace Tests\Feature;

//use App\Classes\Quickbooks\FakeQuickbooksGateway;
//use App\Classes\Quickbooks\QuickbooksGateway;
use App\Contractor;
use App\JobTask;
//use App\Quickbook;
use Illuminate\Foundation\Auth\RegistersUsers;
use Tests\Feature\Traits\JobTaskTrait;
use Tests\Feature\Traits\JobTrait;
use Tests\Feature\Traits\TaskTrait;
use Tests\Feature\Traits\UserTrait;
use Tests\Feature\Traits\UtilitiesTrait;
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

    use WithFaker;
    use UtilitiesTrait;
    use Setup;
    use UserTrait;
    use JobTaskTrait;
    use TaskTrait;
    use JobTrait;
    use RefreshDatabase;

    /**  @test */
    function shouldReturnTasksInDollars()
    {
        //

        $this->withoutExceptionHandling();

        $general = $this->createContractor();

        $this->createTask($general->id, [
            "name" => "task1",
            "proposed_cust_price" => 1000,
            "proposed_sub_price" => 500,
            "unit_price" => 1000
        ]);

        $response = $this->actingAs($general)->json('POST', '/search/task', [
            "taskname" => "task1",
            "proposed_cust_price" => 10,
            "proposed_sub_price" => 5,
            "unit_price" => 10,

        ]);

        $response->assertJson([
            [
                "id" => 1,
                "name" => "task1",
                "proposed_cust_price" => 10,
                "proposed_sub_price" => 5,
                "unit_price" => 10
            ]
        ]);

    }


    /************************************
     * PRIVATE METHODS
     ***********************************/


    /**  @test */
    function that_i_am_pulling_the_right_tasks_back()
    {

        $this->withoutExceptionHandling();

        // GIVEN
//        create a general contractor
        $general = $this->createContractor();
//        create a customer
        $customer = $this->createCustomer();
//        create a job
        $job = $this->createJob(
            $customer->id,
            $general->id,
            $customer->location_id,
            'initiated'
        );
//        create a task
        $task = $this->createTask($general->id, [
            "name" => "task1",
            "unit_price" => 10000
        ]);
//        create a job task
        $jobTask = $this->createJobTask($job->id, $task->id, $customer->location_id, $general->id, 'initiated');
//        invite the sub

//        dd($jobTask);

        // ACTION
        $response = $this->actingAs($general)->json('POST', '/task/notify', [
            "task_id" => $task->id,
            "email" => "",
            "phone" => "6023508801",
            "counter" => 0,
            "name" => "",
            "firstName" => "Kristen",
            "lastName" => "Battafarano",
            "givenName" => "",
            "familyName" => "",
            "quickbooksId" => "",
            "companyName" => "Garden Bud",
            "paymentType" => "",
            "errors" => ["errors" => []],
            "busy" => true,
            "successful" => false,
            "jobTaskId" => 2
        ]);

        // ASSERTION
        $response->assertJson([
            [
                "id" => 1,
                "name" => "hello",
                "proposed_cust_price" => 10,
                "proposed_sub_price" => 5
            ]
        ]);

//        assert a new sub has been added as a user in the database table

//        assert there is an entry in the sub table

//        assert the sub has been associated with the general

//        assert the sub has been notified of the new job

//        assert the sub status table has been set to initiated for the sub


    }

}
