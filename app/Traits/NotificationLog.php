<?php

namespace App\Traits;
use Illuminate\Support\Facades\Log;

trait NotificationLog {

    public static function info($message) {
        if (env('APP_ENV') != 'production') {
//            Log::info($message);
        }
    }

}