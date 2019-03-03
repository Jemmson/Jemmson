<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Contractor;
use App\Customer;
use App\ContractorCustomer;
use App\User;

class ContractorCustomerTest extends TestCase
{
    use DatabaseMigrations;

    /**  @test */
    function if_a_customer_is_not_associated_to_a_contractor_then_the_customer_should_be_added_to_the_contractor_customer_table() {
        //
        $user = factory(User::class)->create([
            'current_billing_plan' => 'basic_monthly'
        ]);
        $contractor = factory(Contractor::class)->create([
            'user_id' => $user->id,
            'free_jobs' => 0,
            'accounting_software' => 'quickBooks'
        ]);

        $user = factory(User::class)->create([
            'current_billing_plan' => 'basic_monthly'
        ]);
        $customer = factory(Customer::class)->create([
            'user_id' => $user->id
        ]);

        $cc = new ContractorCustomer();

        $this->assertEquals(true, $cc->checkIfCustomerCurrentlyExistsForContractor($contractor->id, $customer->id));

        $cc->associateCustomer($contractor->id, $customer->id);

        $this->assertDatabaseHas('contractor_customer', [
            'contractor_id' => $contractor->id,
            'customer_id' => $customer->id
        ]);

    }

    /**  @test */
    function if_a_customer_is_associated_to_a_contractor_then_the_customer_should_not_be_added_to_contractorCustomer_table() {

    }

}
