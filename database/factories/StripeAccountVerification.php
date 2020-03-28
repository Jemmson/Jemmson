<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\StripeAccountVerification;
use Faker\Generator as Faker;

$factory->define(StripeAccountVerification::class, function (Faker $faker) {
    return [
        //
        'account_id' => 'acct_1GQZp9AChMTtsYfn',
        'current_deadline' => NULL,
        'currently_due' => json_encode(array (0 => 'individual.id_number',)),
        'disabled_reason' => 'requirements.past_due',
        'errors' => json_encode(array ()),
        'eventually_due' => json_encode(array (0 => 'individual.id_number',)),
        'past_due' => json_encode(array (0 => 'individual.id_number',)),
        'pending_verification' => json_encode(array ()),
    ];
});
