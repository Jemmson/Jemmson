<?php

namespace Tests\Browser;

use FontLib\Table\Type\name;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory;
use App\User;
use App\Contractor;

class RegistrationContractorTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     * @group login1234
     */
    public function testContractorRegistrationElements()
    {
        $faker = Factory::create();

        echo "hello test contractor" . "\n";

        $con = [
            'name' => $faker->name,
            'email' => $faker->email,
            'password' => 'asdasd'
        ];

        var_dump($con);

        echo $con['name'] . "\n";
        echo $con['email'] . "\n";
        echo $con['password'] . "\n";

        $this->browse(function (Browser $browser) use ($con) {
            $browser->visit('/register')
                ->type('name', $con['name'])
                ->type('email', $con['email'])
                ->radio('usertypeContractor', 'contractor')
                ->type('password', $con['password'])
                ->type('password_confirmation', $con['password'])
                ->check('terms')
                ->press('register')
                ->pause(2000)
                ->assertPathIs('/furtherInfo');
//                ->pause(50000);
        });

        $user_info = [
            'company_name' => $faker->company,
            'phone_number' => $faker->phoneNumber,
            'address_line_1' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => "AZ",
            'zip' => "85283",
        ];
//
        $this->browse(function (Browser $browser) use ($user_info) {
            $browser->visit('/furtherInfo')
                ->type('phone_number', $user_info['phone_number'])
                ->type('company_name', $user_info['company_name'])
                ->type('address_line_1', $user_info['address_line_1'])
                ->type('city', $user_info['city'])
                ->type('state', $user_info['state'])
                ->type('zip', $user_info['zip'])
                ->check('email_contact')
                ->check('phone_contact')
                ->pause(10000)
                ->check('sms_text')
//                ->pause(50000)
                ->press('submit')
                ->pause(10000)
                ->assertPathIs('/home')
//                ->assertSee('I am a contractor')
//                ->assertDontSee('I am a customer')
//                ->assertSee('Initiate Bid')
//                ->assertSee('Bid List')
//                ->pause(10000)
//                ->pause(50000);
//                ->assertSee('I am in further Info');
                ->pause(0);
        });
    }

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