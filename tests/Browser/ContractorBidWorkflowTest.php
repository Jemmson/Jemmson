<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Contractor;
use App\User;
use ContractorFactory;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContractorBidWorkflowTest extends DuskTestCase
{
//    use DatabaseMigrations;
    use DatabaseTransactions;

    /**
     * A Dusk test example.
     *
     * @return void
     * @test
     * @group cont
     */
    public function ContractorInitatiatesABid()
    {
//        given
//        contractor exists
        $cont = factory(\App\Contractor::class)->create();
        $this->assertDatabaseHas('contractors', [
            'user_id' => $cont->user_id
        ]);
        echo $cont;
        var_dump($cont);
//        customer may or may not exist in the application
//        there are no subs for this job
//        there is no bid or invoice
//
    }
}
