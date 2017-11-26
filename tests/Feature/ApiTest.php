<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApiTest extends TestCase
{
    /**
     * Test job actions
     *
     * @return void
     */
    public function test_job_accept()
    {
        $response =$this->json('POST', 
                               'api/job/action', 
                               ['job_id' => 1, 'action' => 'accept']);
        $response->assertJson(['status' => true]);
    }

    /**
     * Test job actions
     *
     * @return void
     */
    public function test_job_approved()
    {
        $response =$this->json('POST', 
                               'api/job/action', 
                               ['job_id' => 1, 'action' => 'approve']);
        $response->assertJson(['status' => true]);
    }

    /**
     * Test job actions
     *
     * @return void
     */
    public function test_job_decline()
    {
        $response =$this->json('POST', 
                               'api/job/action', 
                               ['job_id' => 1, 'action' => 'decline']);
        $response->assertJson(['status' => true]);
    }


}
