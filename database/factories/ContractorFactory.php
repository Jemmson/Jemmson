<?php

use Faker\Generator as Faker;

$factory->define(App\Contractor::class, function (Faker $faker) {
    return [
        //
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'email_method_of_contact' => 'on',
        'address_line_1' => $faker->streetAddress,
        'address_line_2' => $faker->word,
        'city' => $faker->city,
        'state' => 'AZ',
        'zip' => $faker->postcode,
        'phone_method_of_contact' => 'on',
        'sms_method_of_contact' => 'on'
    ];
});
