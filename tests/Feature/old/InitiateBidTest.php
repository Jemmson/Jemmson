<?php

namespace Tests\Feature;

use App\Location;
use Tests\Feature\Traits\JobTaskTrait;
use Tests\Feature\Traits\JobTrait;
use Tests\Feature\Traits\TaskTrait;
use Tests\Feature\Traits\UserTrait;
use Tests\Feature\Traits\UtilitiesTrait;
use Tests\TestCase;
use App\User;
use App\Customer;
use App\Contractor;
use App\Job;
use App\ContractorCustomer;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\BidInitiated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\AnonymousNotifiable;


class InitiateBidTest1 extends TestCase
{

    use WithFaker;
    use UtilitiesTrait;
    use Setup;
    use UserTrait;
    use JobTaskTrait;
    use TaskTrait;
    use JobTrait;
    use RefreshDatabase;

    protected function initiateBid($general, $params)
    {
        return $this->actingAs($general)->json('POST', '/initiate-bid', $params);
    }

    public function test_That_The_Right_Response_Is_Returned_And_DB_Is_Populated_Appropriately()
    {

        $this->withoutExceptionHandling();

        $general = $this->createUser('contractor', 1, 1, [], [
            'company_name' => 'Albertsons',
            'free_jobs' => 5
        ]);

        $lastName = $this->faker->lastName;
        $firstName = $this->faker->firstName;
        $jobName = "2020-" . rand(100, 999) . "-$lastName-$firstName";

        $response = $this->initiateBid($general, [
            "busy" => false,
            "customerName" => "$firstName $lastName",
            "email" => "",
            "errors" => ["errors" => []],
            "firstName" => "$firstName",
            "id" => "",
            "jobName" => $jobName,
            "lastName" => $this->faker->lastName,
            "paymentType" => "cash",
            "paymentTypeDefault" => null,
            "phone" => "(480)-703-4902",
            "quickbooks_id" => "",
            "successful" => false,
            "taxRate" => 0
        ]);

        $response->assertJsonStructure(
            [
                "job" => [
                    "contractor_id",
                    "customer_id",
                    "job_name",
                    "payment_type",
                    "status",
                    "location_id",
                    "updated_at",
                    "created_at",
                    "id"
                ],
                "customer" => [
                    "name",
                    "phone",
                    "first_name",
                    "last_name",
                    "usertype",
                    "password_updated",
                    "updated_at",
                    "created_at",
                    "id",
                    "tax_rate"
                ],
                "jobStatuses" => [
                    "job_id",
                    "status_number",
                    "status",
                    "updated_at",
                    "created_at",
                    "id"
                ]
            ]
        );

//        Contractor can create new a new job
        $this->assertDatabaseHas('contractors', [
            "user_id" => $general->id
        ]);

        $this->assertGreaterThan(0, $general->contractor()->get()->first()->free_jobs);

//        Check a customer has been added to the database
        $newCustomer = User::where('name', '=', "$firstName $lastName")->get()->first();

        $this->assertDatabaseHas('users', [
            "id" => $newCustomer->id,
            "location_id" => null,
            "usertype" => "customer",
            "password_updated" => 0,
            'phone' => '4807034902'
        ]);

        $this->assertDatabaseHas('customers', [
            "user_id" => $newCustomer->id
        ]);

//**    Check that customer and contractor are associated to each other in the database
        $this->assertDatabaseHas('contractor_customer', [
            "contractor_user_id" => $general->id,
            "customer_user_id" => $newCustomer->id
        ]);


//      Check that a job has been created
        $this->assertDatabaseHas('jobs', [
            'contractor_id' => $general->id,
            'customer_id' => $newCustomer->id,
            'job_name' => $jobName,
            'status' => "bid.initiated",
            'location_id' => null
        ]);


        $res = json_decode($response->getContent());

//        dd($res->job->id);

//      Check the customer has been notified
        $this->assertDatabaseHas('user_tokens', [
            "job_id" => $res->job->id,
            "user_id" => $res->customer->id,
            "job_task_id" => null,
            "job_step" => $res->jobStatuses->status,
            "job_task_step" => null,
            "type" => 'email',
        ]);

        $this->assertDatabaseHas('user_tokens', [
            "job_id" => $res->job->id,
            "user_id" => $res->customer->id,
            "job_task_id" => null,
            "job_step" => $res->jobStatuses->status,
            "job_task_step" => null,
            "type" => 'text',
        ]);

        $this->assertDatabaseHas('customers', [
            "user_id" => $newCustomer->id,
            "location_id" => null,
            "email_method_of_contact" => null,
            "phone_method_of_contact" => null,
            "sms_method_of_contact" => null,
            "notes" => null
        ]);

//        Notification::fake();
//
//        // Assert that no notifications were sent...
//        Notification::assertNothingSent();
//
//        $job = Job::where('job_name', '=', $jobName)->get()->first();
//
//        Notification::assertSentTo(
//            $newCustomer,
//            $job,
//            BidInitiated::class,
//            function ($notification, $channels) use ($job, $newCustomer) {
//                return $notification->job[0]->id === $job->id;
//            }
//        );

        // Assert a notification was sent to the given users...
//        Notification::assertSentTo(
//            [$newCustomer], BidInitiated::class
//        );

//        // Assert a notification was not sent...
//        Notification::assertNotSentTo(
//            [$newCustomer], AnotherNotification::class
//        );

//        // Assert a notification was sent via Notification::route() method...
//        Notification::assertSentTo(
//            new AnonymousNotifiable, OrderShipped::class
//        );

    }

    public function test_that_I_am_returning_the_correct_json_response_when_searching_for_a_name()
    {

        $this->withoutExceptionHandling();

        $user1 = factory(User::class)->create([
            'name' => 'sally',
            'first_name' => 'sally',
            'last_name' => 'connors',
            'email' => 'a@a.com',
            'usertype' => 'customer',
            'password_updated' => 1,
        ]);

        $location1 = factory(Location::class)->create();

        factory(Customer::class)->create([
            'location_id' => $location1->id,
            'user_id' => $user1->id,
        ]);

        $user1->location_id = $location1->id;
        $user1->save();

        $user2 = factory(User::class)->create([
            'name' => 'sallyandra',
            'first_name' => 'sallyandra',
            'last_name' => 'connorandra',
            'email' => 'b@b.com',
            'password_updated' => 1,
            'usertype' => 'customer',
        ]);

        $location2 = factory(Location::class)->create();

        factory(Customer::class)->create([
            'location_id' => $location2->id,
            'user_id' => $user2->id,
        ]);

        $user2->location_id = $location2->id;
        $user2->save();


        $contractor = factory(User::class)->create([
            'name' => 'jack',
            'email' => 'c@c.com',
            'password_updated' => 1,
            'usertype' => 'contractor',
        ]);

        $location3 = factory(Location::class)->create();


        $cont = factory(Contractor::class)->create([
            'location_id' => $location3->id,
            'user_id' => $contractor->id,
        ]);

        $contractor->location_id = $location3->id;
        $contractor->save();

        $cc = new ContractorCustomer();
        $cc->contractor_user_id = $contractor->id;
        $cc->customer_user_id = $user1->id;
        $cc->save();

        $cc = new ContractorCustomer();
        $cc->contractor_user_id = $contractor->id;
        $cc->customer_user_id = $user2->id;
        $cc->save();

        $response = $this->actingAs($contractor)->json('GET', '/customer/search', ['query' => 'sally']);

        $this->assertDatabaseHas('users', ['name' => 'sally']);
        $this->assertDatabaseHas('users', ['name' => 'sallyandra']);

//        $response->assertJsonFragment([
//                'created' => true,
//            ]);

        $response->assertJsonFragment([
            'name' => 'sally',
        ]);

        $response->assertJsonFragment([
            'name' => 'sallyandra',
        ]);
    }

    private function addingAContractor()
    {
        $contractor = factory(User::class)->create([
            'password_updated' => 1,
            'usertype' => 'contractor',
        ]);

        $location = factory(Location::class)->create();

        $cont = factory(Contractor::class)->create([
            'location_id' => $location->id,
            'user_id' => $contractor->id,
            'accounting_software' => 'quickBooks'
        ]);

        $contractor->location_id = $location->id;
        $contractor->save();

        return $contractor;
    }
}
