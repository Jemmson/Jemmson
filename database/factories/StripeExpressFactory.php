<?php

use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(App\StripeExpress::class, function (Faker $faker) {
    return [
        'contractor_id' => 1,
        'access_token' => 'sk_test_6AIpa8maI15LwuJ2np4hpAhD00hXsKOhiW',
        'livemode' => 0,
        'refresh_token' => 'rt_GcPRm5AsYEyMfePvM3XFRnrmnQe1KrQNhJuUOFU2rcqkPKAO',
        'token_type' => 'bearer',
        'stripe_publishable_key' => 'pk_test_PfVfEmOVvJtePow6qro2u63P00sbnHsHL0',
        'stripe_user_id' => 'acct_1FOafmFB3JWNIBCK',
        'scope' => 'read_write'
    ];
});
