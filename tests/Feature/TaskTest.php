<?php

namespace Tests\Feature;

use App\Task;
use App\Job;
//use Carbon\Carbon;
use Illuminate\Support\Carbon;
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
    public function validating_taskName_taskPrice_subTaskPrice_start_when_accepted_are_required()
    {

        $response = $this->json('POST', '/api/task/addTask', []);

        $response->assertJsonFragment([
            "errors" => [
                "start_when_accepted" => ["The start when accepted field is required."],
                "subTaskPrice" => ["The sub task price field is required."],
                "taskName" => ["The task name field is required."],
                "taskPrice" => ["The task price field is required."]],
            "message" => "The given data was invalid."
        ]);
    }

    /** @test */
    public function taskPrice_must_be_greater_than_sub_price() {

        $c = Carbon::now();
        $currentDay = $c->year."-".$c->month."-".$c->day." 12:00:00";

        $response = $this->json('POST', '/api/task/addTask', [
            'taskName' => 'new pump',
            'taskPrice' => 199,
            'subTaskPrice' => 200,
            'start_when_accepted' => false,
            'start_date' => $currentDay,
            'qty' => 1,
            'qtyUnit' => 'pump'
        ]);

        $response->assertExactJson([
            "message" => "Unit price for customer needs to be greater than or equal to Unit Price for Sub",
            "errors" => ["error" => ['Unit price for customer needs to be greater than or equal to Unit Price for Sub']]
        ]);

    }

    /** @test */
    public function an_error_is_thrown_if_contractors_task_price_less_than_a_subs_price()
    {

//        dd(Carbon::parse('+1 week'));
//        dd(Carbon::parse('YYYY-MM-DD H:m:S az'));
//        dd(Carbon::now('YYYY'));
        $c = Carbon::now();
        $currentDay = $c->year."-".$c->month."-".$c->day." 12:00:00";

        $response = $this->json('POST', '/api/task/addTask', [
            'taskName' => 'new pump',
            'taskPrice' => 199,
            'subTaskPrice' => 200,
            'start_when_accepted' => false,
            'start_date' => $currentDay,
            'qty' => 1,
            'qtyUnit' => 'pump'
        ]);

        $response->assertExactJson([
            "message" => "Unit price for customer needs to be greater than or equal to Unit Price for Sub",
            "errors" => ["error" => ['Unit price for customer needs to be greater than or equal to Unit Price for Sub']]
        ]);

    }

    /** @test */
    public function update_existing_task_add_to_the_jobTask_table() {

        // Given
        $t = new Task;
        $t->name = 'Trim Oak Tree';
        $t->save();

        $c = Carbon::now();
        $currentDay = $c->year."-".$c->month."-".$c->day." 12:00:00";

        $this->assertDatabaseHas('tasks', [
           'name' => 'Trim Oak Tree',
           'proposed_cust_price' => null,
           'proposed_sub_price' => null,
        ]);

        $response = $this->json('POST', '/api/task/addTask', [
            'taskName' => 'Trim Oak Tree 1',
            'taskPrice' => 201,
            'subTaskPrice' => 200,
            'start_when_accepted' => false,
            'start_date' => $currentDay,
            'qty' => 1,
            'qtyUnit' => 'pump',
            'updateTask' => true,
            'createNew' => false
        ]);

    }

        /** @test */
    public function that_the_db_does_not_add_a_task_if_the_task_is_meant_to_be_updated() {

//        $this->

    }

    /** @test */
    public function that_the_db_does_update_a_task_if_the_task_is_meant_to_be_ignored() {

//        $this->

    }

    /** @test */
    public function that_the_db_does_add_a_task_if_the_task_is_meant_to_updated() {


    }
}
