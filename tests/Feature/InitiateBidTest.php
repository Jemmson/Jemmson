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
    }

}
