<?php

use Faker\Generator as Faker;

$factory->define(App\Location::class, function (Faker $faker) {
    return [
        'user_id' => 0,
        'default' => 1,
        'address_line_1' => $faker->address,
        'address_line_2' => '',
        'city' => $faker->city,
        'state' => 'AZ',
        'zip' => '85283',
        'area' => '',
    ];
});


$factory->define(App\User::class, function (Faker $faker) {
    return [
//        'location_id' => $faker->name,
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'usertype' => 'contractor',
        'password' => bcrypt('asdasd'),
        'password_updated' => 1,
        'remember_token' => '',
        'photo_url' => '',
        'logo_url' => '',
        'uses_two_fator_auth' => 0,
        'authy_id' => '',
        'country_code' => '',
        'phone' => '',
        'two_factor_reset_code' => '',
        'current_team_id' => 0,
        'stripe_id' => '',
        'current_billing_plan' => '',
        'card_brand' => '',
        'card_last_four' => '',
        'card_country' => '',
        'billing_address' => '',
        'billing_address_line_2' => '',
        'billing_city' => '',
        'billing_state' => '',
        'billing_zip' => '',
        'billing_country' => '',
        'extra_billing_information' => '',
        'trial_ends_at' => '2018-12-31',
        'last_read_announcements_at' => '2018-12-31',
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Contractor::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'location_id' => 0,
        'free_jobs' => 1,
        'company_name' => '',
        'company_logo_name' => '',
        'email_method_of_contact' => 1,
        'sms_method_of_contact' => 1,
        'phone_method_of_contact' => 1,
    ];
});
