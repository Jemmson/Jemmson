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
    function can_get_the_current_job_task() {
        //
        $general = $this->createContractor();
        $this->createJob(1, 1, 1, 'initiated');
        $this->createTask(1);
        $jt = $this->createJobTask(1,1,1,1, 'initiated');
        $getJT = $general->getCurrentJobTask($jt->id);

        $this->assertEquals($jt->id, $getJT->id);
    }
}
