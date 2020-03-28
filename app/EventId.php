<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class EventId extends Model
{
    //
    protected $table = 'event_id';
    protected $guarded = [];

    public static function get($eventId)
    {
        $eventIdentifier = EventId::where('event_id', '=', $eventId)->get()->first();
        if (\is_null($eventIdentifier)) {
            return new EventId();
        }

        return false;
    }

    public function updateTable($eventId)
    {
        $this->event_id = $eventId;

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
