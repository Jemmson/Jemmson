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

class JobTest extends TestCase
{

    use RefreshDatabase;

    /**  @test */
    function create_a_job_name_from_an_empty_job() {
        //

        $j = new Job();
        $this->assertEquals('1', $j->jobName());

    }

    /**  @test */
    function return_jobName_when_the_job_Name_exists() {
        //
        $j = new Job();
        $this->assertEquals('customer', $j->jobName('customer'));

    }

    /**  @test */
    public function create_a_bid()
    {
        $j = new Job();
        $jobName = $j->jobName();

        $user = factory(User::class)->create([
            'usertype' => 'customer',
            'phone' => '0000000000'
        ]);

        $customer = factory(Customer::class)->create([
            'user_id' => $user->id,
            'email_method_of_contact' => 1,
            'phone_method_of_contact' => 1,
            'sms_method_of_contact' => 1,
            'notes' => 'zxccxzccczcxcz'
        ]);


        $user1 = factory(User::class)->create([
            'usertype' => 'customer',
            'phone' => '0000000000'
        ]);


        $contractor = factory(Contractor::class)->create([
            'user_id' => $user1->id,
            'free_jobs' => 5,
            'company_name' => 'dlkdfkdfkldf',
            'company_logo_name' => 'xxxzzzzzzzx',
            'email_method_of_contact' => 1,
            'sms_method_of_contact' => 1,
            'phone_method_of_contact' => 1
        ]);

        $j->createEstimate($customer->id, $jobName, $contractor->id);

        $this->assertDatabaseHas('jobs',[
            'customer_id' => $customer->id,
            'contractor_id' => $contractor->id,
            'job_name' => $jobName,
        ]);
    }

    /**  @test */
    function the_job_should_set_the_status_to_intiate_bid_once_the_job_is_initiated() {

        $job = factory(Job::class)->create();

        $status = 'initiated';

        $js = new JobStatus();
        $js->setStatus($job->id, $status);

        $this->assertDatabaseHas('job_status',[
           'job_id' => $job->id,
            'status_number' => 1,
            'status' => $status
        ]);
    }


    /**  @test */
    function that_i_get_the_correct_payload_when_querying_existing_jobs() {
        //

        $this->withExceptionHandling();

        $contractor = factory(User::class)->create([
            'usertype' => 'contractor',
            'password_updated' => 1
        ]);

        $location = factory(Location::class)->create();

        factory(Contractor::class)->create([
            'user_id' => $contractor->id,
            'location_id' => $location->id,
        ]);

        $customer = factory(User::class)->create([
            'usertype' => 'customer',
            'password_updated' => 1
        ]);

        $location1 = factory(Location::class)->create();

        factory(Customer::class)->create([
            'user_id' => $customer->id,
            'location_id' => $location1->id,
        ]);


        $job = factory(Job::class)->create([
            'customer_id' => $customer->id,
            'contractor_id' => $contractor->id
        ]);

        $task = factory(Task::class)->create([
            "name" => "pool work",
            "sub_instructions" => "sub Instruction",
            "customer_instructions" => "customer instructions"
        ]);

        factory(JobTask::class)->create([
            "job_id" => $job->id,
            "task_id" => $task->id
        ]);


        $response = $this->actingAs($contractor)->json('GET', '/jobs');


        $response->assertJson([
            "created" => "true"
        ]);


    }


}
