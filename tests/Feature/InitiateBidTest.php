<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Customer;
use App\User;
use App\Contractor;
use App\Http\Controllers\InitiateBidController;
use Faker\Factory;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InitiateBidTest extends TestCase
{
//    use DatabaseTransactions;

    /** @test */
    public function sending_an_email_if_the_customer_is_in_the_database()
    {

//        $ib = new InitiateBidController;

//        $both = $ib->validateInput('s@s.com', '1234567');

//        $this->assertEquals('both', $both);
//
//        $email = $ib->validateInput('s@s.com', '');
//
//        $this->assertEquals('email', $email);
//
//        $phone = $ib->validateInput('', '1234567');
//
//        $this->assertEquals('phone', $phone);

    }

    /** @test */
    public function that_a_customer_exists_and_I_have_both_email_and_phone_number_and_that_the_application_returns_true()
    {

        $user = $this->creating_a_customer();

        $ib = new InitiateBidController;

        $user = $ib->customerExistsInTheDatabase($user->email, $user->phone);

        $this->assertEquals(1, count($user));

    }

    /** @test */
    public function that_a_customer_exists_and_I_have_their_email_and_that_the_application_returns_true()
    {

        $user = $this->creating_a_customer();
        $user->phone = NULL;
        $user->save();

        $ib = new InitiateBidController;

        $user = $ib->customerExistsInTheDatabase($user->email, $user->phone);

        $this->assertEquals(1, count($user));

    }

    /** @test */
    public function that_a_customer_exists_and_I_have_their_phone_number_and_that_the_application_returns_true()
    {

        $user = $this->creating_a_customer();
        $user->email = NULL;
        $user->save();

        $ib = new InitiateBidController;

        $user = $ib->customerExistsInTheDatabase($user->email, $user->phone);

        $this->assertEquals(1, count($user));

    }

    /** @test */
    public function that_a_customer_does_not_exist_and_that_the_application_returns_false()
    {

        // I have a customer where both the email and the phone number is populated
        $faker = Factory::create();
        $user = [
            'email' => $faker->email,
            'phone' => $faker->phoneNumber
        ];

        $ib = new InitiateBidController;

        $exists = $ib->customerExistsInTheDatabase($user['email'], $user['phone']);

        $this->assertEquals(false, $exists);

    }

    /** @test */
    public function the_customer_does_not_exist_in_the_database_so_I_need_to_create_a_new_customer()
    {

        // I have a customer where both the email and the phone number is populated
        $faker = Factory::create();
        $user = [
            'email' => $faker->email,
            'phone' => $faker->phoneNumber
        ];

        $ib = new InitiateBidController;
        $ib->createNewUser($user['email'], $user['phone']);

        $this->assertDatabaseHas('users', [
            'email' => $user['email'],
            'phone' => $user['phone']
        ]);

    }

    /** @test */
    public function the_customer_does_not_exist_in_the_database_and_I_only_have_email_so_I_need_to_create_a_new_customer()
    {

        // I have a customer where both the email and the phone number is populated
        $faker = Factory::create();
        $user = [
            'email' => $faker->email,
            'phone' => -1
        ];

        $ib = new InitiateBidController;
        $ib->createNewUser($user['email'], $user['phone']);

        $this->assertDatabaseHas('users', [
            'email' => $user['email']
        ]);

    }

    /** @test */
    public function the_customer_does_not_exist_in_the_database_and_I_only_have_the_phone_number_so_I_need_to_create_a_new_customer()
    {

        // I have a customer where both the email and the phone number is populated
        $faker = Factory::create();
        $user = [
            'email' => -1,
            'phone' => $faker->phoneNumber
        ];

        $ib = new InitiateBidController;
        $ib->createNewUser($user['email'], $user['phone']);

        $this->assertDatabaseHas('users', [
            'phone' => $user['phone']
        ]);

    }

    /** @test */
    public function create_a_bid_from_a_user()
    {

        $faker = Factory::create();
        $job = [
            'name' => $faker->name
        ];

        $ib = new InitiateBidController;
        $customer = $this->creating_a_customer();

        $contractor = factory(Contractor::class)->create();

        $user = User::find($contractor->user_id);
        $user->usertype = "contractor";
        $user->save();
        $this->actingAs($user);

        $job_id = $ib->createBid($customer->id, $job['name']);

        $this->assertDatabaseHas('jobs', [
            'job_name' => $job['name'],
            'id' => $job_id
        ]);

    }

    /** @test */
    public function create_a_token_to_be_used_for_the_passwordless_link()
    {

        $user = $this->creating_a_customer();

        $token = $user->generateToken(true);
        $t = json_decode($token);

        $this->assertDatabaseHas('user_tokens', [
            'user_id' => $user->id,
            'token' => $t->token
        ]);
    }

    /** @test */
    public function send_an_email_if_an_email_was_given_by_the_contractor()
    {

        $faker = Factory::create();
        $email = 'pike.shawn@gmail.com';
        $token = $faker->word;
        $jobName = $faker->word;
        $job_id = $faker->numberBetween(1,99999);

        $contractor = factory(Contractor::class)->create();
        $user = User::find($contractor->user_id);
        $user->usertype = "contractor";
        $user->save();
        $this->actingAs($user);

        $ib = new InitiateBidController;

        $data = [
            'email' => $email,
            'link' => $token,
            'job_name' => $jobName,
            'job_id' => $job_id,
            'contractor' => $user->name
        ];

        $ib->sendEmail($data, $email);

    }

    /** @test */
    public function send_a_text_if_a_phone_number_was_given_by_the_contractor()
    {

        $faker = Factory::create();
        $email = 'pike.shawn@gmail.com';
        $token = $faker->word;
        $jobName = $faker->word;
        $job_id = $faker->numberBetween(1,99999);

        $contractor = factory(Contractor::class)->create();
        $user = User::find($contractor->user_id);
        $user->usertype = "contractor";
        $user->phone = '480.703.4902';
        $user->save();
        $this->actingAs($user);

        $ib = new InitiateBidController;

        $data = [
            'email' => $email,
            'link' => $token,
            'job_name' => $jobName,
            'job_id' => $job_id,
            'contractor' => $user->name
        ];

        $ib->sendText($data, $email);

    }

    protected function creating_a_customer()
    {
        // I have a customer where both the email and the phone number is populated
        $customer = factory(Customer::class)->create();

        $user = User::find($customer->user_id);
        $user->usertype = "customer";
        $user->save();

        return $user;
    }
}
