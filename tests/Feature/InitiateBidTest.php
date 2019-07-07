<?php

namespace Tests\Feature;

use App\Location;
use Tests\TestCase;
use App\User;
use App\Customer;
use App\Contractor;
use App\ContractorCustomer;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InitiateBidTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }


    public function test_that_I_am_returning_the_correct_json_response()
    {

        $this->withoutExceptionHandling();

        $user1 = factory(User::class)->create([
            'name' => 'sally',
            'first_name' => 'sally',
            'last_name' => 'connors',
            'email' => 'a@a.com',
            'usertype' => 'customer',
            'password_updated' => 1,
        ]);

        $location1 = factory(Location::class)->create();

        factory(Customer::class)->create([
            'location_id' => $location1->id,
            'user_id' => $user1->id,
        ]);

        $user1->location_id = $location1->id;
        $user1->save();

        $user2 = factory(User::class)->create([
            'name' => 'sallyandra',
            'first_name' => 'sallyandra',
            'last_name' => 'connorandra',
            'email' => 'b@b.com',
            'password_updated' => 1,
            'usertype' => 'customer',
        ]);

        $location2 = factory(Location::class)->create();

        factory(Customer::class)->create([
            'location_id' => $location2->id,
            'user_id' => $user2->id,
        ]);

        $user2->location_id = $location2->id;
        $user2->save();


        $contractor = factory(User::class)->create([
            'name' => 'jack',
            'email' => 'c@c.com',
            'password_updated' => 1,
            'usertype' => 'contractor',
        ]);

        $location3 = factory(Location::class)->create();


        $cont = factory(Contractor::class)->create([
            'location_id' => $location3->id,
            'user_id' => $contractor->id,
        ]);

        $contractor->location_id = $location3->id;
        $contractor->save();

        $cc = new ContractorCustomer();
        $cc->contractor_user_id = $contractor->id;
        $cc->customer_user_id = $user1->id;
        $cc->save();

        $cc = new ContractorCustomer();
        $cc->contractor_user_id = $contractor->id;
        $cc->customer_user_id = $user2->id;
        $cc->save();

        $response = $this->actingAs($contractor)->json('GET', '/customer/search', ['query' => 'sally']);

        $this->assertDatabaseHas('users', ['name' => 'sally']);
        $this->assertDatabaseHas('users', ['name' => 'sallyandra']);

//        $response->assertJsonFragment([
//                'created' => true,
//            ]);

        $response->assertJsonFragment([
                'name' => 'sally',
            ]);

        $response->assertJsonFragment([
                'name' => 'sallyandra',
            ]);
    }
}
