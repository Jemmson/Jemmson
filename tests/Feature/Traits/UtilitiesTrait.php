<?php

namespace Tests\Feature\Traits;

trait UtilitiesTrait {
    public function mergeArrays($payload, $array)
    {
        if (!empty($array)) {
            foreach ($array as $k => $a) {
                $payload[$k] = $a;
            }
        }
        return $payload;
    }
}
