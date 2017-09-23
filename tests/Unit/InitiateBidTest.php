<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App;

class InitiateBidTest extends TestCase
{
//    use DatabaseMigrations;
    /**s
     * A basic test example.
     *
     * @return void
     */

    public function testUsersHaveContractors()
    {
        $users = factory(App\User::class, 3)
            ->create()
            ->each(function ($u) {
                $u->contractors()->save(factory(App\Contractor::class)->make());
            });
    }

}
