<?php

namespace Tests\Unit;

use App\Http\Controllers\StripeGatewayController;
use App\JobTaskStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Feature\Setup;
use Tests\Feature\Traits\JobTaskTrait;
use Tests\Feature\Traits\JobTrait;
use Tests\Feature\Traits\TaskTrait;
use Tests\Feature\Traits\UserTrait;
use Tests\Feature\Traits\UtilitiesTrait;

class StripeGatewayTest extends TestCase
{
    use WithFaker;
    use UtilitiesTrait;
    use Setup;
    use UserTrait;
    use JobTaskTrait;
    use TaskTrait;
    use JobTrait;
    use RefreshDatabase;

//    /**  @test */
//    function that_all_parties_are_in_the_same_region() {
//        //
//        $customer = $this->createCustomer();
//        $general = $this->createContractor();
//        $sub = $this->createContractor();
//
//        $this->createLocation($customer->id);
//        $this->createLocation($general->id);
//        $this->createLocation($sub->id);
//
//        $sgc = new StripeGatewayController();
//
//        $this->assertEquals(true, $sgc->allPartiesInSameRegion($customer, $general, $sub));
//    }

    /**  @test */
    function test_there_are_more_than_one_excluded_task() {
        //

        $excluded = [
            1 => true,
            4 => false,
            5 => true,
            10 => false,
            12 => true,
        ];

        $sgc = new StripeGatewayController();

        $this->assertEquals(true, $sgc->jobTasksExist($excluded)["exists"]);

    }

    /**  @test */
    function test_that_it_should_be_false_if_all_tasks_are_marked_as_excluded() {
        //

        $excluded = [
            1 => true,
            4 => true,
            5 => true,
            10 => true,
            12 => true,
        ];

        $sgc = new StripeGatewayController();

        $this->assertEquals(false, $sgc->jobTasksExist($excluded)["exists"]);

    }

    /**  @test */
    function get_total_amount_of_all_non_excluded_job_tasks() {
        //

        $job = $this->createJob(2, 1, 1, 'finished');

        $taskId = $this->createTask(1);

        $jobTask = $this->createJobTask($job->id, $taskId, 1, 1, 'finished');

        $jtId = $jobTask->id;

        $excluded = [
            $jtId => false,
        ];

        $sgc = new StripeGatewayController();

        $jobTasks = $sgc->jobTasksExist($excluded);

        $this->assertEquals(200, $sgc->getTotalTaskPrices($jobTasks["jobTasks"]));

    }

    /**  @test */
    function that_a_job_task_has_not_been_paid() {
        //

        $sgc = new StripeGatewayController();

        $job = $this->createJob(2, 1, 1, 'finished');
        $taskId = $this->createTask(1);
        $jobTask = $this->createJobTask($job->id, $taskId, 1, 1, 'finished');

        factory(JobTaskStatus::class)->create([
            "job_task_id" => $jobTask->id,
            "status" => "waiting_for_customer_approval",
            "status_number" => 11,
            "sent_on" => Carbon::now()
        ]);

        $this->assertEquals(true, $sgc->hasNotBeenPaid($jobTask));
        
    }
    
}
