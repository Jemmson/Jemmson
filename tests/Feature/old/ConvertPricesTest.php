<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\JobTask;
use App\User;
use App\Location;
use App\Contractor;
use App\Customer;
use App\Job;
use App\Task;


class ConvertPricesTest extends TestCase
{

    use WithFaker;
    use RefreshDatabase;

    /**  @test */
    function pricesShouldBeStoredAsCents() {
        //

        $incomingPrice = 100;

        $user = factory(User::class)->create();

        $location = factory(Location::class)->create();

        $contractor = factory(Contractor::class)->create([
            "user_id" => $user->id,
            "location_id" => $location->id
        ]);

        $user1 = factory(User::class)->create();

        $location1 = factory(Location::class)->create();

        $customer = factory(Customer::class)->create([
            "user_id" => $user1->id,
            "location_id" => $location1->id
        ]);

        $job = factory(Job::class)->create([
            'customer_id' => $customer->id,
            'contractor_id' => $contractor->id,
            'location_id' => $location1->id,
        ]);

        $task = factory(Task::class)->create();

        $jobTask = factory(JobTask::class)->create([
            'job_id' => $job->id,
            'task_id' => $task->id,
            'bid_id' => 1,
            'location_id' => $location1->id,
            'contractor_id' => $contractor->id,
            'qty' => 2,
        ]);

        $response = $this->actingAs($user)->json('POST', '/api/task/updateCustomerPrice', [
            "jobId" => $job->id,
            "jobTaskId" => $jobTask->id,
            "price" => 123,
            "qty" => 2
        ]);

        $this->assertDatabaseHas('job_task',[
            'id' => $jobTask->id,
            'unit_price' => 12300,
            'cust_final_price' => 24600,
        ]);

        $this->assertDatabaseHas('job',[
            'bid_price' => 24600,
        ]);



    }

}
