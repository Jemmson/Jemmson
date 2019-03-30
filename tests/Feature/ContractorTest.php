<?php

namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Contractor;
use App\User;


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

    /**  @test */
    function if_contractor_uses_accounting_software_then_it_should_be_recorded_in_the_contractors_table() {
        //
        $user = factory(User::class)->create([
            'current_billing_plan' => 'basic_monthly'
        ]);
        $contractor = factory(Contractor::class)->create([
            'user_id' => $user->id,
            'free_jobs' => 0,
            'accounting_software' => 'quickBooks'
        ]);

        $this->assertDatabaseHas('contractors',[
            'user_id' => $user->id,
            'free_jobs' => 0,
            'accounting_software' => 'quickBooks'
        ]);
    }

    /**  @test */
    function must_be_able_to_add_what_quickbooks_as_the_kind_of_software_the_contractor_is_using() {
        //

        $user = factory(User::class)->create([
            'current_billing_plan' => 'basic_monthly'
        ]);

        $contractor = factory(Contractor::class)->create([
            'user_id' => $user->id,
            'free_jobs' => 0,
            'accounting_software' => 'quickBooks'
        ]);

        $softwareName = 'quickbooks';

        $contractor->usesAccountingSoftware($softwareName);

        $this->assertDatabaseHas('contractors', [
            'accounting_software' => 'quickBooks',
            'id' => $contractor->id
        ]);
    }

    /**  @test */
    function contractorShouldBeAbleToDetermineWhatKindOfAccountingSoftwareTheyAreUsing() {
        //

        $user = factory(User::class)->create();

        $contractor = factory(Contractor::class)->create([
            'user_id' => $user->id,
            'free_jobs' => 100,
            'accounting_software' => 'quickBooks'
        ]);

        $softwareName = 'quickBooks';

        $contractor->usesAccountingSoftware($softwareName);

        $this->assertEquals('quickBooks', $contractor->checkAccountingSoftware());

    }

    /**  @test */
    function contractor_must_be_able_to_create_a_quickbooks_estimate() {
        // 

//        $this->assertEquals(true, $contractor->quickBooksEstimateCreated());
    }
    
    /**  @test */
    function check_if_contractor_has_a_quickooks_customer_if_not_then_add_it_to_quickBooks() {
        // 

//        $this->assertEquals(, $contractor->firstOrCreateQuickBooksCustomer($customer));
    }
    
    /**  @test */
    function check_that_a_contractor_does_not_have_a_the_same_phone_number_as_another_contractor() {
        // 
        
    }

}

