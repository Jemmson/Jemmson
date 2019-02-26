<?php

namespace Tests\Feature;

use App\Task;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Contractor;
use App\User;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;

class ContractorTest extends TestCase
{

    use DatabaseMigrations;

    /**  @test */
    function sustract_one_free_job_from_a_contractor_if_the_contractor_has_a_postive_number_of_free_jobs_left() {

        // add a contractor to the site
        $contractor = factory(Contractor::class)->create();

        // check the number of jobs the contractor has in the site
        $this->assertEquals($contractor->numberOfJobsLeft(), 5);

        // initiate a bid and then check that there is one less free job in the database
        $contractor->subtractFreeJob();

        $this->assertEquals(4, $contractor->numberOfJobsLeft());

    }

    /**  @test */
    function do_not_sustract_one_free_job_from_a_contractor_if_the_contractor_does_not_have_a_postive_number_of_free_jobs_left() {

        // add a contractor to the site
        $contractor = factory(Contractor::class)->create([
            'free_jobs' => 0
        ]);

        // check the number of jobs the contractor has in the site
        $this->assertEquals(0, $contractor->numberOfJobsLeft());

        // initiate a bid and then check that there is one less free job in the database
        $contractor->subtractFreeJob();

        $this->assertEquals(0, $contractor->numberOfJobsLeft());

    }
    
    /**  @test */
    function throw_error_if_contractor_tries_to_initiate_a_bid_but_is_not_subscribed_and_has_no_free_jobs() {

        $user = factory(User::class)->create([
            'current_billing_plan' => null
        ]);
        $contractor = factory(Contractor::class)->create([
            'user_id' => $user->id,
            'free_jobs' => 0
        ]);

        $this->assertEquals(false, $contractor->canCreateNewJob());
    }

    /**  @test */
    function a_contractor_can_create_a_new_job_if_he_is_not_subscribed_but_has_free_jobs_left() {

        $user = factory(User::class)->create([
            'current_billing_plan' => null
        ]);
        $contractor = factory(Contractor::class)->create([
            'user_id' => $user->id,
            'free_jobs' => 1
        ]);

        $this->assertEquals(true, $contractor->canCreateNewJob());
    }

    /**  @test */
    function a_contractor_can_create_a_new_job_if_he_is_subscribed_but_has_no_free_jobs_left() {

        $user = factory(User::class)->create([
            'current_billing_plan' => 'basic_monthly'
        ]);
        $contractor = factory(Contractor::class)->create([
            'user_id' => $user->id,
            'free_jobs' => 0
        ]);

        $this->assertEquals(true, $contractor->canCreateNewJob());
    }
}

