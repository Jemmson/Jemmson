<?php

namespace Tests\Feature;

use App\BidContractorJobTask;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Job;
use Carbon\Carbon;
use App\JobTask;

class StatusesTest extends TestCase
{

    use Setup;
    use WithFaker;
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
//    public function test_when_contractor_initiates_a_bid_there_should_be_appropriate_statuses()
//    {
//
//        $contractor = $this->createAUser('contractor', 1, 1);
//
//        $response = $this->actingAs($contractor)->json(
//            'Post',
//            '/initiate-bid',
//            [
//                "busy" => false,
//                "customerName" => "Shawn Pike",
//                "email" => "",
//                "errors" => ["errors" => []],
//                "errors" => [],
//                "firstName" => "Shawn",
//                "jobName" => "pool job",
//                "lastName" => "Pike",
//                "phone" => "(480) 703-4902",
//                "quickbooks_id" => "",
//                "successful" => false
//            ]);
//
//        $job = Job::first();
//
//        $this->assertDatabaseHas('jobs', [
//            "id" => $job->id,
//            "status" => "bid.initiated",
//        ]);
//
//        $job_task = JobTask::first();
//
//        $this->assertEquals(true, empty($job_task));
//
//        $bcjt = BidContractorJobTask::first();
//
//        $this->assertEquals(true, empty($bcjt));
//
//        $response->assertStatus(200);
//    }


    public function test_when_contractor_adds_a_task_there_are_appropriate_statuses()
    {
        $contractor = $this->createAUser('contractor', 1, 1);
        $customer = $this->createAUser('customer', 1, 2);

        $job = factory(Job::class)->create();

        $response = $this->actingAs($contractor)->json(
            'Post',
            '/task/addTask',
            [
                "area" => "",
                "assetAccountRef" =>
                [
                    "value" => "0",
                    "name" => "Inventory Asset"
                ],
                "contractorId" => $contractor->id,
                "createNew" => true,
                "customer_id" => $customer->id,
                "customer_message" => "ASDSSADDAS",
                "expenseAccountRef" =>
                    [
                        "value" => "0",
                        "name" => "Cost of Goods Sold"
                    ],
                "hasQtyUnitError" => false,
                "hasStartDateError" => false,
                "incomeAccountRef" => [
                    "value" => "0",
                    "name" => "Sales of Product Income"
                ],
                "item_id" => "",
                "invStartDate" => "",
                "jobId" => $job->id,
                "qty" => "120",
                "qtyOnHand" => "0",
                "qtyUnit" => "ft",
                "qtyUnitErrorMessage" => "",
                "start_date" => Carbon::now(),
                "start_when_accepted" => true,
                "startDateErrorMessage" => "",
                "sub_message" => "DSADSADSADSA",
                "subTaskPrice" => "0",
                "taskExists" => "",
                "taskId" => 0,
                "taskPrice" => "3",
                "taskName" => "Clean Tile",
                "trackQtyOnHand" => true,
                "type" => "Inventory",
                "updateTask" => false,
                "useStripe" => false,
                "errors" => [
                    "errors" => []
                ],
                "busy" => true,
                "successful" => false
            ]
        );

        $this->assertDatabaseHas('jobs', [
            "id" => $job->id,
            "status" => "bid.in_progress",
        ]);

        $job_task = JobTask::first();

        $this->assertDatabaseHas('job_task', [
            "id" => $job_task->id,
            "status" => "bid_task.initiated",
        ]);

        $bcjt = BidContractorJobTask::first();

        $this->assertEquals(true, empty($bcjt));
//        $this->assertDatabaseHas('bid_contractor_job_task', [
//            "id" => $bcjt->id,
//            "status" => null,
//        ]);

        $response->assertStatus(200);
    }



}
