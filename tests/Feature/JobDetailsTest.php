<?php

namespace Tests\Feature;

use GCNstripe_1CustNReg_1Job_Initiated;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Payloads\NewJob\NewJobInitiatedPayload;
use Tests\TestCase;

class JobDetailsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testBrandNewJob()
    {
        $this->refreshDatabase();
        $this->seed(GCNstripe_1CustNReg_1Job_Initiated::class);
        $general = User::find(1);
        $newJob = new NewJobInitiatedPayload();
        $response = $this->actingAs($general)->json('get', '/job/1');
        $response->assertJson($newJob->getInitiatedJobResponse());
        $this->assertDatabaseHas('users', $newContractor->basic_users_table());
    }
}
