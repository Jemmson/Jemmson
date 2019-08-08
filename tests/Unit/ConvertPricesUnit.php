<?php

namespace Tests\Feature;

use App\Http\Controllers\TaskController;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Traits\ConvertPrices;

class ConvertPricesUnit extends TestCase
{

    /**  @test */
    function shouldConvertDollarsToCents() {
        //
        $price = 100;

        $cdtc = new TaskController();

        $this->assertEquals(10000, $cdtc->convertToCents($price));
    }

    /**  @test */
    function shouldConvertCentsToDollars() {
        //
        $price = 10000;

        $cdtc = new TaskController();

        $this->assertEquals(100, $cdtc->convertToDollars($price));
    }

}
