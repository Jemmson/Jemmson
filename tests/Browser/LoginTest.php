<?php

namespace Tests\Browser;
use App\Contractor;
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
        $user = User::find($contractor->user_id);
        $user->usertype = "contractor";
        $user->save();

        $this->browse(function (Browser $browser) use ($faker) {
            $browser->visit('/login')
                ->type('username', $faker->email)
                ->type('password', 'secret')
                ->press('login')
                ->pause(5000)
                ->assertPathIs('/home')
                ->pause(0);
        });
    }
}
