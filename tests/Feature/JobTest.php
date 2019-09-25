<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Job;
use App\User;
use App\Customer;
use App\JobStatus;
use App\Contractor;
use App\Location;
use App\Task;
use App\JobTask;
use Illuminate\Foundation\Testing\WithFaker;

class JobTest extends TestCase
{

//    use RefreshDatabase;
    use Setup;
    use WithFaker;


    /**  @test */
    function test_returning_the_correct_json_response_when_going_to_the_jobs_endpoint()
    {
        //

        $this->withoutExceptionHandling();

        $contractor = $this->createAUser("contractor", 1, 1);

        $contractorLocation = factory(Location::class)->create([
            'user_id' => $contractor->id,
        ]);

        $contractor->location_id = $contractorLocation->id;
        $contractor->save();

        $customer = $this->createAUser("customer", 1, 2);

        $customerLocation = factory(Location::class)->create([
            'user_id' => $customer->id,
        ]);

        $customer->location_id = $customerLocation->id;
        $customer->save();

        $task = $this->createATask("task 1", 100, $contractor->id);

        $job = factory(Job::class)->create([
            'customer_id' => $customer->id,
            'contractor_id' => $contractor->id,
            'location_id' => $customerLocation->id
        ]);

        $jobTask = factory(JobTask::class)->create([
            'job_id' => $job->id,
            'task_id' => $task->id,
            'bid_id' => 1,
            'location_id' => $customerLocation->id,
            'contractor_id' => $contractor->id,
        ]);

        $response = $this->actingAs($contractor)->json('GET', '/jobs');

        $response->assertJson([
            [
                "id" => 1,
                "customer_id" => $customer->id,
                "contractor_id" => $contractor->id,
                "location_id" => null,
                "job_name" => "pool job",
                "status" => "bid.initiated",
                "bid_price" => 0,
                "completed_bid_date" => null,
                "agreed_start_date" => null,
                "agreed_end_date" => null,
                "actual_end_date" => null,
                "deleted_at" => null,
                "created_at" => "2019-09-16 23:16:59",
                "updated_at" => "2019-09-16 23:16:59",
                "declined_message" => null,
                "paid_with_cash_message" => null,
                "qb_estimate_id" => "NULL",
                "job_tasks" => [],
                "customer" => [
                    "id" => 2,
                    "customer" => [
                        "id" => 1,
                        "user_id" => 2,
                        "location_id" => null,
                        "email_method_of_contact" => null,
                        "phone_method_of_contact" => null,
                        "sms_method_of_contact" => null,
                        "notes" => null,
                        "created_at" => "2019-09-16 23:16:59",
                        "updated_at" => "2019-09-16 23:16:59"
                    ],
                    "tax_rate" => 0
                ]
            ]
        ]);

    }

}
