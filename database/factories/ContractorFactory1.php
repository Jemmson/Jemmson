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
        'company_logo_name' => $faker->word,
        'sms_method_of_contact' => '1',
        'phone_method_of_contact' => '1',
        'company_name' => $faker->word,
    ];
});
