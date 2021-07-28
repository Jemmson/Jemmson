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

class AssessorTest extends TestCase
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
    function checklocationRoute() {

        // GIVEN


        // ACTION


        // ASSERTION


    }
}
