<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Feature\Setup;
use Tests\Feature\Traits\JobTaskTrait;
use Tests\Feature\Traits\JobTrait;
use Tests\Feature\Traits\TaskTrait;
use Tests\Feature\Traits\UserTrait;
use Tests\Feature\Traits\UtilitiesTrait;
use App\Task;

class JobTask extends TestCase
{

    use WithFaker;
    use UtilitiesTrait;
    use Setup;
    use UserTrait;
    use JobTaskTrait;
    use TaskTrait;
    use JobTrait;
    use RefreshDatabase;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    /**  @test */
    function that_get_the_task_from_the_job_task() {

        // GIVEN
//        need a task
//        need a job task
        $this->createJob(1, 1, 1, 'approved');
        $task = $this->createTask(1);
        $jobTask = $this->createJobTask(1, $task->id, 1, 1, 'approved_by_customer');

        // ACTION
//      trigger the method

        $returnedTask = $jobTask->getTheTaskFromTheJobTask($jobTask);

        // ASSERTION
//        that I receive the task
        $this->assertEquals($task->id, $returnedTask->id);
    }

    /**  @test */
    function that_get_the_sub_from_the_job_task() {

        // GIVEN
//        need a task
//        need a job task
        $general = $this->createContractor();
        $job = $this->createJob(1, 1, 1, 'approved');
        $task = $this->createTask(1);
        $jobTask = $this->createJobTask($job->id, $task->id, 1, 1, 'approved_by_customer');
        $sub = $this->createSub($general, $jobTask->id, $jobTask);
        $this->addSubToJobTask($jobTask, $sub->id);

        // ACTION
//      trigger the method

        $returnedSub = $jobTask->getTheSubFromTheJobTask($jobTask);

        // ASSERTION
//        that I receive the task
        $this->assertEquals($returnedSub->id, $sub->id);
    }

    /**  @test */
    function that_get_the_job_from_the_job_task() {

        // GIVEN
//        need a task
//        need a job task
        $general = $this->createContractor();
        $customer = $this->createCustomer();
        $customerLocation = $this->createLocation($general->id);
        $job = $this->createJob($customer->id, $general->id, $customerLocation->id, 'approved');
        $task = $this->createTask($general->id);
        $jobTask = $this->createJobTask($job->id, $task->id, $customerLocation->id, $general->id, 'approved_by_customer');
        $sub = $this->createSub($general, $jobTask->id, $jobTask);
        $this->addSubToJobTask($jobTask, $sub->id);

        // ACTION
//      trigger the method
        $returnedJob = $jobTask->getTheJobFromTheJobTask($jobTask);

        // ASSERTION
//        that I receive the task
        $this->assertEquals($returnedJob->id, $job->id);
    }



}
