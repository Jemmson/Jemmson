<?php

namespace Tests\Feature;

use App\Quickbook;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class QuickbookTest extends TestCase
{
    use DatabaseMigrations;

    /**  @test */
    function will_generate_csrf_token() {
        //
        $qb = new Quickbook();
        $guid = $qb->generateCsrf();
        $this->assertNotEquals('', $guid);
    }

    /**  @test */
    function will_save_generated_token_to_the_database() {
        //
        $qb = new Quickbook();
        $guid = $qb->generateCsrf();
        $qb->addGuidToTable($guid);
        $this->assertDatabaseHas('quickbook_csrf_tokens',[
           'guid' => $guid,
           'consumed' => false,
           'expired' => false
        ]);
    }

    /**  @test */
    function should_check_that_the_guid_that_was_returned_is_in_the_database() {
        //
        $qb = new Quickbook();
        $guid = $qb->generateCsrf();
        $qb->addGuidToTable($guid);
        $this->assertEquals(true, $qb->checkGuidIsInDb($guid));
    }

    /**  @test */
    function check_that_the_guid_was_created_within_tthe_last_5_minutes() {
        //
        $qb = new Quickbook();
        $guid = $qb->generateCsrf();
        $qb->addGuidToTable($guid);
        $this->assertEquals(true, $qb->checkGuidIsLessThan5MinutesOld($guid));
    }

    /**  @test */
    function check_that_the_guid_was_not_created_within_the_last_5_minutes() {
        //
        $qb = new Quickbook();
        $guid = $qb->generateCsrf();
        $qb->addGuidToTable($guid);

        $date = Carbon::now()->addMinutes(10);

        $statement = "Update quickbook_csrf_tokens set created_at = '".$date."' where guid = '".$guid."'";
        DB::select($statement);


        $this->assertEquals(false, $qb->checkGuidIsLessThan5MinutesOld($guid));
    }

    /**  @test */
    function the_csrf_token_should_be_consumed_if_the_guid_is_valid() {
        //
        $qb = new Quickbook();
        $guid = $qb->generateCsrf();
        $qb->addGuidToTable($guid);
        $qb->consumeToken($guid);

        $this->assertDatabaseHas('quickbook_csrf_tokens', [
           'guid' => $guid,
            'consumed' => 1
        ]);
    }
}
