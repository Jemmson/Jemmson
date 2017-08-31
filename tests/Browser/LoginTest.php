<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testThatLogInElementsAreThere()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('username', 'pike.shawn@gmail.com')
                ->type('password', 'asdqwe')
                ->press('login')
                ->assertPathIs('/home')
                ->pause(0);
        });
    }
}
