<?php

namespace Tests\Browser;

use FontLib\Table\Type\name;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\FactoryBuilder;
use Faker\Factory;
use App\User;
use App\Contractor;

class RegistrationContractorTest extends DuskTestCase
{

    use RefreshDatabase;

    /**
     * A Dusk test example.
     *
     * @return void
     * @group login
     */
    public function testThatAContractorCanRegister()
    {
        $this->setupInitialRegistrationInfo('contractor');
        $this->setUpCompanyInformation();
//        $this->initiateAbid();
    }

    /**
     * A Dusk test example.
     *
     * @return void
     * @group login
     */
    public function test_that_submit_button_is_disabled_when_page_is_loaded()
    {
        $contractor = factory(\App\Contractor::class)->create();
        $location = factory(\App\Location::class)
            ->create(['user_id' => $contractor->user_id]);
        $contractor->location_id = $location->id;
        $contractor->save();
        $user = User::find($contractor->user_id);
        $user->location_id = $location->id;
        $user->save();

        // check that the submit button is disabled with phone field filled out but customer name is empty
        $this->browse(function (Browser $browser) use ($contractor) {
            $browser->loginAs(User::find($contractor->user_id))
                ->visit('/#/initiate-bid')
                ->pause(5000)
                ->assertDisabled('@submitBid')
                ->pause(0);
        });
    }

    /**
     * A Dusk test example.
     *
     * @return void
     * @group login
     */
    public function test_that_submit_button_is_disabled_when_phone_is_filled_out_and_other_fields_are_empty()
    {
        $contractor = factory(\App\Contractor::class)->create();
        $location = factory(\App\Location::class)
            ->create(['user_id' => $contractor->user_id]);
        $contractor->location_id = $location->id;
        $contractor->save();
        $user = User::find($contractor->user_id);
        $user->location_id = $location->id;
        $user->save();

//      check that the submit button is disabled with phone field filled out but customer name empty
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs(User::find($user->id))
                ->visit('/#/initiate-bid')
                ->type('@phone', $user->phone)
                ->type('@jobName', '')
                ->type('@customerName', '')
                ->assertDisabled('submit')
//                ->pause(2000)
                ->pause(0);
        });
    }

    /**
     * A Dusk test example.
     *
     * @return void
     * @group login
     */
    public function test_that_submit_button_is_disabled_when_customer_is_filled_out_and_other_fields_are_empty()
    {
        $contractor = factory(\App\Contractor::class)->create();
        $location = factory(\App\Location::class)
            ->create(['user_id' => $contractor->user_id]);
        $contractor->location_id = $location->id;
        $contractor->save();
        $user = User::find($contractor->user_id);
        $user->location_id = $location->id;
        $user->save();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs(User::find($user->id))
                ->visit('/#/initiate-bid')
                ->type('@phone', '')
                ->type('@jobName', '')
                ->type('@customerName', $user->name)
                ->assertDisabled('submit')
//                ->pause(2000)
                ->pause(0);
        });
    }

    /**
     * A Dusk test example.
     *
     * @return void
     * @group login
     */
    public function test_that_submit_button_is_disabled_when_job_name_is_filled_out_and_other_fields_are_empty()
    {
        $faker = Factory::create();

        $contractor = factory(\App\Contractor::class)->create();
        $location = factory(\App\Location::class)
            ->create(['user_id' => $contractor->user_id]);
        $contractor->location_id = $location->id;
        $contractor->save();
        $user = User::find($contractor->user_id);
        $user->location_id = $location->id;
        $user->save();

        $this->browse(function (Browser $browser) use ($user, $faker) {
            $browser->loginAs(User::find($user->user_id))
                ->visit('/#/initiate-bid')
                ->type('@phone', '')
                ->type('@jobName', $faker->name)
                ->type('@customerName', '')
                ->assertDisabled('submit')
//                ->pause(2000)
                ->pause(0);
        });
    }

    /**
     * A Dusk test example.
     *
     * @return void
     * @group login
     */
    public function test_that_submit_button_is_disabled_when_job_name_and_customer_name_is_filled_out()
    {
        $faker = Factory::create();

        $contractor = factory(\App\Contractor::class)->create();
        $location = factory(\App\Location::class)
            ->create(['user_id' => $contractor->user_id]);
        $contractor->location_id = $location->id;
        $contractor->save();
        $user = User::find($contractor->user_id);
        $user->location_id = $location->id;
        $user->save();

        $this->browse(function (Browser $browser) use ($user, $faker) {
            $browser->loginAs(User::find($user->user_id))
                ->visit('/#/initiate-bid')
                ->type('@phone', '')
                ->type('@jobName', $faker->name)
                ->type('@customerName', $user->name)
                ->assertDisabled('submit')
//                ->pause(2000)
                ->pause(0);
        });
    }

    /**
     * A Dusk test example.
     *
     * @return void
     * @group login
     */
    public function test_that_submit_button_is_disabled_when_job_name_and_phone_is_filled_out()
    {
        $contractor = factory(\App\Contractor::class)->create();
        $location = factory(\App\Location::class)
            ->create(['user_id' => $contractor->user_id]);
        $contractor->location_id = $location->id;
        $contractor->save();
        $user = User::find($contractor->user_id);
        $user->location_id = $location->id;
        $user->save();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs(User::find($user->id))
                ->visit('/#/initiate-bid')
                ->type('@phone', $user->phone)
                ->type('@jobName', '')
                ->type('@customerName', $user->name)
                ->assertDisabled('submit')
//                ->pause(2000)
                ->pause(0);
        });
    }

    /**
     * A Dusk test example.
     *
     * @return void
     * @group loginEn
     */
    public function test_that_submit_button_is_enabled_when_customer_name_and_phone_are_filled_out()
    {
        $contractor = factory(\App\Contractor::class)->create();
        $location = factory(\App\Location::class)
            ->create(['user_id' => $contractor->user_id]);
        $contractor->location_id = $location->id;
        $contractor->save();
        $user = User::find($contractor->user_id);
        $user->location_id = $location->id;
        $user->save();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs(User::find($user->user_id))
                ->visit('/#/initiate-bid')
                ->pause(10000)
                ->type('#phone', $user->phone)
                ->type('#jobName', '')
                ->type('#customerName', $user->name)
                ->pause(50000)
                ->assertEnabled('submit')
                ->pause(0);
        });
    }

//    /**
//     * A Dusk test example.
//     *
//     * @return void
//     * @group login
//     */
//    public function test_that_submit_button_is_enabled_when_customer_name_job_name_are_filled_out()
//    {
//        $contractor = factory(\App\Contractor::class)->create();
//        $location = factory(\App\Location::class)
//            ->create(['user_id'=>$contractor->user_id]);
//        $contractor->location_id = $location->id;
//        $contractor->save();
//        $user = User::find($contractor->user_id);
//        $user->location_id = $location->id;
//        $user->save();
//
//        $this->browse(function (Browser $browser) use ($contractor, $user) {
//            $browser->visit('/#/initiate-bid')
//                ->type('@phone', $user->phone)
//                ->type('@jobName', '')
//                ->type('@customerName', $user->name)
//                ->assertDisabled('submit')
////                ->pause(2000)
//                ->pause(0);
//        });
//    }


//    /**
//     * A Dusk test example.
//     *
//     * @return void
//     * @group login
//     */
//    public function testThatACustomerCanRegister()
//    {
////        $this->setupInitialRegistrationInfo('customer');
////        $this->setUpCustomerInformation();
//    }

    private function setupInitialRegistrationInfo($usertype)
    {
        $faker = Factory::create();

        if ($usertype == 'contractor') {
            $con = [
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => 'asdasd'
            ];
            $this->browse(function (Browser $browser) use ($con) {
                $browser->visit('/');
                $browser->visit('/register#/')
                    ->type('name', $con['name'])
                    ->type('email', $con['email'])
                    ->radio('usertypeContractor', 'contractor')
                    ->type('password', $con['password'])
                    ->type('password_confirmation', $con['password'])
                    ->check('terms')
                    ->press('register')
                    ->pause(3000);
            });
        } else {
            $con = [
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => 'asdasd'
            ];
            $this->browse(function (Browser $browser) use ($con) {
                $browser->visit('/');
                $browser->visit('/register#/')
//                    ->pause(20000)
                    ->type('email', $con['email'])
                    ->type('name', $con['name'])
                    ->radio('usertype', 'customer')
                    ->type('password', $con['password'])
                    ->type('password_confirmation', $con['password'])
                    ->check('terms')
                    ->pause(30000)
                    ->press('register');
            });
        }
    }

    private function setUpCompanyInformation()
    {
        $faker = Factory::create();

        $company_info = [
            'company_name' => $faker->company,
            'phone_number' => '4807034902',
            'address_line_1' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => "AZ",
            'zip' => "85283",
        ];
//
        $this->browse(function (Browser $browser) use ($company_info) {
            $browser
                ->type('phone_number', $company_info['phone_number'])
                ->type('company_name', $company_info['company_name'])
                ->type('address_line_1', $company_info['address_line_1'])
                ->type('city', $company_info['city'])
                ->type('state', $company_info['state'])
                ->type('zip', $company_info['zip'])
                ->pause(5000)
                ->assertSee('mobile')
                ->press('submit')
                ->pause(5000)
                ->pause(0);
        });
    }

    private function setUpCustomerInformation()
    {
        $faker = Factory::create();

        $customer_info = [
            'company_name' => $faker->company,
            'phone_number' => '4807034902',
            'address_line_1' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => "AZ",
            'zip' => "85283",
        ];
//
        $this->browse(function (Browser $browser) use ($company_info) {
            $browser
                ->pause(20000)
                ->type('phone_number', $company_info['phone_number'])
                ->type('company_name', $company_info['company_name'])
                ->type('address_line_1', $company_info['address_line_1'])
                ->type('city', $company_info['city'])
                ->type('state', $company_info['state'])
                ->type('zip', $company_info['zip'])
                ->press('submit')
                ->pause(5000);
        });
    }


    private function initiateAbid()
    {
        $faker = Factory::create();

        $user = User::find(1);
        $user->phone = '6023508801';
        $user->save();

        $customer = [
            'name' => $faker->name,
            'jobName' => 'Fix ' . $faker->name,
            'email' => $faker->email,
            'mobilePhone' => '4807034902',
            'landlinePhone' => '4809249886'
        ];

//        // check that the submit button is disabled with phone field filled out but customer name empty
//        $this->browse(function (Browser $browser) use ($customer) {
//            $browser->visit('/#/initiate-bid')
//                ->type('@phone', $customer['mobilePhone'])
//                ->type('@jobName', '')
//                ->type('@customerName', '')
//                ->assertDisabled('submit')
////                ->pause(2000)
//                ->pause(0);
//        });

////         check that the submit button is disabled with phone field filled out but customer name empty
//        $this->browse(function (Browser $browser) use ($customer) {
//            $browser->visit('/#/initiate-bid')
//                ->type('@phone', $customer['mobilePhone'])
//                ->type('@jobName', 'test')
//                ->type('@customerName', 'test')
//                ->assertDisabled('submit')
////                ->pause(2000)
//                ->pause(0);
//        });

//        // check that the submit is disabled with only the customer name being filled out
//        $this->browse(function (Browser $browser) use ($customer) {
//            $browser->visit('/#/initiate-bid')
//                ->type('@customerName', $customer['name'])
//                ->type('@phone', '')
//                ->type('@jobName', '')
//                ->assertDisabled('submit');
//        });

//        // check that button is disabled when the jobName is filled out but no other fields are filled out
//        $this->browse(function (Browser $browser) use ($customer) {
//            $browser->visit('/#/initiate-bid')
//                ->type('@customerName', '')
//                ->type('@phone', '')
//                ->type('@jobName', $customer['jobName'])
//                ->assertDisabled('submit');
//        });

        // check that the button is disabled with jobName and CustomerName filled out
        $this->browse(function (Browser $browser) use ($customer) {
            $browser->visit('/#/initiate-bid')
                ->type('@customerName', $customer['name'])
                ->type('@phone', '')
                ->type('@jobName', $customer['jobName'])
                ->assertDisabled('submit');
        });

        // check that the button is disabled with jobName and Mobile Number filled out
        $this->browse(function (Browser $browser) use ($customer) {
            $browser->visit('/#/initiate-bid')
                ->type('@customerName', '')
                ->type('@phone', $customer['mobilePhone'])
                ->type('@jobName', $customer['jobName'])
                ->assertDisabled('submit');
        });


//        TODO:: Need to figure out how to test when an asynchronous response is occurring - mobile and landline not working very well
//        // check that the button is disabled when all fields are entered but a landline number was put in
//        $this->browse(function (Browser $browser) use ($customer) {
//            $browser->visit('/#/initiate-bid')
//                ->type('@phone', $customer['landlinePhone'])
//        ->waitForText('mobile')
//        ->assertVue('networkType.originalCarrier', 'mobile', '@initiate-bid')
//                ->type('@customerName', '')
//                ->type('@jobName', $customer['jobName'])
//                ->assertSee('landline')
//                ->assertDisabled('submit')
//                ->pause(0)
//                ->pause(0);
//        });
//
//        // check that the phone number shows mobile when a mobile number is put into the phone number field
//        $this->browse(function (Browser $browser) use ($customer) {
//            $browser->visit('/#/initiate-bid')
//                ->type('@phone', $customer['mobilePhone'])
//                ->type('@jobName', 'test')
////                ->pause(2000)
//                ->assertSee('mobile');
//
//        });
//
//        // check that the phone number shows landline when a landline number is entered
//        $this->browse(function (Browser $browser) use ($customer) {
//            $browser->visit('/#/initiate-bid')
//                ->type('@phone', $customer['landlinePhone'])
//                ->type('@jobName', 'test')
////                ->pause(2000)
//                ->assertSee('landline');
//
//        });

        // check that the submit button is enabled after adding the phone number to the mobile phone field
        $this->browse(function (Browser $browser) use ($customer) {
            $browser->visit('/#/initiate-bid')
                ->type('customer', $customer['name'])
                ->type('phone', $customer['mobilePhone'])
                ->type('jobName', '')
                ->assertEnabled('submit');

        });
//
//
//        $this->browse(function (Browser $browser) use ($customer) {
//            $browser->visit('/#/initiate-bid')
//                // check if the submit button is disabled upon initially going to the page
////                ->pause(1000)
////                ->assertDisabled('submit')
//
//
////
//
//
//                //                ->type('jobName', $customer['jobName'])
////                ->type('email', $customer['email'])
////                ->type('phone', $customer['mobilePhone'])
////                ->type('jobName', 'test')
////                ->pause(3000)
////                ->assertSee('mobile')
////                ->type('jobName', 'test')
////                ->type('phone', $customer['landlinePhone'])
////                ->pause(1000)
////                ->assertSee('landline')
////                ->type('customer', '')
////                ->assertDisabled('submit')
////                ->type('customer', $customer['name'])
////                ->assertEnabled('submit')
//////                ->type('jobName', $customer['jobName'])
////                ->click('@submit')
//////                ->pause(5000)
//////                ->assertPathIs('/')  // not right - this is a bug
//////                ->pause(5000)
//////                ->press('reviewBid')
////                ->pause(5000)
//                ->pause(0);
//        });
    }

    private function testMobileValidation()
    {

    }

    private function test_initiate_bid_happy_path($user, $customer, $phone)
    {
        // need to test with different phone number
    }

    /**
     * A Dusk test example.
     *
     * @return void
     * @group login
     */
//    public function testContractorRegistrationElements()
//    {
//        $faker = Factory::create();
//
//        echo "hello test contractor" . "\n";
//
//        $con = [
//            'name' => $faker->name,
//            'email' => $faker->email,
//            'password' => 'asdasd'
//        ];
//
//        var_dump($con);
//
//        echo $con['name'] . "\n";
//        echo $con['email'] . "\n";
//        echo $con['password'] . "\n";
//
//        $this->browse(function (Browser $browser) use ($con) {
//            $browser->visit('/');
//            $browser->visit('/register#/')
//                ->type('name', $con['name'])
//                ->type('email', $con['email'])
//                ->radio('usertypeContractor', 'contractor')
//                ->type('password', $con['password'])
//                ->type('password_confirmation', $con['password'])
//                ->check('terms')
//                ->press('register')
//                ->pause(3000);
//        });

//    }


//    public function testPikeShawnContractor()
//    {
//        $faker = Factory::create();
//        $user_info = [
//            'company_name' => $faker->company,
//            'phone_number' => '480.703.4902',
//            'address_line_1' => $faker->streetAddress,
//            'city' => $faker->city,
//            'state' => "AZ",
//            'zip' => "85283",
//        ];
//
//        $contractor = factory(Contractor::class)->create();
//
//        $user = User::find($contractor->user_id);
//        $user->usertype = "contractor";
//        $user->save();
//
//        dd();
//
//        $this->browse(function (Browser $browser) use ($user_info) {
//            $browser->loginAs(User::find(1))->visit('/furtherInfo')
////                ->pause(50000)
//                ->type('phone_number', $user_info['phone_number'])
//                ->type('company_name', $user_info['company_name'])
//                ->type('address_line_1', $user_info['address_line_1'])
//                ->type('city', $user_info['city'])
//                ->type('state', $user_info['state'])
//                ->type('zip', $user_info['zip'])
//                ->check('email_contact')
//                ->check('phone_contact')
//                ->check('sms_text')
//                ->press('submit')
////                ->pause(10000)
//                ->assertPathIs('/home')
////                ->assertSee('I am a contractor')
////                ->assertDontSee('I am a customer')
////                ->assertSee('Initiate Bid')
////                ->assertSee('Bid List')
////                ->pause(10000)
////                ->pause(50000);
////                ->assertSee('I am in further Info');
//                ->pause(0);
//        });
//    }
}