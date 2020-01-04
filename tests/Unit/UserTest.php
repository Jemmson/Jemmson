<?php

namespace Tests\Unit;

use Tests\Feature\Traits\JobTaskTrait;
use Tests\Feature\Traits\JobTrait;
use Tests\Feature\Traits\TaskTrait;
use Tests\Feature\Traits\UserTrait;
use Tests\Feature\Traits\UtilitiesTrait;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\User;

class UserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use JobTaskTrait;
    use JobTrait;
    use TaskTrait;
    use UtilitiesTrait;
    use UserTrait;

   /**  @test */
   function approves_subs_bid() {
       //
       $general = $this->createContractor();
       $sub1 = $this->createContractor();
       $sub2 = $this->createContractor();
       $sub3 = $this->createContractor();

       factory(BidContractorJobTask::class)->create([
           "sub1" => $sub1->id,
           "status" => 'initiated',
           "job_task_id" => 1
           ]);
       factory(BidContractorJobTask::class)->create([
           "sub1" => $sub2->id,
           "status" => 'initiated',
           "job_task_id" => 1
           ]);
       factory(BidContractorJobTask::class)->create([
           "sub1" => $sub3->id,
           "status" => 'initiated',
           "job_task_id" => 1
           ]);

   }

}
