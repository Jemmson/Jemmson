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

    public function basic_users_table(): array
    {
        return [
            "name" => "Kristen Battafarano",
            "email" => NULL,
            "first_name" => "Kristen",
            "last_name" => " Battafarano",
            "usertype" => "customer",
            "password_updated" => 0,
            "terms" => 0,
            "two_factor_reset_code" => NULL,
            "phone" => "6023508801",
            "billing_address" => NULL,
            "billing_city" => NULL,
            "billing_state" => NULL,
            "billing_zip" => NULL,
            "billing_country" => "US",
        ];
    }

    public function basic_contractor_customer_table(): array
    {
        return [
            'id' => 1,
            'contractor_user_id' => 1,
            'customer_user_id' => 2
        ];
    }

    public function basic_jobs_table(): array
    {
        return [
            'id' => 1,
            'contractor_id' => 1,
            'customer_id' => 2,
            'bid_price' => 0,
            'job_name' => '2021-101-Battafarano-Kristen',
            'location_id' => NULL,
            'paid_jemmson_cash_fee' => 0,
            'payment_type' => 'cash',
            'status' => 'bid.initiated',
        ];
    }

    public function basic_user_tokens_table_email(): array
    {
        return [
            'id' => 1,
            'job_id' => 1,
            'user_id' => 2,
            'job_step' => 'initiated',
            'type' => 'email'
        ];
    }

    public function basic_user_tokens_table_text(): array
    {
        return [
            'id' => 2,
            'job_id' => 1,
            'user_id' => 2,
            'job_step' => 'initiated',
            'type' => 'text'
        ];
    }

    public function basic_job_status_table(): array
    {
        return [
            'id' => 1,
            'job_id' => 1,
            'status' => 'initiated',
            'status_number' => 1
        ];
    }

    public function basic_customers_table(): array
    {
        return [];
    }

}