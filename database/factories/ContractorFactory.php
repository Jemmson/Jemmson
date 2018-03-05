<?php

use Faker\Generator as Faker;

$factory->define(App\Contractor::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'email_method_of_contact' => '1',
        'phone_method_of_contact' => '1',
        'sms_method_of_contact' => '1'
    ];
});
