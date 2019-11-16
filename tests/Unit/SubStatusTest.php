<?php

namespace Tests\Unit;

use App\SubStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubStatusTest extends TestCase
{

    use RefreshDatabase;

    /**  @test */
    function test_that_I_am_saving_the_initiated_status_in_the_database() {
        // 
        
        $subStatus = new SubStatus();
        
        $subStatus->initiated(1,1);
        
        $this->assertDatabaseHas('sub_status', [
           "user_id" => 1,
           "job_task_id" => 1,
           "status_number" => 1,
           "status" => "sub.initiated"
        ]);
        
    }
}
