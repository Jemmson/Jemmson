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
        $user_info = [];

        $this->browse(function (Browser $browser) use ($user_info) {
            $browser->loginAs(User::find(61))
                ->visit('/furtherInfo')
                ->pause(50000)
                ->assertSee('I am a customer')
                ->pause(50000);
//                ->press('submit')
//                ->assertSee('I am in further Info');
//                ->pause(5000)
//                ->assertPathIs('/home');
        });
    }

}