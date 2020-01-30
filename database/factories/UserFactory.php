<?php

use Faker\Generator as Faker;
use App\User;

$factory->define(User::class, function (Faker $faker) {
    return [
        //
        'name' => $faker->name,
        'location_id' => rand(1, 100),
        'email' => $faker->unique()->safeEmail,
        'usertype' => 'contractor',
        'password' => bcrypt('asdasd'),
        'password_updated' => 0,
        'remember_token' => str_random(10),
        'uses_two_factor_auth' => 0,
        'phone' => '0000000000',
        'current_billing_plan' => null
    ];
});
