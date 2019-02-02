<?php

use Faker\Generator as Faker;

$factory->define(App\Location::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'default' => 1,
        'address_line_1' => $faker->address,
        'address_line_2' => '',
        'city' => $faker->city,
        'state' => '',
        'zip' => '85283',
        'area' => '',
    ];
});
