<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JobTask extends TestCase
{


    /**  @test */
    function shouldBeAbleToAddATask() {
        //
        $this->withoutExceptionHandling();

        $general = $this->createAUser('contractor', 1, 1, [], [
            'company_name' => 'Albertsons',
            'free_jobs' => 5
        ]);
    }

    /**  @test */
    function subs_bid_has_been_accepted_but_sub_wants_delete_the_task_and_not_do_the_job_so_tak_should_be_remove_from_bid_contractor_job_table_and_subs_should_show_as_pending_or_accept_bid_on_job_table() {
        //

    }

    /**  @test */
    function should_() {
        //

    }

}
