<?php

namespace Tests\Unit;

use App\Contractor;
use App\Customer;
use App\Job;
use App\JobStatus;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobUnitTest extends TestCase
{
    /**  @test */
    function create_a_job_name_from_an_empty_job()
    {
        //

        $j = new Job();
        $this->assertEquals('1', $j->jobName());

    }

    /**  @test */
    function return_jobName_when_the_job_Name_exists()
    {
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

        $this->assertDatabaseHas('jobs', [
            'customer_id' => $customer->id,
            'contractor_id' => $contractor->id,
            'job_name' => $jobName,
        ]);
    }

    /**  @test */
    function the_job_should_set_the_status_to_intiate_bid_once_the_job_is_initiated()
    {

        $job = factory(Job::class)->create();

        $status = 'initiated';

        $js = new JobStatus();
        $js->setStatus($job->id, $status);

        $this->assertDatabaseHas('job_status', [
            'job_id' => $job->id,
            'status_number' => 1,
            'status' => $status
        ]);
    }
}
