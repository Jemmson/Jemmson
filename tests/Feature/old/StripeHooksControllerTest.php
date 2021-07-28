<?php

namespace Tests\Feature;

use App\Http\Controllers\StripeHooksController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class StripeHooksControllerTest extends TestCase
{
    use RefreshDatabase;

    /**  @test */
    function that_a_verification_is_properly_updated_from_the_account_updated_event() {

        // GIVEN
        $request = $this->accountUpdatedPayload();
        $sthc = new StripeHooksController();

        // ACTION
        $sthc->accountUpdated($request);

        // ASSERTION
        $this->assertDatabaseHas('stripe_events', [
           'account_id' => 'acct_1GQZp9AChMTtsYfn',
           'account_updated' => $this->accountUpdatedJson()
        ]);

    }

    private function accountUpdatedJson()
    {
        return json_encode($this->accountUpdatedPayload()->json());
    }

    private function accountUpdatedPayload()
    {
        return ‌Illuminate\Http\Request::__set_state(array(
            'json' =>
                Symfony\Component\HttpFoundation\ParameterBag::__set_state(array(
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
            'convertedFiles' =>
                array (
                ),
            'userResolver' =>
                Closure::__set_state(array(
                )),
            'routeResolver' =>
                Closure::__set_state(array(
                )),
            'attributes' =>
                Symfony\Component\HttpFoundation\ParameterBag::__set_state(array(
                    'parameters' =>
                        array (
                        ),
                )),
            'request' =>
                Symfony\Component\HttpFoundation\ParameterBag::__set_state(array(
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
            'query' =>
                Symfony\Component\HttpFoundation\ParameterBag::__set_state(array(
                    'parameters' =>
                        array (
                        ),
                )),
            'server' =>
                Symfony\Component\HttpFoundation\ServerBag::__set_state(array(
                    'parameters' =>
                        array (
                            'REDIRECT_STATUS' => '200',
                            'HTTP_HOST' => 'localhost:9500',
                            'HTTP_USER_AGENT' => 'Stripe/1.0 (+https://stripe.com/docs/webhooks)',
                            'CONTENT_LENGTH' => '3537',
                            'HTTP_ACCEPT' => '*/*; q=0.5, application/xml',
                            'HTTP_CACHE_CONTROL' => 'no-cache',
                            'CONTENT_TYPE' => 'application/json; charset=utf-8',
                            'HTTP_STRIPE_SIGNATURE' => 't=1585145373,v1=62efc31a2a8a9c4c7cb53f04c43dd558337cf9e0849bcccd50d450b8631a4695,v0=af8f622afb4fb59486729e5d978c83a4d2a86c1dc653a9da05161b210226be60',
                            'HTTP_ACCEPT_ENCODING' => 'gzip',
                            'PATH' => './vendor/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin',
                            'SERVER_SIGNATURE' => '<address>Apache/2.4.25 (Debian) Server at localhost Port 9500</address>
',
                            'SERVER_SOFTWARE' => 'Apache/2.4.25 (Debian)',
                            'SERVER_NAME' => 'localhost',
                            'SERVER_ADDR' => '172.25.0.4',
                            'SERVER_PORT' => '9500',
                            'REMOTE_ADDR' => '172.25.0.1',
                            'DOCUMENT_ROOT' => '/var/www/html/public',
                            'REQUEST_SCHEME' => 'http',
                            'CONTEXT_PREFIX' => '',
                            'CONTEXT_DOCUMENT_ROOT' => '/var/www/html/public',
                            'SERVER_ADMIN' => 'webmaster@localhost',
                            'SCRIPT_FILENAME' => '/var/www/html/public/index.php',
                            'REMOTE_PORT' => '38884',
                            'REDIRECT_URL' => '/hooks',
                            'GATEWAY_INTERFACE' => 'CGI/1.1',
                            'SERVER_PROTOCOL' => 'HTTP/1.1',
                            'REQUEST_METHOD' => 'POST',
                            'QUERY_STRING' => '',
                            'REQUEST_URI' => '/hooks',
                            'SCRIPT_NAME' => '/index.php',
                            'PHP_SELF' => '/index.php',
                            'REQUEST_TIME_FLOAT' => 1585145373.182,
                            'REQUEST_TIME' => 1585145373,
                            'argv' =>
                                array (
                                ),
                            'argc' => 0,
                        ),
                )),
            'files' =>
                Symfony\Component\HttpFoundation\FileBag::__set_state(array(
                    'parameters' =>
                        array (
                        ),
                )),
            'cookies' =>
                Symfony\Component\HttpFoundation\ParameterBag::__set_state(array(
                    'parameters' =>
                        array (
                        ),
                )),
            'headers' =>
                Symfony\Component\HttpFoundation\HeaderBag::__set_state(array(
                    'headers' =>
                        array (
                            'host' =>
                                array (
                                    0 => 'localhost:9500',
                                ),
                            'user-agent' =>
                                array (
                                    0 => 'Stripe/1.0 (+https://stripe.com/docs/webhooks)',
                                ),
                            'content-length' =>
                                array (
                                    0 => '3537',
                                ),
                            'accept' =>
                                array (
                                    0 => '*/*; q=0.5, application/xml',
                                ),
                            'cache-control' =>
                                array (
                                    0 => 'no-cache',
                                ),
                            'content-type' =>
                                array (
                                    0 => 'application/json; charset=utf-8',
                                ),
                            'stripe-signature' =>
                                array (
                                    0 => 't=1585145373,v1=62efc31a2a8a9c4c7cb53f04c43dd558337cf9e0849bcccd50d450b8631a4695,v0=af8f622afb4fb59486729e5d978c83a4d2a86c1dc653a9da05161b210226be60',
                                ),
                            'accept-encoding' =>
                                array (
                                    0 => 'gzip',
                                ),
                        ),
                    'cacheControl' =>
                        array (
                            'no-cache' => true,
                        ),
                )),
            'content' => '{
  "id": "evt_1GQZvEAChMTtsYfnWsrrY8IV",
  "object": "event",
  "account": "acct_1GQZp9AChMTtsYfn",
  "api_version": "2020-03-02",
  "created": 1585145372,
  "data": {
    "object": {
      "id": "acct_1GQZp9AChMTtsYfn",
      "object": "account",
      "business_profile": {
        "mcc": "5734",
        "name": null,
        "support_address": null,
        "support_email": null,
        "support_phone": "+16023508801",
        "support_url": null,
        "url": null
      },
      "capabilities": {
        "card_payments": "pending",
        "transfers": "pending"
      },
      "charges_enabled": false,
      "country": "US",
      "default_currency": "usd",
      "details_submitted": true,
      "email": "kbattafarano@gmail.com",
      "payouts_enabled": false,
      "settings": {
        "branding": {
          "icon": null,
          "logo": null,
          "primary_color": null
        },
        "card_payments": {
          "statement_descriptor_prefix": null,
          "decline_on": {
            "avs_failure": false,
            "cvc_failure": false
          }
        },
        "dashboard": {
          "display_name": null,
          "timezone": "Etc/UTC"
        },
        "payments": {
          "statement_descriptor": "",
          "statement_descriptor_kana": null,
          "statement_descriptor_kanji": null
        },
        "payouts": {
          "debit_negative_balances": true,
          "schedule": {
            "delay_days": 2,
            "interval": "daily"
          },
          "statement_descriptor": null
        }
      },
      "type": "express",
      "created": 1585145042,
      "external_accounts": {
        "object": "list",
        "data": [
          {
            "id": "ba_1GQZphAChMTtsYfnm4m2q3eH",
            "object": "bank_account",
            "account": "acct_1GQZp9AChMTtsYfn",
            "account_holder_name": null,
            "account_holder_type": null,
            "bank_name": "STRIPE TEST BANK",
            "country": "US",
            "currency": "usd",
            "default_for_currency": true,
            "fingerprint": "dXe0Guhfuq2aF7hk",
            "last4": "6789",
            "metadata": {
            },
            "routing_number": "110000000",
            "status": "new"
          }
        ],
        "has_more": false,
        "total_count": 1,
        "url": "/v1/accounts/acct_1GQZp9AChMTtsYfn/external_accounts"
      },
      "login_links": {
        "object": "list",
        "total_count": 0,
        "has_more": false,
        "url": "/v1/accounts/acct_1GQZp9AChMTtsYfn/login_links",
        "data": [

        ]
      },
      "metadata": {
      },
      "requirements": {
        "current_deadline": null,
        "currently_due": [
          "individual.id_number"
        ],
        "disabled_reason": "requirements.past_due",
        "errors": [

        ],
        "eventually_due": [
          "individual.id_number"
        ],
        "past_due": [
          "individual.id_number"
        ],
        "pending_verification": [

        ]
      }
    },
    "previous_attributes": {
      "requirements": {
        "currently_due": [

        ],
        "disabled_reason": "requirements.pending_verification",
        "eventually_due": [

        ],
        "past_due": [

        ],
        "pending_verification": [
          "individual.id_number"
        ]
      }
    }
  },
  "livemode": false,
  "pending_webhooks": 1,
  "request": {
    "id": null,
    "idempotency_key": null
  },
  "type": "account.updated"
}',
            'languages' => NULL,
            'charsets' => NULL,
            'encodings' => NULL,
            'acceptableContentTypes' => NULL,
            'pathInfo' => '/hooks',
            'requestUri' => '/hooks',
            'baseUrl' => '',
            'basePath' => NULL,
            'method' => 'POST',
            'format' => NULL,
            'session' =>
                Illuminate\Session\Store::__set_state(array(
                    'id' => 'DgzF3RStZ1Y3TanMMxNetOZdoazdX5nrbF9FeoKW',
                    'name' => 'laravel_session',
                    'attributes' =>
                        array (
                            '_token' => 'yhATOdDbAAAmpyex0WSMaclt8h3ny5k1hA93zr9k',
                        ),
                    'handler' =>
                        Illuminate\Session\FileSessionHandler::__set_state(array(
                            'files' =>
                                Illuminate\Filesystem\Filesystem::__set_state(array(
                                )),
                            'path' => '/var/www/html/storage/framework/sessions',
                            'minutes' => 120,
                        )),
                    'started' => true,
                )),
            'locale' => NULL,
            'defaultLocale' => 'en',
            'preferredFormat' => NULL,
            'isHostValid' => true,
            'isForwardedValid' => true,
        ));
    }
}
