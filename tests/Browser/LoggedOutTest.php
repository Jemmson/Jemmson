<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoggedOutTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     * @group loggedout
     */
    public function testExample()
    {
        // get verbs requires auth
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertPathIs('/')
                    ->visit('/home')
//                    ->pause(100000)
                    ->assertPathIs('/home')
                    ->visit('/furtherInfo')
//                    ->pause(100000)
                    ->assertPathIs('/login')
                    ->visit('/login')
//                    ->pause(100000)
                    ->assertPathIs('/login')
//                ->visit('/contractorFeatures')
//                    ->pause(100000)
//                ->assertPathIs('/login')
//                ->visit('/customerFeatures')
//                    ->pause(100000)
//                ->assertPathIs('/login')
                ->visit('/invoices')
//                    ->pause(100000)
                ->assertPathIs('/login')
                ->visit('/invoice/1')
//                    ->pause(100000)
                ->assertPathIs('/login')
                ->visit('/stripe/express/connect')
//                    ->pause(100000)
                ->assertPathIs('/login')
                ->visit('/stripe/express/auth')
//                    ->pause(100000)
                ->assertPathIs('/login')
//                    ->pause(100000)
                ;
        });
    }
}
