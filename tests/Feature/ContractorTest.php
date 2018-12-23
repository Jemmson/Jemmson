<?php

namespace Tests\Feature;

use App\Task;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Contractor;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;

class ContractorTest extends TestCase
{

//    use DatabaseMigrations;

    /**
     * A basic test example.
     *
     * @test
     */
    public function contractor_can_initiate_a_bid()
    {
        $faker = Factory::create();
        $contractor = Contractor::make([
            'user_id' => 7,
            'company_name' => $faker->name,
            'company_logo_name' => $faker->name,
            'email_method_of_contact' => '1',
            'sms_method_of_contact' => '1',
            'phone_method_of_contact' => '1',
        ]);

//        $t = new TaskController();
//        $request = new Request([
//            'taskName' => $faker->name,
//            'taskPrice' =>  $faker->numberBetween(100, 200),
//            'subTaskPrice' => $faker->numberBetween(100, 200),
//            'start_when_accepted' => $faker->dateTime,
//            //            'sub_sets_own_price_for_job' => 'required',
//            'start_date' => $faker->dateTime,
//            'qty' => $faker->numberBetween(100, 200),
//            'qtyUnit' => $faker->numberBetween(100, 200)
//        ]);
//        $t->addTask($request);



//        $contractor = factory(\App\Contractor::class)->create();
//        $location = factory(\App\Location::class)
//            ->create(['user_id' => $contractor->user_id]);
//        $contractor->location_id = $location->id;
//        $contractor->save();
//        $user = \App\User::find($contractor->user_id);
//        $user->location_id = $location->id;
//        $user->save();
//
//        $this->get('/');
//        $this->click('login');
////        $this->see('manage');
    }
}
