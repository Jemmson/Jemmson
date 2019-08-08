<?php

namespace App\Traits;

trait ConvertPrices {

    public function convertToCents($price)
    {
        return $price * 100;
    }

    public function convertToDollars($price)
    {
        return $price / 100;
    }

}