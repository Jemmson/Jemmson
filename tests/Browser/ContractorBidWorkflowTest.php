<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Contractor;
use App\User;
use ContractorFactory;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContractorBidWorkflowTest extends DuskTestCase
{
//    use DatabaseMigrations;
//    use DatabaseTransactions;

    /**
     * A Dusk test example.
     *
     * @return void
     * @test
     * @group cont
     */
    public function ContractorInitatiatesABid()
    {
//        given
//        contractor exists
        $cont = factory(\App\Contractor::class)->create();
        $this->assertDatabaseHas('contractors', [
            'user_id' => $cont->user_id
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $cont->user_id
        ]);

        $user = \App\User::find($cont->user_id);
        $user->usertype = 'contractor';
        $user->save();

        $faker = Factory::create();

        $this->browse(function ($browser) use ($user) {
            $browser->visit('/login')
                ->type('username', $user->email)
                ->type('password', 'secret')
                ->press('login')
//                ->pause(50000)
                ->assertPathIs('/home')
                ->pause(0);
        });

//        dd($user->usertype);

        $this->assertTrue($user->usertype === 'contractor', $user->usertype);

//        $this->browse(function ($browser) use ($user) {
//            $browser->visit('/login')
//                ->type('username', $user->email)
//                ->type('password', 'secret')
//                ->press('login')
//                ->assertPathIs('/furtherInfo')
//                ->pause(100000);
//        });

        $this->browse(function (Browser $browser) use ($user, $faker) {
//            dd($user);
            $browser->loginAs(User::find($user->id))
                ->visit('/initiate-bid')
                ->type('phone', '4807034902')
                ->type('customerName', $faker->name)
                ->type('jobName', $faker->company)
                ->type('email', $faker->email)
                ->type('phone', '')
//                ->pause(5000)
                ->press('submit')
                ->assertPathIs('/bid-list')
                ->press('review')
                ->assertSee('Bid Initiated')
                ->press('addTaskToBid')
                ->type('taskName', 'New Pump')
                ->type('area', 'East Mesa')
//                ->type('start_date', '2018-01-25')
                ->keys('#start_date', '02012018')
                ->type('taskPrice', '248.24')
                ->type('subTaskPrice', '224.56')
                ->press('addTaskToInvoice')
//                ->pause(1000)
                ->press('addTaskToBid')
                ->type('taskName', 'New Filter')
                ->type('area', 'East Mesa')
//                ->type('start_date', '2018-01-25')
                ->keys('#start_date', '02012018')
                ->type('taskPrice', '456.78')
                ->type('subTaskPrice', '425.87')
                ->press('addTaskToInvoice')
                ->visit('/bid-list')
                ->press('review')
                ->assertSee('Bid In Progress')
                ->pause(0);
        });

//        echo $cont;
//        var_dump($cont);
//        customer may or may not exist in the application
//        there are no subs for this job
//        there is no bid or invoice
//
    }
}
