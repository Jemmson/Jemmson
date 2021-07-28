<?php

namespace Tests\Feature;

use App\StripeEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StripeEventTest extends TestCase
{
    use RefreshDatabase;

    /**  @test */
    function test_that_I_get_a_new_instance_of_stripe_verification_if_one_does_not_exist_in_the_database() {
        $accountId = 'acct_1GQZp9AChMTtsYfn';
        $this->assertDatabaseMissing('stripe_events', [
            'account_id' => $accountId
        ]);
        $stripeVerification = StripeEvent::get($accountId);
        $this->assertEquals(null, $stripeVerification->accountId);
    }

    /**  @test */
    function test_that_I_get_an_existing_instance_of_stripe_verification_if_one_exists_in_the_database() {
        $accountId = 'acct_1GQZp9AChMTtsYfn';
        factory(StripeEvent::class)->create();
        $stripeVerification = StripeEvent::get($accountId);
        $this->assertDatabaseHas('stripe_events', [
            'account_id' => $accountId
        ]);
        $this->assertEquals($accountId, $stripeVerification->account_id);
    }
}
