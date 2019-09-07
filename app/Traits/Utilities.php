<?php

namespace App\Traits;

trait Utilities {

    public function digitsOnly($num) {
        $digitsOnly = '';

        for($i = 0; $i < strlen($num); $i++) {
            if (is_numeric($num[$i])) {
                $digitsOnly = $digitsOnly . $num[$i];
            }
        }

        return $digitsOnly;
    }

    public static function digitsOnlyStatic($num) {
        $digitsOnly = '';

        for($i = 0; $i < strlen($num); $i++) {
            if (is_numeric($num[$i])) {
                $digitsOnly = $digitsOnly . $num[$i];
            }
        }

        return $digitsOnly;
    }

}