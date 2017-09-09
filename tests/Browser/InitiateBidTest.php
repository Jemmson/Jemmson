<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Faker\Factory;
use App\User;

class InitiateBidTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testExample()
    {

        $faker = Factory::create();
        $job = [
            'name' => $faker->name,
            'email' => $faker->email,
            'phone' => $faker->e164PhoneNumber
        ];

        $this->browse(function (Browser $browser) use ($job) {
            $browser
                ->loginAs(User::find(1))
                ->visit('contractor/initiate-bid')
                ->type('jobName', $job['name'])
                ->type('email', $job['email'])
                ->type('phone', $job['phone'])
                ->press('submit')
                ->pause(3000)
                ->assertPathIs('/contractor/bid-list');
        });
    }
}
