<?php

use Faker\Generator as Faker;

$factory->define(App\QuickbooksContractor::class, function (Faker $faker) {
    return [
        'quickbooks_id' => 7,
        'contractor_id' => 1,
        'sub_contractor_id' => 7,
        'company_name' => $faker->company,
        'given_name' => $faker->name,
        'middle_name' => $faker->name,
        'family_name' => $faker->name,
        'fully_qualified_name' => '',
        'primary_phone' => $faker->phoneNumber,
        'primary_email_addr' => $faker->email,
        'created_at' => '2019-05-05 01:17:28',
        'updated_at' => '2019-05-05 01:17:28'
    ];
});
