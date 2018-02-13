<?php

use Faker\Generator as Faker;

//$factory->define(App\User::class, function (Faker $faker) {
//    return [
//        'name' => $faker->name,
//        'email' => $faker->unique()->safeEmail,
//        'password' => bcrypt('secret'),
//        'remember_token' => str_random(10),
//    ];
//});

$factory->define(App\Contractor::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'email_method_of_contact' => '1',
        'address_line_1' => $faker->streetAddress,
        'address_line_2' => $faker->word,
        'city' => $faker->city,
        'state' => 'AZ',
        'zip' => $faker->postcode,
        'company_logo_name' => $faker->word,
        'sms_method_of_contact' => '1',
        'phone_method_of_contact' => '1',
        'phone_number' => $faker->phoneNumber,
        'company_name' => $faker->word,
    ];
});
