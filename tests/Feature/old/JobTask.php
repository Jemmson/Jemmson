<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\JobTaskTrait;
use Tests\Feature\Traits\JobTrait;
use Tests\Feature\Traits\TaskTrait;
use Tests\Feature\Traits\UserTrait;
use Tests\Feature\Traits\UtilitiesTrait;
use Tests\TestCase;

class JobTask extends TestCase
{

    use WithFaker;
    use UtilitiesTrait;
    use Setup;
    use UserTrait;
    use JobTaskTrait;
    use TaskTrait;
    use JobTrait;
    use RefreshDatabase;

    /**  @test */
    function shouldBeAbleToAddATask()
    {
        //
        $this->withoutExceptionHandling();

        $general = $this->createUser('contractor', 1, 1, [], [
            'company_name' => 'Albertsons',
            'free_jobs' => 5
        ]);
    }

    /**  @test */
    function that_when_the_sub_finishes_the_task_that_the_general_is_notifed()
    {

        // GIVEN


        // ACTION


        // ASSERTION


    }

}
