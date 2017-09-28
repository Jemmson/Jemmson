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

//    use DatabaseTransactions;

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function test_that_a_user_can_intiate_a_bid_and_then_go_to_the_bid_list()
    {

//        dd($job['contractorId']);
//        $userId = $user->id;

        $job = $this->createContractor();

        $this->browse(function (Browser $browser) use ($job) {
//            dd($user->id);
            $browser->loginAs(User::find($job['contractorId']))
                ->pause(0)
                ->visit('/initiate-bid')
                ->type('customerName', $job['customerName'])
                ->type('jobName', $job['name'])
                ->type('email', $job['email'])
                ->type('phone', $job['phone'])
                ->press('submit')
                ->pause(0)
                ->assertPathIs('/bid-list')
                ->pause(0);
        });
//        dd($user);
    }

    public function test_if_there_is_no_customer_name_then_I_get_redirected_back_to_the_page()
    {

//        dd($job['contractorId']);
//        $userId = $user->id;

        $job = $this->createContractor();

        $this->browse(function (Browser $browser) use ($job) {
//            dd($user->id);
            $browser->loginAs(User::find($job['contractorId']))
                ->pause(0)
                ->visit('/initiate-bid')
                ->type('customerName', '')
                ->type('jobName', $job['name'])
                ->type('email', $job['email'])
                ->type('phone', $job['phone'])
                ->press('submit')
                ->pause(0)
                ->assertPathIs('/initiate-bid')
                ->pause(0);
        });
//        dd($user);
    }


    public function test_if_phone_and_email_are_not_passed_in_then_the_page_is_redirected_back_to_the_initiate_bid_page()
    {

        $job = $this->createContractor();

        $this->browse(function (Browser $browser) use ($job) {
//            dd($user->id);
            $browser->loginAs(User::find($job['contractorId']))
                ->pause(0)
                ->visit('/initiate-bid')
                ->type('customerName', $job['customerName'])
                ->type('jobName', $job['name'])
                ->type('email', '')
                ->type('phone', '')
                ->press('submit')
                ->pause(0)
                ->assertPathIs('/initiate-bid')
                ->pause(0);
        });

    }

    public function test_validation_for_invalid_email()
    {

        $job = $this->createContractor();

        $this->browse(function (Browser $browser) use ($job) {
//            dd($user->id);
            $browser->loginAs(User::find($job['contractorId']))
                ->pause(0)
                ->visit('/initiate-bid')
                ->type('customerName', $job['customerName'])
                ->type('jobName', $job['name'])
                ->type('email', 'im invalid email')
                ->type('phone', '')
                ->press('submit')
                ->pause(0)
                ->assertPathIs('/initiate-bid')
                ->pause(0);
        });

    }

    public function test_validation_for_phone_with_too_few_numbers()
    {

        $job = $this->createContractor();

        $this->browse(function (Browser $browser) use ($job) {
//            dd($user->id);
            $browser->loginAs(User::find($job['contractorId']))
                ->pause(0)
                ->visit('/initiate-bid')
                ->type('customerName', $job['customerName'])
                ->type('jobName', $job['name'])
                ->type('email', '')
                ->type('phone', '123456')
                ->press('submit')
                ->pause(0)
                ->assertPathIs('/initiate-bid')
                ->pause(0);
        });

    }

    public function test_validation_for_phone_with_too_many_numbers()
    {

        $job = $this->createContractor();

        $this->browse(function (Browser $browser) use ($job) {
//            dd($user->id);
            $browser->loginAs(User::find($job['contractorId']))
                ->pause(0)
                ->visit('/initiate-bid')
                ->type('customerName', $job['customerName'])
                ->type('jobName', $job['name'])
                ->type('email', '')
                ->type('phone', '1234567891011')
                ->press('submit')
                ->pause(0)
                ->assertPathIs('/initiate-bid')
                ->pause(0);
        });

    }


    // TODO: need a test to test the dropdown
    /** @test */
//    public function put_in_a_name_a_drop_down_of_names_appears_click_on_name_email_and_phone_is_auto_filled() {
//
//        $job = $this->createContractor();
//
//        $this->browse(function (Browser $browser) use ($job) {
////            dd($user->id);
//            $browser->loginAs(User::find($job['contractorId']))
//                ->pause(0)
//                ->visit('/initiate-bid')
//                ->type('customerName', 'Mac')
//                ->pause(900000)
//                ->press('1234567890')
//                ->assertSee($job['email'])
//                ->assertSee($job['phone'])
//                ->pause(500000);
//        });
//
//    }
    
    /** @test */
    public function if_jobName_is_not_passed_into_the_controller_then_the_jobName_is_autoGenerated() {

        $job = $this->createContractor();

        $this->browse(function (Browser $browser) use ($job) {
//            dd($user->id);
            $browser->loginAs(User::find($job['contractorId']))
                ->pause(0)
                ->visit('/initiate-bid')
                ->type('customerName', $job['customerName'])
                ->type('jobName', '')
                ->type('email', $job['email'])
                ->type('phone', $job['phone'])
                ->press('submit')
                ->pause(0)
                ->assertPathIs('/bid-list')
                ->pause(0);
        });

    }

    public function createContractor()
    {

        $userToUpdate = User::where("phone", "=", "4807034902")->get();

        if (!empty($userToUpdate[0])){
            $userToUpdate[0]->phone = "0000000000";
            $userToUpdate[0]->save();
        }

        $contractor = factory(Contractor::class)->create();

        $user = User::find($contractor->user_id);
        $user->usertype = "contractor";
        $user->save();

//        dd($user->id);

        $faker = Factory::create();
        $job = [
            'customerName' => $faker->name,
            'name' => $faker->name,
            'email' => "pike.shawn@gmail.com",
            'phone' => "4807034902",
            'contractorId' => $user->id
        ];

        return $job;
    }
    
}
