<?php

use Faker\Generator as Faker;

$factory->define(App\Contractor::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'location_id' => 1,
        'free_jobs' => 5,
        'company_name' => $faker->name,
        'company_logo_name' => $faker->name,
        'email_method_of_contact' => 1,
        'sms_method_of_contact' => 1,
        'phone_method_of_contact' => 1
    ];
});
