<?php

namespace Tests\Feature;

use App\Contractor;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoggedInTest extends TestCase
{

    use DatabaseMigrations;


    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLoggedInValue()
    {

        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $contractor = factory(Contractor::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->json('GET', 'loggedIn');

        $response
            ->assertStatus(201)
            ->assertJson([
                'created' => true,
            ]);
    }




}
