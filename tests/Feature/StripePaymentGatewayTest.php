<?php

namespace Tests\Feature;

use App\ContractorCustomer;
use App\StripeExpress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\JobTaskTrait;
use Tests\Feature\Traits\JobTrait;
use Tests\Feature\Traits\TaskTrait;
use Tests\Feature\Traits\UserTrait;
use Tests\Feature\Traits\UtilitiesTrait;
use Tests\TestCase;
use App\TransferGroup;
use Laravel\Spark\Services\Stripe;
use Whoops\Util\TemplateHelper;

class StripePaymentGatewayTest extends TestCase
{

    use WithFaker;
    use UtilitiesTrait;
    use Setup;
    use UserTrait;
    use JobTaskTrait;
    use TaskTrait;
    use JobTrait;
    use RefreshDatabase;

    public function test_charges_with_a_valid_payment_gateway_are_successful()
    {

        $paymentGateway = new StripePaymentGateway;

        $token = \Stripe\Token::create([
            "number" => "4242424242424242",
            "exp_month" => "1",
            "exp_year" => date('Y') + 1,
            "cvc" => "123"
        ], ['api_key' => config('services.stripe.secret')])->id;


    }

    /**  @test */
    function that_can_connect_using_the_oauth_link()
    {
        //

        $clientId = 'ca_Cb5HGB6tDEhW7HxWGxFjoyiR7ds1S9ca';
        $redirect_uri = 'http://localhost://9500';
        $responseType = 'code';
        $scope = 'read_write';
        $stripeOauthEndpoint = 'https://connect.stripe.com/oauth/authorize';
        $state = uniqid() . "-" . uniqid() . "-" . uniqid() . "-" . uniqid(); // prevent CSRF attacks

//        ***************************
//        ** Step 1
//        ** Oauth link

        echo "$stripeOauthEndpoint?response_type=$responseType&client_id=$clientId&scope=$scope&state=$state";
//        Standard
//        https://dashboard.stripe.com/oauth/authorize?response_type=code&client_id=ca_Cb5HGB6tDEhW7HxWGxFjoyiR7ds1S9ca&scope=read_write

//        Express
//        https://dashboard.stripe.com/express/oauth/authorize?response_type=code&client_id=ca_Cb5HGB6tDEhW7HxWGxFjoyiR7ds1S9ca&scope=read_write

//        ***************************
//        ** Step 2
//        ** User connects their info to Stripe


//        ***************************
//        ** Step 3
//        ** User is redirected back to my site
//        ** https://gemsub.com/stripe/express/auth?scope=read_write&code={AUTHORIZATION_CODE}
//        ** An Error will return if there is an issue with the Oauth -> error=
//        ** https://gemsub.com/stripe/express/auth?error=access_denied&error_description=The%20user%20denied%20your%20request
//**
//        http://localhost:9500/stripe/express/auth?
//**        state=5e2903fd2b391-5e2903fd2b393-5e2903fd2b394-5e2903fd2b395
//**        &scope=read_write
//**        &code=ac_Gb7cK64Oo5CW9YeXWrkMEzL1mnodWH2V


//        ***************************
//        ** Step 4
//        ** Fetch the user's credentials from Stripe

        // Set your secret key: remember to change this to your live secret key in production
        // See your keys here: https://dashboard.stripe.com/account/apikeys
        \Stripe\Stripe::setApiKey('sk_test_ebg7SjOI3rsZkeV5SZsUkOon');

        $response = \Stripe\OAuth::token([
            'grant_type' => 'authorization_code',
            'code' => 'ac_123456789',
        ]);

        // Access the connected account id in the response
//                $connected_account_id = $response->stripe_user_id;

    }

    public function setupWithGeneralAndSubWithCC()
    {
        $general = $this->createContractor();
        $this->createLocation($general->id);

        $customer = $this->createCustomer();
        $location = $this->createLocation($customer->id);

        $job = $this->createJob($customer->id, $general->id, $location->id, 'bid.initiated');
        $this->addCustomerToContractorCustomerTable($customer->id, $general->id);
        $task = $this->createTask($general->id);
        $jobTask = $this->createJobTask($job->id, $task->id, $location->id, $general->id, 'initiated');

        $this->assertDatabaseHas('tasks', [
            "id" => $task->id,
            "contractor_id" => $general->id
        ]);

        $sub = $this->createSub($general, $jobTask->id, $jobTask);
        $this->createLocation($sub->id);

        $this->setsUpStipe($sub, 'sub');
        $this->setsUpStipe($general, 'general');

        return [
            "general" => $general,
            "customer" => $customer,
            "job" => $job,
            "task" => $task,
            "jobTask" => $jobTask,
            "sub" => $sub
        ];
    }

    /**  @test */
    function test_that_I_can_charge_a_customer()
    {
        //

        $setup = $this->setupWithGeneralAndSubWithCC();
        $sub = $setup['sub'];
        $general = $setup['general'];
        $customer = $setup['customer'];
        $jobTask = $setup['jobTask'];
        $job = $setup['job'];
        $bidPrice = 9000;
        $bid = $jobTask->bidContractorJobTasks()->where('contractor_id', '=', $sub->id)->get()->first();

        $this->subSendsBidToGeneral(
            $sub,
            $bidPrice,
            "creditCard",
            $general->id,
            $jobTask,
            $sub->id,
            $job
        );
        $this->approvesSubsBid(
            $general, $setup['jobTask'], $sub->id, $jobTask->id, $bidPrice, $bid->id
        );

        $address = $this->getJobAddress($job->location()->get()->first());

        $this->customerApprovesBid(
            $job,
            $address,
            $job->agreed_start_date,
            $customer->location_id,
            true
        );

        $this->subFinishesJobTask($jobTask);
//        $this->customerMakesPayment($jobTask);
        $stripeCustomer = $this->createStripeCustomer($customer);
        $amount = 10000;

        $charge = $this->jemmsonIsPaidTheTask($this->clientSideToken(), $amount);
        $contractorAmount = $this->calculateContractorAmount($amount, $jobTask);
        $this->transferAmountToGeneral($contractorAmount['contractorFee'], $general->id, $charge->id);
        $this->transferAmountToSub(
            $contractorAmount['subFee'],
            $sub->id,
            $jobTask->contractor_id,
            $charge->id
        );

        $this->stripeIdIsAddedToContractorCustomerTable($general->id, $customer->id, $stripeCustomer->id);

        $this->assertEquals();
    }

    public function getJobAddress($location)
    {
        return [
            "addressLine1" => $location->address_line_1,
            "addressLine2" => $location->address_line_2,
            "city" => $location->city,
            "state" => $location->state,
            "zip" => $location->zip
        ];
    }

    public function stripeIdIsAddedToContractorCustomerTable($generalId, $customerId, $stripeCustomerId)
    {
        $cc = ContractorCustomer::where('contractor_user_id', '=', $generalId)
            ->where('customer_user_id', '=', $customerId)->get()->first();

        if (\is_null($cc->stripe_id)) {
            $cc->stripe_id = $stripeCustomerId;
            try {
                $cc->save();
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ], 200);
            }
        }
    }

    public function calculateContractorAmount($amount, $jobTask)
    {
        $stripe = $amount * env('STRIPE_PERCENT_FEE') + 30;
        $subFee = $jobTask->sub_final_price;
        $jemmsonFee = env('JEMMSON_FLAT_RATE');
        $contractorFee = $amount - $stripe - $subFee - $jemmsonFee;

        return [
            "stripe" => $stripe,
            "subFee" => $subFee,
            "jemmsonFee" => $jemmsonFee,
            "contractorFee" => $contractorFee
        ];
    }

    public function transferAmountToGeneral($amount, $contractorId, $chargeId)
    {
        $accountId = StripeExpress::where('contractor_id', '=', $contractorId)
            ->get()->first()->stripe_user_id;
        return $this->transfer($amount, $accountId, $chargeId);
    }

    public function transferAmountToSub($amount, $contractorId, $jobTaskContractorId, $chargeId)
    {
        if ($contractorId === $jobTaskContractorId) {
            $accountId = StripeExpress::where('contractor_id', '=', $contractorId)
                ->get()->first()->stripe_user_id;
            return $this->transfer($amount, $accountId, $chargeId);
        }
    }

    public function transfer($amount, $accountId, $chargeId)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        \Stripe\Stripe::$apiVersion = '2019-08-14';

        return \Stripe\Transfer::create([
            'amount' => $amount,
            'currency' => 'usd',
            'destination' => $accountId,
            "source_transaction" => $chargeId
        ]);
    }

    public function createStripeCustomer($customer)
    {
        $address = [
            "line1" => "123 abc st.",
            "line2" => "",
            "city" => "",
            "state" => "",
            "postal_code" => "",
            "country" => ""
        ];

        if ($this->stripeCustomerDoesNotExist($customer)) {
            return $this->addStripeCustomer($address);
        } else {
            return $this->retrieveCustomer($customer->stripe_id);
        }
    }

    public function stripeCustomerDoesNotExist($customer)
    {
        return $customer->stripe_id == null;
    }

    public function retrieveCustomer($customerStripeId)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        \Stripe\Stripe::$apiVersion = '2019-08-14';

        return \Stripe\Customer::retrieve($customerStripeId);
    }

    public function addStripeCustomer($address)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        \Stripe\Stripe::$apiVersion = '2019-08-14';

        return \Stripe\Customer::create([
            'description' => 'My First Test Customer (created for API docs)',
            "address" => [
                "line1" => $address['line1'],
                "line2" => $address['line2'],
                "city" => $address['city'],
                "state" => $address['state'],
                "postal_code" => $address['postal_code'],
                "country" => $address['country']
            ]
        ]);
    }

    public function clientSideToken()
    {
        $token = [
            'id' => "tok_visa",
            'object' => "token",
            'card' => [
                'id' => "card_1G5ZPIIX4qnobbHhCErGzxFw",
                'object' => "card",
                'address_city' => null,
                'address_country' => null,
                'address_line1' => null,
                'address_line1_check' => null,
                'address_line2' => null,
                'address_state' => null,
                'address_zip' => "42424",
                'address_zip_check' => "unchecked",
                'brand' => "Visa",
                'country' => "US",
                'cvc_check' => "unchecked",
                'dynamic_last4' => null,
                'exp_month' => 4,
                'exp_year' => 2024,
                'funding' => "credit",
                'last4' => "4242",
                'metadata' => [],
                'name' => null,
                'tokenization_method' => null,
                'client_ip' => "24.117.48.95",
                'created' => 1580138505,
                'livemode' => false,
                'type' => "card",
                'used' => false,
            ]
        ];

        return $token['id'];

    }

    public function jemmsonIsPaidTheTask($token, $amount, $description = '')
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        \Stripe\Stripe::$apiVersion = '2019-08-14';

        return \Stripe\Charge::create([
            'amount' => $amount,
            'currency' => 'usd',
            'source' => $token,
            'description' => $description
        ]);

    }


    /**  @test */
    function test_a_customer_can_pay_for_a_task()
    {
        //

        // Set your secret key: remember to change this to your live secret key in production
// See your keys here: https://dashboard.stripe.com/account/apikeys
        \Stripe\Stripe::setApiKey('sk_test_ebg7SjOI3rsZkeV5SZsUkOon');

// Create a PaymentIntent:
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => 10000,
            'currency' => 'usd',
            'payment_method_types' => ['card'],
            'transfer_group' => '{ORDER10}',
        ]);

// Create a Transfer to a connected account (later):
        $transfer = \Stripe\Transfer::create([
            'amount' => 7000,
            'currency' => 'usd',
            'destination' => '{{CONNECTED_STRIPE_ACCOUNT_ID}}',
            'transfer_group' => '{ORDER10}',
        ]);

// Create a second Transfer to another connected account (later):
        $transfer = \Stripe\Transfer::create([
            'amount' => 2000,
            'currency' => 'usd',
            'destination' => '{{OTHER_CONNECTED_STRIPE_ACCOUNT_ID}}',
            'transfer_group' => '{ORDER10}',
        ]);

    }

    /**  @test */
    function test_I_can_charge_a_card_using_payment_intents()
    {
        //
        $this->createAPaymentIntent();
    }

    private function createAPaymentIntent()
    {
        // Set your secret key: remember to change this to your live secret key in production
        // See your keys here: https://dashboard.stripe.com/account/apikeys
//        \Stripe\Stripe::setApiKey('sk_test_ebg7SjOI3rsZkeV5SZsUkOon');
        \Stripe\Stripe::setApiKey(env('STRIPE_KEY'));

        $intent = \Stripe\PaymentIntent::create([
            'amount' => 1099,
            'currency' => 'usd',
        ]);
    }
}
