<?php

namespace App\Traits;
use Illuminate\Support\Facades\Log;

trait NotificationLog {

    public static function info($message) {
        Log::info($message);
    }

}