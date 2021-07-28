<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidatePhoneNumberTest extends TestCase
{

    use Setup;
    use RefreshDatabase;

    /**  @test */
    function testThatSuccessfulResponseReturnedWhenTheNumberSearchedForIsAMobileNumber() {
        //

        $contractor = $this->createAUser("contractor", true, 1);

        $response = $this->actingAs($contractor)->json('post', 'api/user/validatePhoneNumber', [
            "num" => "4807034902"
        ]);

        $response->assertJson(
            ["success", "mobile", "mobile"]
        );

    }

    /**  @test */
    function testThatAFailureResponseIsReturnedWhenTheNumberSearchedForIsALandlineNumber() {
        //

        $contractor = $this->createAUser("contractor", true, 1);

        $response = $this->actingAs($contractor)->json('post', 'api/user/validatePhoneNumber', [
            "num" => "2089249886"
        ]);

        $response->assertJson(
            [ "failure", "landline", "landline"]
        );

    }

    /**  @test */
    function test_a_virtual_number_is_returned() {
        //

        $contractor = $this->createAUser("contractor", true, 1);

        $response = $this->actingAs($contractor)->json('post', 'api/user/validatePhoneNumber', [
            "num" => "2237470884"
        ]);

        $response->assertJson(
            [ "success", "virtual", "virtual"]
        );

    }
}
