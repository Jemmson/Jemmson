<?php

namespace Tests\Feature;

use App\Notifications\BidInitiated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Traits\JobTaskTrait;
use Tests\Feature\Traits\JobTrait;
use Tests\Feature\Traits\TaskTrait;
use Tests\Feature\Traits\UserTrait;
use Tests\Feature\Traits\UtilitiesTrait;
use Tests\TestCase;

class ExampleTest extends TestCase
{

    use WithFaker;
    use UtilitiesTrait;
    use Setup;
    use UserTrait;
    use JobTaskTrait;
    use TaskTrait;
    use JobTrait;
    use RefreshDatabase;

    public function testBidInitiated()
    {
        $customer =

        Notification::fake();

        // Assert that no notifications were sent...
        Notification::assertNothingSent();

        // Perform order shipping...

        // Assert a specific type of notification was sent meeting the given truth test...
        Notification::assertSentTo(
            $customer,
            function (BidInitiated $notification, $channels) use ($order) {
                return $notification->order->id === $order->id;
            }
        );

        // Assert a notification was sent to the given users...
        Notification::assertSentTo(
            [$customer], BidInitiated::class
        );

//        // Assert a notification was not sent...
//        Notification::assertNotSentTo(
//            [$user], AnotherNotification::class
//        );

//        // Assert a notification was sent via Notification::route() method...
//        Notification::assertSentTo(
//            new AnonymousNotifiable, OrderShipped::class
//        );

        // Assert Notification::route() method sent notification to the correct user...
        Notification::assertSentTo(
            new AnonymousNotifiable,
            BidInitiated::class,
            function ($notification, $channels, $notifiable) use ($customer) {
                return $notifiable->routes['nexmo'] === $customer->email;
            }
        );
    }
}