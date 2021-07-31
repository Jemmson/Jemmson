<?php

namespace Tests\Feature\Payloads\NewJob;

use Illuminate\Foundation\Testing\WithFaker;

class NewJobInitiatedPayload
{

    public function getInitiatedJobResponse(): array
    {
        return [
            "id" => 1,
            "agreed_end_date" => null,
            "agreed_start_date" => null,
            "actual_end_date" => null,
            "bid_price" => 0,
            "completed_bid_date" => null,
            "contractor_id" => 1,
            "customer_id" => 2,
            "declined_message" => null,
            "job_name" => "2021-101-Pike-Shawn",
            "location_id" => null,
            "paid_jemmson_cash_fee" => 0,
            "paid_with_cash_message" => null,
            "payment_type" => "cash",
            "qb_estimate_id" => "NULL",
            "status" => "bid.initiated",
            "deleted_at" => null,
            "job_tasks" => [],
            "location" => null,
            "images" => [],
            "job_statuses" => [
                [
                    "id" => 1,
                    "job_id" => 1,
                    "status" => "initiated",
                    "status_number" => 1,
                    "sent_on" => null,
                    "deleted_at" => null
                ]
            ],
            "contractor" => [
                "id" => 1,
                "location_id" => 1,
                "name" => "General Contractor",
                "first_name" => "General",
                "last_name" => "Contractor",
                "email" => "jemmsoninc@gmail.com",
                "usertype" => "contractor",
                "password_updated" => 1,
                "terms" => 1,
                "photo_url" => "https://www.gravatar.com/avatar/b79a143c3fc9e45c8f1344369273cb0c.jpg?s=200&d=mm",
                "logo_url" => null,
                "uses_two_factor_auth" => false,
                "phone" => "4806226441",
                "plan" => null,
                "customer_stripe_id" => null,
                "plan_start_date" => null,
                "two_factor_reset_code" => null,
                "current_team_id" => null,
                "current_billing_plan" => null,
                "billing_state" => "AZ",
                "last_read_announcements_at" => null,
                "deleted_at" => null,
                "stripe_id" => null,
                "trial_ends_at" => null,
                "contractor" => [
                    "id" => 1,
                    "user_id" => 1,
                    "location_id" => 1,
                    "free_jobs" => 4,
                    "company_name" => "Kihei Pool Service",
                    "accounting_software" => null,
                    "company_logo_name" => null,
                    "hide_stripe_modal" => 0,
                    "email_method_of_contact" => null,
                    "sms_method_of_contact" => null,
                    "payment_type" => "cash",
                    "phone_method_of_contact" => null,
                    "deleted_at" => null
                ],
                "tax_rate" => 0
            ],
            "customer" => [
                "id" => 2,
                "name" => "Shawn  Pike",
                "customer" => [
                    "id" => 1,
                    "user_id" => 2,
                    "location_id" => null,
                    "email_method_of_contact" => null,
                    "phone_method_of_contact" => null,
                    "sms_method_of_contact" => null,
                    "notes" => null,
                    "deleted_at" => null
                ],
                "tax_rate" => 0
            ]
        ];
    }
}