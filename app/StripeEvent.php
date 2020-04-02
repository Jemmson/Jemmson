<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class StripeEvent extends Model
{
    //
    protected $table = 'stripe_events';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'stripe_id', 'account_id');
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

    public function updateTable($event)
    {
        $this->account_id = $event->account_id;
        $this->event_id = $event->id;
        $this->event_type = $event->type;
        $this->event_payload = $event;

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
