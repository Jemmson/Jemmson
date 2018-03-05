<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Faker\Factory;
use App\User;

class HomePageCustomerTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))->visit('/home')
//                ->assertSee('Initiate Bid')
                ->assertSee('Bid List')
                ->assertSee('Previous Bids')
                ->assertSee('Settings')
                ->assertDontSee('I am a contractor')
                ->pause(10000);
        });
    }
}
