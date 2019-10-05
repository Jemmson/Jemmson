<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\User;

class UserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**  @test */
    function update_user_password_if_the_password_was_sent_through() {
        //
        $user = factory(User::class)->create();


    }
}
