<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Contractor::class, function (Faker\Generator $faker) {
    return [
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'email_method_of_contact' => 'on',
        'address_line_1' => $faker->streetAddress,
        'address_line_2' => $faker->word,
        'city' => $faker->city,
        'state' => 'AZ',
        'zip' => $faker->postcode,
        'company_logo_name' => $faker->word,
        'sms_method_of_contact' => 'on',
        'phone_method_of_contact' => 'on',
        'phone_number' => $faker->phoneNumber,
        'company_name' => $faker->word,
    ];
});

$factory->define(App\Customer::class, function (Faker\Generator $faker) {
    return [
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'email_method_of_contact' => 'on',
        'address_line_1' => $faker->streetAddress,
        'address_line_2' => $faker->word,
        'city' => $faker->city,
        'state' => 'AZ',
        'zip' => $faker->postcode,
        'notes' => $faker->paragraph,
        'phone_method_of_contact' => 'on',
        'sms_method_of_contact' => 'on',
        'phone_number' => $faker->phoneNumber,
    ];
});

$factory->define(App\Job::class, function (Faker\Generator $faker) {
    return [
        'customer_id' => function () {
            return factory(App\Customer::class)->create()->id;
        },
        'contractor_id' => function () {
            return factory(App\Contractor::class)->create()->id;
        },
        'address_line_1' => $faker->streetAddress,
        'address_line_2' => $faker->word,
        'city' => $faker->city,
        'state' => 'AZ',
        'zip' => $faker->postcode,
        'completed_bid_date' => $faker->dateTime,
        'bid_price' => rand(10,100).".".rand(10,99),
        'agreed_start_date' => $faker->dateTime,
        'agreed_end_date' => $faker->dateTime,
        'actual_end_date' => $faker->dateTime,
        'job_name' => $faker->company,
    ];
});

$factory->define(App\Task::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'standard_task_id' => rand(1, 1000),
    ];
});

// TODO: figure out three dependecies
$factory->define(App\Time::class, function (Faker\Generator $faker) {
    return [
        'start_time' => $faker->dateTime,
        'end_time' => $faker->dateTime,
        'contractor_id' => function () {
            return factory(App\Contractor::class)->create()->id;
        },
        'job_id' => function () {
            return factory(App\Job::class)->create()->id;
        },
        'task_id' => function () {
            return factory(App\Task::class)->create()->id;
        },
    ];
});

$factory->define(App\Page::class, function (Faker\Generator $faker) {
    return [
        'page_name' => $faker->name,
    ];
});

// TODO: figure out three dependencies with two dependencies depending on the same table
$factory->define(App\Feedback::class, function (Faker\Generator $faker) {
    return [
        'comment' => $faker->paragraph,
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'page_id' => function () {
            return factory(App\Page::class)->create()->id;
        },
        'page_from_id' => function () {
            return factory(App\Page::class)->create()->id;
        },
    ];
});

// TODO: figure out three dependencies
$factory->define(App\Element::class, function (Faker\Generator $faker) {
    return [
        'element_id_name' => $faker->name,
        'page_id' => function () {
            return factory(App\Page::class)->create()->id;
        },
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'time_entered' => $faker->dateTime,
        'time_left' => $faker->dateTime,
    ];
});
