<?php

namespace Tests\Feature;

use App\StripeAccountVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StripeAccountVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**  @test */
    function test_that_I_get_a_new_instance_of_stripe_verification_if_one_does_not_exist_in_the_database() {
        $accountId = 'acct_1GQZp9AChMTtsYfn';


        $this->assertDatabaseMissing('stripe_account_verification', [
           'account_id' => $accountId
        ]);
        $stripeVerification = StripeAccountVerification::get($accountId);

        $this->assertEquals(null, $stripeVerification->accountId);

    }

    /**  @test */
    function test_that_I_get_an_existing_instance_of_stripe_verification_if_one_exists_in_the_database() {
        $accountId = 'acct_1GQZp9AChMTtsYfn';

        factory(StripeAccountVerification::class)->create([
            'account_id' => 'acct_1GQZp9AChMTtsYfn',
            'current_deadline' => NULL,
            'currently_due' => json_encode(array (0 => 'individual.id_number',)),
            'disabled_reason' => 'requirements.past_due',
            'errors' => json_encode(array ()),
            'eventually_due' => json_encode(array (0 => 'individual.id_number',)),
            'past_due' => json_encode(array (0 => 'individual.id_number',)),
            'pending_verification' => json_encode(array ()),
        ]);

        $stripeVerification = StripeAccountVerification::get($accountId);

        $this->assertDatabaseHas('stripe_account_verification', [
            'account_id' => $accountId
        ]);

        $this->assertEquals($accountId, $stripeVerification->account_id);
    }

}
