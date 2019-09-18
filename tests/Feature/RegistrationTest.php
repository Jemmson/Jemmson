<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Classes\Registration;
use App\User;
use App\Contractor;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;

//    /**  @test */
//    function should_save_access_token_data_to_table_if_the_software_type_is_quickbooks() {
//        //
//        $user = factory(User::class)->create([
//            'current_billing_plan' => 'basic_monthly'
//        ]);
//
//        $contractor = factory(Contractor::class)->create([
//            'user_id' => $user->id,
//            'free_jobs' => 0,
//            'accounting_software' => 'quickBooks'
//        ]);
//    }

    /**  @test */
    function phoneNumberMustBeValidPhoneNumber()
    {
        //

        $response = $this->json('post', 'api/user/validatePhoneNumber', [
            "num" => "4806226441"
        ]);

        $response->assertJson([
            "success",
            "mobile",
            "mobile"
        ]);

    }

    /**  @test */
    function shouldReturnEmailFieldIsRequiredIfEmailIsSentAsBlank()
    {
        //

        $noEmail = $this->basePostData();

        $noEmail["email"] = "";

        $response = $this->json('post', 'registerContractor', $noEmail);

        $response->assertJson(
            ["errors" =>
                ["email" =>
                    ["The email field is required."]
                ]
            ]
        );

    }

    /**  @test */
    function shouldRegisterAContractorIfAContractorTriesToRegister()
    {
        //

        $contractor = $this->basePostData();

        $response = $this->json('post', 'registerContractor', $contractor);

        $response->assertJson(
            [
                "redirect" => "/#/home",
                "user" => [
                    "name" => "Shawn Pike",
                    "email" => "blah@blah.com",
                    "usertype" => "contractor",
                    "phone" => "4806226441",
                    "first_name" => "Shawn",
                    "last_name" => "Pike",
                    "billing_address" => "705 E Oxford Dr.",
                    "billing_address_line_2" => "",
                    "billing_city" => "Tempe",
                    "billing_state" => "AL",
                    "billing_zip" => "jemmsoninc@gmail.com",
                    "billing_country" => "AF",
                    "password_updated" => true,
                    "id" => 1,
                    "location_id" => 1
                ]
            ]
        );


    }


    private function basePostData()
    {
        return [
            "addressLine1" => "705 E Oxford Dr.",
            "addressLine2" => "",
            "busy" => true,
            "city" => "Tempe",
            "companyName" => "Jemmson",
            "country" => [
                "name" => "Afghanistan",
                "code" => "AF"],
            "disabled" => false,
            "email" => "blah@blah.com",
            "errors" => [
                "first_name" => "",
                "last_name" => "",
                "email" => "",
                "companyName" => "",
                "phoneNumber" => "",
                "addressLine1" => "",
                "addressLine2" => '',
                "city" => '',
                "state" => '',
                "zip" => '',
                "country" => '',
                "password" => '',
                "password_confirmation" => '',
                "terms" => false
            ],
            "first_name" => "Shawn",
            "last_name" => "Pike",
            "password" => "asdasd",
            "password_confirmation" => "asdasd",
            "phoneNumber" => "(480) 622-6441",
            "state" => [
                "name" => "Alabama",
                "code" => "AL"
            ],
            "terms" => true,
            "usertype" => "contractor",
            "zip" => "jemmsoninc@gmail.com"
        ];
    }

}
