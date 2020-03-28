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
        return $this->belongsTo(User::class);
    }

    public static function get($accountId)
    {
        $stripeEvent = StripeEvent::where('account_id', '=', $accountId)->get()->first();
        if (\is_null($stripeEvent)) {
            return new StripeEvent();
        }

        return $stripeEvent;
    }

    public function updateTable($accountId, $event, $eventType)
    {
        $this->account_id = $accountId;
        if ($eventType == 'account_updated') {
            $this->account_updated = $event;
        }

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
