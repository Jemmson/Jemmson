<?php

use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(App\Job::class, function (Faker $faker) {
    return [
        'customer_id' => 1,
        'contractor_id' => 1,
        'location_id' => 1,
        'job_name' => $faker->name,
        'status' => 'bid.in_progress',
        'bid_price' => rand(1, 10000),
        'completed_bid_date' => Carbon::parse('+1 week'),
        'agreed_start_date' => Carbon::parse('+2 week'),
        'agreed_end_date' => Carbon::parse('+3 week'),
        'actual_end_date' => Carbon::parse('+4 week'),
        'deleted_at' => Carbon::parse('+2 week'),
        'declined_message' => $faker->text,
        'paid_with_cash_message' => $faker->text
    ];
});
