<?php

namespace Tests\Feature;

use App\User;
use Tests\Feature\Payloads\NewJob\SearchCustomer;
use GeneralContractorStripeNoCustomersSeeder;
use GeneralContractorStripeOneCustomerInitiatedAndRegisteredSeeder;
use GeneralContractorStripeTwoCustomersInitiatedAndRegisteredSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NewJobTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testPullingBackACustomerThatDoesNotExist()
    {
        $this->refreshDatabase();
        $this->seed(GeneralContractorStripeNoCustomersSeeder::class);
        $general = User::find(1);
        $response = $this->actingAs($general)->json('get', '/customer/search?Kri');
        $this->assertEquals("[]", $response->getContent());
    }

    public function testPullingBackACustomerThatExists()
    {
        $this->refreshDatabase();
        $this->seed(GeneralContractorStripeOneCustomerInitiatedAndRegisteredSeeder::class);
        $general = User::find(1);
        $response = $this->actingAs($general)->json('get', '/customer/search?query=sha');
        $customer = new SearchCustomer();
        $response->assertJson($customer->customerShawn());
    }

    public function testPullingBackOnlyTheCustomerThatExists()
    {
        $this->refreshDatabase();
        $this->seed(GeneralContractorStripeTwoCustomersInitiatedAndRegisteredSeeder::class);
        $general = User::find(1);
        $response = $this->actingAs($general)->json('get', '/customer/search?query=jak');
        $customer = new SearchCustomer();
        $response->assertJson($customer->customerJakson());
    }
}
