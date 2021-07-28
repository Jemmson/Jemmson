<?php

namespace Tests\Feature\Payloads\RegisterContractor;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class RegisterContractorBasic
{
    use WithFaker;

    public function basic_request(): array
    {
        return [
            "first_name" => "Shawn",
            "last_name" => "Pike",
            "email" => "pike.shawn@gmail.com",
            "companyName" => "Jemmson",
            "phoneNumber" => "(480) 703-4902",
            "addressLine1" => "705 E OXFORD DR",
            "addressLine2" => "",
            "licenses" => [
                [
                    [
                        "name" => "EXCAVATING, GRADING AND OIL SURFACING",
                        "type" => "A-5",
                        "number" => "12345",
                        "state" => "Arizona"
                    ]
                ]
            ],
            "city" => "TEMPE",
            "state" => "Arizona",
            "zip" => "85283",
            "country" => "US",
            "password" => "asdasd",
            "password_confirmation" => "asdasd",
            "terms" => true,
            "errors" => [
                "first_name" => "",
                "last_name" => "",
                "email" => "",
                "companyName" => "",
                "phoneNumber" => "",
                "addressLine1" => "",
                "addressLine2" => "",
                "city" => "",
                "state" => "",
                "zip" => "The zip field is required.",
                "country" => "",
                "password" => "",
                "password_confirmation" => "",
                "terms" => false
            ],
            "usertype" => "contractor",
            "busy" => true,
            "disabled" => false
        ];
    }

    public function basic_response(): array
    {
        return [
            "redirect" => "/#/home", "user" => [
                "name" => "Shawn Pike",
                "email" => "pike.shawn@gmail.com",
                "usertype" => "contractor",
                "phone" => "4807034902",
                "first_name" => "Shawn",
                "last_name" => "Pike",
                "billing_address" => "705 E OXFORD DR",
                "billing_address_line_2" => "",
                "billing_city" => "TEMPE",
                "billing_state" => "Arizona",
                "billing_zip" => "85283",
                "billing_country" => "US",
                "password_updated" => 1,
                "id" => 1, "location_id" => 1,
                "tax_rate" => 0
            ]
        ];
    }

    public function basic_users_table(): array
    {
        return [
            "name" => "Shawn Pike",
            "first_name" => "Shawn",
            "last_name" => "Pike",
            "email" => "pike.shawn@gmail.com",
            "usertype" => "contractor",
            "password_updated" => true,
            "terms" => true,
            "uses_two_factor_auth" => false,
            "phone" => "4807034902",
            "billing_address" => "705 E OXFORD DR",
            "billing_city" => "TEMPE",
            "billing_state" => "Arizona",
            "billing_zip" => "85283",
            "billing_country" => "US",
        ];
    }

    public function basic_locations_table(): array
    {
        return [
            "user_id" => 1,
            "default" => false,
            "address_line_1" => "705 e oxford dr",
            "city" => "tempe",
            "state" => "arizona",
            "zip" => "85283",
            "country" => "US"
        ];
    }

    public function basic_licenses_table(): array
    {
        return [
            "contractor_id" => 1,
            "name" => "EXCAVATING, GRADING AND OIL SURFACING",
            "number" => "12345",
            "state" => "Arizona",
            "type" => "A-5",
        ];
    }

    public function basic_contractors_table(): array
    {
        return [
            "user_id" => 1,
            "location_id" => 1,
            "free_jobs" => 5,
            "company_name" => "Jemmson",
            "hide_stripe_modal" => 0,
            "payment_type" => "cash"
        ];
    }
}