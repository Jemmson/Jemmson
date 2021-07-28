<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NexmoTest extends TestCase
{

    public function testValidatingAMobilePhoneNumberReturnsSuccess()
    {
        $response = $this->postJson('/api/user/validatePhoneNumber', ['num' => '4807034902']);
        $response
            ->assertStatus(200)
            ->assertJson([
                "success",
                "mobile",
                "mobile"
            ]);
    }

    public function testValidatingALandLinePhoneNumberReturnsFalse()
    {
        $response = $this->postJson('/api/user/validatePhoneNumber', ['num' => '2087340982']);
        $response
            ->assertStatus(200)
            ->assertJson([
                "failure",
                "mobile",
                "landline"
            ]);
    }
}
