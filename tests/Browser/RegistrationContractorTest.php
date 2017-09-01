<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Faker\Factory;
use App\User;

class RegistrationContractorTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testContratorRegistrationElements()
    {
        $faker = Factory::create();
        $user_info = [
            'company_name' => $faker->company,
            'phone_number' => $faker->phoneNumber,
            'address_line_1' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => "AZ",
            'zip' => "85283",
        ];

        $this->browse(function (Browser $browser) use ($user_info) {
            $browser->loginAs(User::find(1))->visit('/furtherInfo')
                ->type('phone_number', $user_info['phone_number'])
                ->type('company_name', $user_info['company_name'])
                ->type('address_line_1', $user_info['address_line_1'])
                ->type('city', $user_info['city'])
                ->type('state', $user_info['state'])
                ->type('zip', $user_info['zip'])
                ->check('email_contact')
                ->check('phone_contact')
                ->check('sms_text')
                ->press('submit')
//                ->pause(10000)
//                ->assertPathIs('/home')
//                ->assertSee('I am a contractor')
//                ->assertDontSee('I am a customer')
//                ->assertSee('Initiate Bid')
//                ->assertSee('Bid List')
//                ->pause(10000)
                ->pause(0);
//                ->assertSee('I am in further Info');
//                ->pause(5000)
        });
    }
}