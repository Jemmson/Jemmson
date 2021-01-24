<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralContractorStripeNoCustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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

        DB::table('contractors')->insert([
            'id' => 1,
            'user_id' => 1,
            'location_id' => 1,
            'free_jobs' => 5,
            'company_name' => 'Kihei Pool Service',
            'payment_type' => 'cash',
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
    }
}
