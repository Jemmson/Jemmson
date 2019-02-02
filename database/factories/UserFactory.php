<?php

use Faker\Generator as Faker;

$factory->define(App\User::class, function (Faker $faker) {
    return [
        //
        'name' => $faker->name,
        'location' => rand(1, 100),
        'email' => $faker->unique()->safeEmail,
        'usertype' => 'contractor',
        'password' => bcrypt('asdasd'),
        'password_updated' => 0,
        'remember_token' => str_random(10),
        'uses_two_factor_auth' => 0,
        'phone' => '0000000000'
    ];
});
