<?php

namespace Tests\Feature;

use App\User;
use GeneralContractorStripeNoCustomersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Feature\Payloads\NewJob\InitiateBidPayload;

class InitiateBidTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    public function testInitiatingABidToABrandNewCustomer()
    {
        $this->refreshDatabase();
        $this->seed(GeneralContractorStripeNoCustomersSeeder::class);
        $general = User::find(1);
        $initiateBid = new InitiateBidPayload();
        $response = $this->actingAs($general)->json('post', '/initiate-bid', $initiateBid->InitiateBidRequest());
        $response->assertJson($initiateBid->InitiateBidResponse());
        $this->assertDatabaseHas('users', $initiateBid->basic_users_table());
        $this->assertDatabaseHas('customers', $initiateBid->basic_customers_table());
        $this->assertDatabaseHas('contractor_customer', $initiateBid->basic_contractor_customer_table());
        $this->assertDatabaseHas('jobs', $initiateBid->basic_jobs_table());
        $this->assertDatabaseHas('user_tokens', $initiateBid->basic_user_tokens_table_email());
        $this->assertDatabaseHas('user_tokens', $initiateBid->basic_user_tokens_table_text());
        $this->assertDatabaseHas('job_status', $initiateBid->basic_job_status_table());
    }

}
