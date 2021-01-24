<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralContractorStripeOneCustomerInitiatedNotRegisteredSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('contractor_customer')->insert([
            'id' => 1,
            'contractor_user_id' => 1,
            'customer_user_id' => 2
        ]);

        DB::table('contractors')->insert([
            'id' => 1,
            'user_id' => 1,
            'location_id' => 1,
            'free_jobs' => 5,
            'company_name' => 'Kihei Pool Service',
            'payment_type' => 'cash',
        ]);

        DB::table('customers')->insert([
            'id' => 1,
            'user_id' => 2
        ]);

        DB::table('job_status')->insert([
            'id' => 1,
            'job_id' => 1,
            'status' => 'initiated',
            'status_number' => 1
        ]);

        DB::table('jobs')->insert([
            'id' => 1,
            'contractor_id' => 1,
            'customer_id' => 2,
            'job_name' => '2021-101-Pike-Shawn',
            'payment_type' => 'creditCard',
            'status' => 'bid.initiated',
        ]);

        DB::table('licenses')->insert([
            'id' => 1,
            'contractor_id' => 1,
            'name' => 'PIERS AND FOUNDATIONS',
            'number' => 'AZ12345',
            'state' => 'Arizona',
            'type' => 'A-7',
        ]);

        DB::table('locations')->insert([
            'id' => 1,
            'user_id' => 1,
            'address_line_1' => '705 east oxford drive',
            'city' => 'tempe',
            'state' => 'az',
            'zip' => '85283',
            'country' => 'US'
        ]);

        DB::table('stripe_expresses')->insert([
            'id' => 1,
            'contractor_id' => 1,
            'access_token' => 'sk_test_51CENK6Bp6bf1LkLwqJOmg29UbIRXNpXeh0CQkakZMKnZxFuovBydpb0x4VQf5QIdQI8JWQtjG41eCjojcFsQBm9P002iIzIyxs',
            'refresh_token' => 'rt_Io29WpSFXXevVMz5CHkIyuMNtdxMszURqTHeUZ8mIbkOxdYr',
            'token_type' => 'bearer',
            'stripe_publishable_key' => 'pk_test_51CENK6Bp6bf1LkLwMa7lU2yoxfA99fg1qHgnP425Jk3wv7INX2BtxDnWQtwikG4IoJPpOA1MXdkcNAG7OFqvuMsi00h6DF1K55',
            'stripe_user_id' => 'acct_1CENK6Bp6bf1LkLw',
            'scope' => 'express',
        ]);

        DB::table('user_tokens')->insert([
            'id' => 1,
            'job_id' => 1,
            'user_id' => 2,
            'expires_at' => '2021-01-23 06:59:37',
            'job_step' => 'initiated',
            'token' => '6LvN722vYXqpYzbM',
            'type' => 'email'
        ]);

        DB::table('user_tokens')->insert([
            'id' => 2,
            'job_id' => 1,
            'user_id' => 2,
            'expires_at' => '2021-01-23 06:59:37',
            'job_step' => 'initiated',
            'token' => 'eiBMjdTgKOi6r8Qj',
            'type' => 'text'
        ]);

        DB::table('users')->insert([
            'id' => 1,
            'location_id' => 1,
            'name' => 'General Contractor',
            'first_name' => 'General',
            'last_name' => 'Contractor',
            'email' => 'jemmsoninc@gmail.com',
            'usertype' => 'contractor',
            'password' => '$2y$10$DhKe88L4Hxgbdm6qYwfSnezUFdPCF6jaFyjQrSBPYOWV.RnbklkWy',
            'password_updated' => 1,
            'terms' => 1,
            'phone' => '4806226441',
            'customer_stripe_id' => 'acct_1CENK6Bp6bf1LkLw',
            'billing_address' => '705 East Oxford Drive',
            'billing_city' => 'Tempe',
            'billing_state' => 'AZ',
            'billing_zip' => '85283'
        ]);

        DB::table('users')->insert([
            'id' => 2,
            'location_id' => NULL,
            'name' => 'Shawn Pike',
            'first_name' => 'Shawn',
            'last_name' => 'Pike',
            'email' => NULL,
            'usertype' => 'customer',
            'password' => '$2y$10$QzbLXmtG8SE9FnUqU74uee5c5u0.6MrO4ZuF69h0N/PutVHGfZn6u',
            'password_updated' => 0,
            'terms' => 0,
            'phone' => '4807034902',
            'customer_stripe_id' => NULL,
            'billing_address' => NULL,
            'billing_city' => NULL,
            'billing_state' => NULL,
            'billing_zip' => NULL,
            'billing_country' => 'US',
        ]);
    }
}
