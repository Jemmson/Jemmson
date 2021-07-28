<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testRegisteringAContractor()
    {
        $newContractor = new Payloads\RegisterContractor\RegisterContractorBasic();
        $response = $this->json('post', '/registerContractor', $newContractor->basic_request());
        $response->assertJson($newContractor->basic_response());
        $this->assertDatabaseHas('users', $newContractor->basic_users_table());
        $this->assertDatabaseHas('locations', $newContractor->basic_locations_table());
        $this->assertDatabaseHas('licenses', $newContractor->basic_licenses_table());
        $this->assertDatabaseHas('contractors', $newContractor->basic_contractors_table());
    }
}
