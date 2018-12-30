<?php

use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(App\BidContractorJobTask::class, function (Faker $faker) {
    return [
        'contractor_id' => 1,
        'job_task_id' => 1,
        'bid_price' => rand(1, 10000),
        'status' => 'sent',
        'proposed_start_date' => Carbon::parse('+1 week'),
        'bid_description' => $faker->text,
        'accepted' => 0,
        'payment_type' => 'cash'
    ];
});
