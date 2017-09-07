<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Faker\Factory;

class RegistrationTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testRegistration()
    {
        $faker = Factory::create();
        $user = [
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            'password' => $password = bcrypt('secret')
        ];

        echo $user['name'];

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/register')
                ->type('name', $user['name'])
                ->type('email', $user['email'])
                ->radio('usertype', 'contractor')
                ->type('password', 'asdqwe')
                ->type('password_confirmation', 'asdqwe')
                ->check('terms')
//                ->pause(1000)
                ->press('register')
//                ->assertSee('I am in further Info');
                ->pause(1000)
                ->assertPathIs('/furtherInfo')
                ->pause(0);
        });
    }
}