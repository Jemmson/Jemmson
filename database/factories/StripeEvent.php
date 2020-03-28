<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\StripeEvent;
use Faker\Generator as Faker;

$factory->define(StripeEvent::class, function (Faker $faker) {
    return [
        'account_id' => 'acct_1GQZp9AChMTtsYfn',
        'capability_updated' => NULL,
        'account_updated' => json_encode(array(
            'parameters' =>
                array (
                    'id' => 'evt_1GQZvEAChMTtsYfnWsrrY8IV',
                    'object' => 'event',
                    'account' => 'acct_1GQZp9AChMTtsYfn',
                    'api_version' => '2020-03-02',
                    'created' => 1585145372,
                    'data' =>
                        array (
                            'object' =>
                                array (
                                    'id' => 'acct_1GQZp9AChMTtsYfn',
                                    'object' => 'account',
                                    'business_profile' =>
                                        array (
                                            'mcc' => '5734',
                                            'name' => NULL,
                                            'support_address' => NULL,
                                            'support_email' => NULL,
                                            'support_phone' => '+16023508801',
                                            'support_url' => NULL,
                                            'url' => NULL,
                                        ),
                                    'capabilities' =>
                                        array (
                                            'card_payments' => 'pending',
                                            'transfers' => 'pending',
                                        ),
                                    'charges_enabled' => false,
                                    'country' => 'US',
                                    'default_currency' => 'usd',
                                    'details_submitted' => true,
                                    'email' => 'kbattafarano@gmail.com',
                                    'payouts_enabled' => false,
                                    'settings' =>
                                        array (
                                            'branding' =>
                                                array (
                                                    'icon' => NULL,
                                                    'logo' => NULL,
                                                    'primary_color' => NULL,
                                                ),
                                            'card_payments' =>
                                                array (
                                                    'statement_descriptor_prefix' => NULL,
                                                    'decline_on' =>
                                                        array (
                                                            'avs_failure' => false,
                                                            'cvc_failure' => false,
                                                        ),
                                                ),
                                            'dashboard' =>
                                                array (
                                                    'display_name' => NULL,
                                                    'timezone' => 'Etc/UTC',
                                                ),
                                            'payments' =>
                                                array (
                                                    'statement_descriptor' => '',
                                                    'statement_descriptor_kana' => NULL,
                                                    'statement_descriptor_kanji' => NULL,
                                                ),
                                            'payouts' =>
                                                array (
                                                    'debit_negative_balances' => true,
                                                    'schedule' =>
                                                        array (
                                                            'delay_days' => 2,
                                                            'interval' => 'daily',
                                                        ),
                                                    'statement_descriptor' => NULL,
                                                ),
                                        ),
                                    'type' => 'express',
                                    'created' => 1585145042,
                                    'external_accounts' =>
                                        array (
                                            'object' => 'list',
                                            'data' =>
                                                array (
                                                    0 =>
                                                        array (
                                                            'id' => 'ba_1GQZphAChMTtsYfnm4m2q3eH',
                                                            'object' => 'bank_account',
                                                            'account' => 'acct_1GQZp9AChMTtsYfn',
                                                            'account_holder_name' => NULL,
                                                            'account_holder_type' => NULL,
                                                            'bank_name' => 'STRIPE TEST BANK',
                                                            'country' => 'US',
                                                            'currency' => 'usd',
                                                            'default_for_currency' => true,
                                                            'fingerprint' => 'dXe0Guhfuq2aF7hk',
                                                            'last4' => '6789',
                                                            'metadata' =>
                                                                array (
                                                                ),
                                                            'routing_number' => '110000000',
                                                            'status' => 'new',
                                                        ),
                                                ),
                                            'has_more' => false,
                                            'total_count' => 1,
                                            'url' => '/v1/accounts/acct_1GQZp9AChMTtsYfn/external_accounts',
                                        ),
                                    'login_links' =>
                                        array (
                                            'object' => 'list',
                                            'total_count' => 0,
                                            'has_more' => false,
                                            'url' => '/v1/accounts/acct_1GQZp9AChMTtsYfn/login_links',
                                            'data' =>
                                                array (
                                                ),
                                        ),
                                    'metadata' =>
                                        array (
                                        ),
                                    'requirements' =>
                                        array (
                                            'current_deadline' => NULL,
                                            'currently_due' =>
                                                array (
                                                    0 => 'individual.id_number',
                                                ),
                                            'disabled_reason' => 'requirements.past_due',
                                            'errors' =>
                                                array (
                                                ),
                                            'eventually_due' =>
                                                array (
                                                    0 => 'individual.id_number',
                                                ),
                                            'past_due' =>
                                                array (
                                                    0 => 'individual.id_number',
                                                ),
                                            'pending_verification' =>
                                                array (
                                                ),
                                        ),
                                ),
                            'previous_attributes' =>
                                array (
                                    'requirements' =>
                                        array (
                                            'currently_due' =>
                                                array (
                                                ),
                                            'disabled_reason' => 'requirements.pending_verification',
                                            'eventually_due' =>
                                                array (
                                                ),
                                            'past_due' =>
                                                array (
                                                ),
                                            'pending_verification' =>
                                                array (
                                                    0 => 'individual.id_number',
                                                ),
                                        ),
                                ),
                        ),
                    'livemode' => false,
                    'pending_webhooks' => 1,
                    'request' =>
                        array (
                            'id' => NULL,
                            'idempotency_key' => NULL,
                        ),
                    'type' => 'account.updated',
                ),
        )),
        ];
});
