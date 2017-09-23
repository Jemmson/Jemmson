<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Contractor;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends DuskTestCase
{

    use RefreshDatabase;

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testThatLogInElementsAreThere()
    {
        $contractor = factory(Contractor::class)->create();

        $user = User::find($contractor->user_id);
        $user->usertype = "contractor";
        $user->save();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                ->type('username', $user->email)
                ->type('password', 'secret')
//                ->pause(5000)
                ->press('login')
                ->pause(0)
                ->pause(0)
                ->assertPathIs('/home');
        });
    }
}
