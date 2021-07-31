<?php

namespace Tests\Feature\Payloads\NewJob;

use Illuminate\Foundation\Testing\WithFaker;

class InitiateBidPayload
{

    public function InitiateBidRequest(): array
    {
        return [
            "customerName" => "Kristen Battafarano",
            "email" => "",
            "firstName" => "Kristen",
            "lastName" => " Battafarano",
            "jobName" => "2021-101-Battafarano-Kristen",
            "phone" => "(602)-350-8801",
            "quickbooks_id" => "",
            "isMobile" => true,
            "id" => "",
            "taxRate" => 0,
            "paymentType" => "cash",
            "paymentTypeDefault" => null,
            "errors" => [
                "errors" => []
            ],
            "busy" => false,
            "successful" => false
        ];
    }

    public function InitiateBidResponse(): array
    {
        return [
            "job" => [
                "contractor_id" => 1,
                "customer_id" => 2,
                "job_name" => "2021-101-Battafarano-Kristen",
                "payment_type" => "cash",
                "status" => "bid.initiated",
                "location_id" => null,
                "id" => 1
            ],
            "customer" => [
                "name" => "Kristen Battafarano",
                "phone" => "6023508801",
                "first_name" => "Kristen",
                "last_name" => " Battafarano",
                "usertype" => "customer",
                "password_updated" => false,
                "id" => 2,
                "tax_rate" => 0
            ],
            "jobStatuses" => [
                "job_id" => 1,
                "status_number" => 1,
                "status" => "initiated",
                "id" => 1
            ]
        ];
    }

}