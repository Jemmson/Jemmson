<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Faker\Factory;
use App\User;

class HomePageContractorTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))->visit('/home')
                    ->assertSee('Initiate Bid')
                    ->assertSee('Bid List')
                    ->assertSee('Type of Work')
                    ->assertSee('Ratings')
                    ->assertSee('Settings')
                    ->assertDontSee('I am a customer')
                    ->pause(10000);
        });
    }
}
