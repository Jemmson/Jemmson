<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App;

class ContractorTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {

        $contractor = factory(\App\Contractor::class)->create();
        $location = factory(\App\Location::class)
            ->create(['user_id' => $contractor->user_id]);
        $contractor->location_id = $location->id;
        $contractor->save();
        $user = \App\User::find($contractor->user_id);
        $user->location_id = $location->id;
        $user->save();

        $this->get('/');
        $this->click('login');
//        $this->see('manage');
    }
}
