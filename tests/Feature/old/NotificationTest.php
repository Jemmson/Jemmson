<?php

namespace Tests\Feature;

use App\Notifications\BidInitiated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\Feature\Traits\UtilitiesTrait;
use Tests\Feature\Traits\UserTrait;
use Tests\Feature\Traits\JobTrait;
use Tests\Feature\Traits\TaskTrait;
use Tests\Feature\Traits\JobTaskTrait;

class NotificationTest extends TestCase
{

    use UtilitiesTrait;
    use UserTrait;
    use TaskTrait;
    use JobTrait;
    use JobTaskTrait;
    use WithFaker;
    use Setup;
    use RefreshDatabase;

    public function testBidInitiated()
    {
        $this->withExceptionHandling();

        $general = $this->createContractor();
        $customer = $this->createCustomer();

//        dd(json_encode($customer->customer()->get()->first()->location_id));

        $location_id = $customer->customer()->get()->first()->location_id;
        $job = $this->createJob(
            $customer->id,
            $general->id,
            $location_id,
            'bid.in_progress'
        );

        Notification::fake();

        // Assert that no notifications were sent...
        Notification::assertNothingSent();

        // Perform initiating a bid...
        $customer->notify(new BidInitiated($job, $customer));


        // Assert a specific type of notification was sent meeting the given truth test...
        Notification::assertSentTo(
            $customer,
            'hello',
            function (BidInitiated $notification, $channels) use ($job) {
                echo $notification->job->id;
                return $notification->job->id === $job->id;
            }
        );

//        // Assert a notification was sent to the given users...
//        Notification::assertSentTo(
//            [$customer], BidInitiated::class
//        );

//        // Assert a notification was not sent...
//        Notification::assertNotSentTo(
//            [$user], AnotherNotification::class
//        );

//        // Assert a notification was sent via Notification::route() method...
//        Notification::assertSentTo(
//            new AnonymousNotifiable, OrderShipped::class
//        );

//        // Assert Notification::route() method sent notification to the correct user...
//        Notification::assertSentTo(
//            new AnonymousNotifiable,
//            BidInitiated::class,
//            function ($notification, $channels, $notifiable) use ($customer) {
//                return $notifiable->routes['nexmo'] === $customer->phone;
//            }
//        );
    }

//    /**  @test */
//    function test_that_a_Notification_is_sent_to_a_new_customer()
//    {
//        //
//
//        $admin = $this->createAdmin_Contractor();
//
//        $this->initiateABid($admin, 'Joe', 'Smith');
//
//        $user = $this->getNewlyCreatedUser('Joe Smith');
//
//        $this->assertDatabaseHas('user_tokens', [
//            "user_id" => $user->id
//        ]);
//
//        $this->assertDatabaseHas('jobs', [
//            "status" => "bid.initiated"
//        ]);
//    }
//
//    /**  @test */
//    function test_that_notification_is_sent_to_a_sub_when_the_sub_is_invited()
//    {
//        //
//        $admin = $this->createAdmin_Contractor();
//        $customer = $this->create_a_customer();
//        $location_id = $customer->customer()->get()->first()->location()->get()->first()->id;
//        $job = $this->create_a_job(
//            $customer->id,
//            $admin->id,
//            $location_id,
//            'bid.in_progress'
//        );
//        $task = $this->create_a_task($admin->id);
//
//        $job_task = $this->create_a_job_task(
//            $job->id,
//            $task->id,
//            $location_id,
//            $admin->id,
//            'bid_task.initiated'
//        );
//
//        $this->assertDatabaseHas('jobs', [
//            "status" => "bid.in_progress"
//        ]);
//
//        $this->assertDatabaseHas('job_task', [
//            "id" => $job_task->id,
//            "status" => "bid_task.initiated"
//        ]);
//
//        $firstName = 'Kristen';
//        $lastName = 'Battafarano';
//        $name = $firstName . ' ' . $lastName;
//        $response = $this->invite_a_sub($admin, $task->id, $job_task->id, $firstName, $lastName);
//
//        $response->assertJson([
//            "message" => 'success'
//        ]);
//
//        $sub = $this->getNewlyCreatedUser($name);
//
//        $this->assertDatabaseHas('user_tokens', [
//            "user_id" => $sub->id
//        ]);
//
//        $this->assertDatabaseHas('jobs', [
//            "status" => "bid.in_progress"
//        ]);
//
//        $this->assertDatabaseHas('job_task', [
//            "status" => "bid.initiated"
//        ]);
//
//        $this->assertDatabaseHas('bid_contractor_job_task', [
//            "status" => "sub.initiated"
//        ]);
//
//    }
//
//
//    #########################
//    ## Routes
//    #########################
//
//    protected function initiateABid(
//        $admin,
//        $firstName = "Shawn",
//        $lastName = "Pike",
//        $jobName = "pool job",
//        $phone = "(480) 703-4902"
//    )
//    {
//        $params = [
//            "busy" => false,
//            "customerName" => $firstName . " " . $lastName,
//            "email" => "",
//            "errors" => [
//                "errors" => []
//            ],
//            "firstName" => $firstName,
//            "jobName" => $jobName,
//            "lastName" => $lastName,
//            "phone" => $phone,
//            "quickbooks_id" => "",
//            "successful" => false
//        ];
//
//        $response = '';
//
//        try {
//            $response = $this->actingAs($admin)->json('POST', '/initiate-bid', $params);
//        } catch (\Exception $e) {
//            return $e->getMessage();
//        }
//
//        return $response;
//    }
}
