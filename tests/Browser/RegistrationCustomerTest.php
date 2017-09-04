<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Faker\Factory;
use App\User;

class RegistrationCustomerTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */

    public function testCustomerRegistrationElements()
    {
        $faker = Factory::create();
        $user_info = [
            'phone_number' => $faker->phoneNumber,
            'address_line_1' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => "AZ",
            'zip' => "85283",
            'notes' => $faker->paragraph,
        ];

        $this->browse(function (Browser $browser) use ($user_info) {
            $browser->loginAs(User::find(61))
                ->visit('/furtherInfo')
                ->type('phone_number', $user_info['phone_number'])
                ->type('address_line_1', $user_info['address_line_1'])
                ->type('city', $user_info['city'])
                ->type('state', $user_info['state'])
                ->type('zip', $user_info['zip'])
                ->type('notes', $user_info['notes'])
                ->check('email_method_of_contact')
                ->check('phone_method_of_contact')
                ->check('sms_method_of_contact')
                ->pause(50000)
                ->press('submit')
                ->assertPathIs('/home');
//                ->press('submit')
//                ->assertSee('I am in further Info');
//                ->pause(5000)
//                ->assertPathIs('/home');
        });
    }

}