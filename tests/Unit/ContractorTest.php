<?php

namespace Tests\Unit;

use App\Contractor;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Setup;

class ContractorTest extends TestCase
{

    use Setup;
    use RefreshDatabase;

    /**  @test */
    function gettingAllContractorsExceptTheGeneralContractor()
    {
        //

        $general = $this->createAUser('contractor', 1, 1, [], [
            "company_name" => "Albertsons"
        ]);
        $this->createAUser('contractor', 1, 2, [], [
            "company_name" => "Albertsons1"
        ]);
        $this->createAUser('contractor', 1, 3, [], [
            "company_name" => "Albertsons2"
        ]);
        $this->createAUser('contractor', 1, 4, [], [
            "company_name" => "Albertsons3"
        ]);

        $this->actingAs($general);

        $c = new Contractor();

        $subs = $c->getAllContractorsExceptTheGeneralContractor(
            'Albert',
            $general->contractor()->get()->first()->company_name
        );

        $this->assertEquals(3, count($subs));

    }
//
//    /**  @test */
//    function () {
//        //
//
//    }

}
