<?php

use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(App\JobTask::class, function (Faker $faker) {
    return [
        'job_id' => 1,
        'task_id' => 1,
        'bid_id' => 1,
        'location_id' => 1,
        'contractor_id' => 1,
        'status' => 'bid_task.initiated',
        'cust_final_price' => rand(1, 10000),
        'sub_final_price' => rand(1, 10000),
        'start_when_accepted' => 0,
        'stripe' => 0,
        'start_date' => Carbon::parse('+2 week'),
        'deleted_at' => Carbon::parse('+1 week'),
//        'stripe_transfer_id' => NULL,
        'customer_message' => $faker->text,
        'sub_message' => $faker->text,
        'qty' => rand(1, 30),
        'sub_sets_own_price_for_job' => 0,
        'declined_message' => $faker->text,
        'unit_price' => rand(1, 10000)
    ];
});
