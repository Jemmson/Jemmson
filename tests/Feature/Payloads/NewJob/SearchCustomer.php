<?php

namespace Tests\Feature\Payloads\NewJob;

use Illuminate\Foundation\Testing\WithFaker;

class SearchCustomer
{
    use WithFaker;

    public function customerShawn(): array
    {
        return [
            [
                "id" => 2,
                "name" => "Shawn  Pike",
                "first_name" => "Shawn",
                "last_name" => "Pike",
                "phone" => "4807034902",
                "email" => "pike.shawn@gmail.com",
                "tax_rate" => 0,
                "payment_type" => null,
                "quickbooks_id" => null,
                "jobNumber" => 102
            ]
        ];
    }

    public function customerJakson(): array
    {
        return [
            [
                "id" => 3,
                "name" => "Jakson  Thiel",
                "first_name" => "Jakson",
                "last_name" => "Thiel",
                "phone" => "6024326933",
                "email" => "jakson.thiel@gmail.com",
                "tax_rate" => 0,
                "payment_type" => null,
                "quickbooks_id" => null,
                "jobNumber" => 102
            ]
        ];
    }

}