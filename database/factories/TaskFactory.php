<?php

use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(App\Task::class, function (Faker $faker) {
    return [
        //
        'name' => $faker->name,
        'standard_task_id' => 0,
        'contractor_id' => rand(1, 1000),
        'proposed_cust_price' => rand(1, 10000),
        'proposed_sub_price' => rand(1, 10000),
        'sub_instructions' => $faker->text,
        'cust_instructions' => $faker->text,
        'updated_at' => Carbon::now(),
        'created_at' => Carbon::now(),
    ];
});
