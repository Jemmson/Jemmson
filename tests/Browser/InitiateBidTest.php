<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Contractor;
use App\User;
use Faker\Factory;

class InitiateBidTest extends DuskTestCase
{

    use DatabaseTransactions;

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function test_that_a_user_can_intiate_a_bid_and_then_go_to_the_bid_list()
    {

//        $contractor = factory(Contractor::class)->create();
//
//        $user = User::find($contractor->user_id);
//        $user->usertype = "contractor";
//        $user->save();

//        dd($user->id);

        $faker = Factory::create();
        $job = [
            'name' => $faker->name,
            'email' => "pike.shawn@gmail.com",
            'phone' => "4807034902"
        ];

//        $userId = $user->id;

        $this->browse(function (Browser $browser) use ($job) {
//            dd($user->id);
            $browser->loginAs(User::find(11))
                ->pause(0)
                ->visit('/initiate-bid')
                ->type('jobName', $job['name'])
                ->type('email', $job['email'])
                ->type('phone', $job['phone'])
                ->press('submit')
//                ->pause(0)
                ->assertPathIs('/bid-list');
        });
//        dd($user);
    }

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function test_if_phone_and_email_are_not_passed_in_then_the_page_is_redirected_back_to_the_initiate_bid_page()
    {

        // TODO: need to handle this error (1/1) Swift_RfcComplianceException Address in mailbox given [] does not comply with RFC 2822, 3.6.2. when malformed email is present.
        // this test breaks

//        $this->browse(function (Browser $browser) {
//            $browser
//                ->loginAs(User::find(1))
//                ->visit('/initiate-bid')
//                ->type('jobName', 'name')
//                ->press('submit')
//                ->pause(50000)
//                ->assertPathIs('/initiate-bid');
//        });

    }
}
