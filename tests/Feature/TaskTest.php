<?php

namespace Tests\Feature;

use App\Task;
use App\Job;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Contractor;
use App\Http\Controllers\TaskController;

class TaskTest extends TestCase
{

//    use RefreshDatabase;

    use DatabaseMigrations;

    /**
     * A basic test example.
     *
     * @test
     */
//    public function a_task_can_be_created()
//    {
//
//        //Given
////        $user = factory(User:class)->create([
////            'usertype' => 'contractor'
////        ]);
//
////        $job = factory(Job::class)->create();
//
//        $task = factory(Task::class)->create();
//        return $task;
//
////        $faker = Factory::create();
////        $contractor = Contractor::make([
////            'user_id' => 7,
////            'company_name' => $faker->name,
////            'company_logo_name' => $faker->name,
////            'email_method_of_contact' => '1',
////            'sms_method_of_contact' => '1',
////            'phone_method_of_contact' => '1',
////        ]);
//
//
//        //Action
//
//
//        //Assert
//
//
////        $t = new TaskController();
////        $request = new Request([
////            'taskName' => $faker->name,
////            'taskPrice' =>  $faker->numberBetween(100, 200),
////            'subTaskPrice' => $faker->numberBetween(100, 200),
////            'start_when_accepted' => $faker->dateTime,
////            //            'sub_sets_own_price_for_job' => 'required',
////            'start_date' => $faker->dateTime,
////            'qty' => $faker->numberBetween(100, 200),
////            'qtyUnit' => $faker->numberBetween(100, 200)
////        ]);
////        $t->addTask($request);
//
//
////        $contractor = factory(\App\Contractor::class)->create();
////        $location = factory(\App\Location::class)
////            ->create(['user_id' => $contractor->user_id]);
////        $contractor->location_id = $location->id;
////        $contractor->save();
////        $user = \App\User::find($contractor->user_id);
////        $user->location_id = $location->id;
////        $user->save();
////
////        $this->get('/');
////        $this->click('login');
//////        $this->see('manage');
//    }


//    /* @test */
//    public function testTaskWasCreated()
//    {
//
////        dd(Carbon::parse('+1 week'));
//
//
//        $job = factory(Job::class)->create();
//        $task = factory(Task::class)->create();
//
////        dd($job->id);
//
//        $response = $this->json('POST', '/api/task/addTask', [
//            'taskName' => 'new pump',
//            'taskPrice' => 234,
//            'subTaskPrice' => 0,
//            'start_when_accepted' => false,
//            'start_date' => '2018-12-29 14:26:50',
//            'qty' => 1,
//            'qtyUnit' => 'pump',
//            'jobId' => $job->id,
//            'updateTask' => true,
//            'createNew' => false,
//            'taskId' => $task->id,
//        ]);
//
////        dd($response);
//
////        $r = json_decode(json_encode($response));
//
////        dd($r);
//
////        echo $r->baseResponse->original->message;
//
//
//        $response
//            ->assertStatus(201)
//            ->assertJson([
//                'created' => true,
//            ]);
//    }

    /** @test */
    public function an_error_is_thrown_if_contractors_task_price_less_than_a_subs_price() {

        $response = $this->json('POST', '/api/task/addTask', [
            'taskName' => 'new pump',
            'taskPrice' => 199,
            'subTaskPrice' => 200,
            'start_when_accepted' => false,
            'start_date' => '2018-12-29 14:26:50',
            'qty' => 1,
            'qtyUnit' => 'pump'
        ]);

        $response->assertExactJson([
            "message" => "Unit price for customer needs to be greater than or equal to Unit Price for Sub",
            "errors" => ["error" => ['Unit price for customer needs to be greater than or equal to Unit Price for Sub']]
        ]);

    }

}
