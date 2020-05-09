<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StripeEvent extends Model
{
    //
    protected $table = 'stripe_events';
    protected $guarded = [];
    public $primaryKey = 'event_id';

    public function stripeExpress()
    {
        return $this->belongsTo(User::class, 'customer_stripe_id', 'account_id');
    }

    public static function get($eventId)
    {
        $stripeEvent = StripeEvent::where('event_id', '=', $eventId)->get()->first();
        if (\is_null($stripeEvent)) {
            return new StripeEvent();
        }

        return $stripeEvent;
    }

    public static function exists($eventId)
    {
        $stripeEvent = StripeEvent::where('eventId', '=', $eventId)->get()->first();

        if (\is_null($stripeEvent)) {
            return new StripeEvent();
        }

        return $stripeEvent;
    }

    public function getAccountId($event)
    {
        $account = [
            "transfer.created" => $event->data->object->destination,
            "payment.created" => $event->account,
            "payment_intent.created" => $event->data->object->on_behalf_of,
            "charge.succeeded" => $event->data->object->on_behalf_of,
            "payment_intent.succeeded" => $event->data->object->on_behalf_of,
            "customer.created" => $event->data->object->id,
            "payment_method.attached" => $event->data->object->customer,
            "account.application.authorized" => $event->account,
            "account.updated" => $event->account,
            "capability.updated" => $event->account,
            "setup_intent.created" => null,
            "setup_intent.succeeded" => null
        ];

        return $account[$event->type];
    }

    public function updateTable($event)
    {
        $this->customer_id = $event->data->object->customer;
        $this->event_id = $event->id;
        $this->event_type = $event->type;
        $this->event_payload = json_encode($event);

        $this->account_id = $this->getAccountId($event);

        try {
            $this->save();
        } catch (\Exception $e) {
            Log::error(': ' . $e->getMessage());
            return response()->json([
                "message" => "",
                "errors" => ["error" => [$e->getMessage()]]], 404);
        }
    }

}
