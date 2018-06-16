<?php

namespace Tests\Browser;

use FontLib\Table\Type\name;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
//    public function testThatAContractorCanRegister()
//    {
//        $this->setupInitialRegistrationInfo('contractor');
//        $this->setUpCompanyInformation();
//    }

    /**
     * A Dusk test example.
     *
     * @return void
     * @group login
     */
    public function testThatACustomerCanRegister()
    {
        $this->setupInitialRegistrationInfo('customer');
        $this->setUpCustomerInformation();
    }

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
                    ->press('register')
                    ->pause(3000);
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
                ->press('submit')
                ->pause(10000);
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
                ->pause(10000);
        });
    }

    private function initiateAbid()
    {
        $user = User::find(1);
        $user->phone = '6023508801';
        $user->save();

        $customer = [
            'name' => $faker->name,
            'jobName' => 'Fix ' . $faker->name,
            'email' => $faker->email,
            'phone' => '4807034902'
        ];

        $this->browse(function (Browser $browser) use ($customer) {
            $browser->visit('/#/initiate-bid')
//                ->pause(10000)
                ->type('customerName', $customer['name'])
//                ->type('jobName', $customer['jobName'])
//                ->type('email', $customer['email'])
                ->type('phone', $customer['phone'])
                ->pause(3000)
                ->press('submit')
                ->pause(5000)
//                ->assertPathIs('/')  // not right - this is a bug
                ->pause(5000)
                ->press('reviewBid')
                ->pause(5000);
        });
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