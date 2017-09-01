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
                    ->pause(50000)
                    ->assertSee('I am a contractor')
                    ->assertDontSee('I am a customer')
                    ->assertSee('Initiate Bid')
                    ->assertSee('Bid List');
        });
    }
}
