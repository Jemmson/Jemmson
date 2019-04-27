<?php

namespace Tests\Feature;

use App\Classes\Quickbooks\FakeQuickbooksGateway;
use App\Classes\Quickbooks\QuickbooksGateway;
use App\ContractorCustomer;
use App\Http\Controllers\CustomerController;
use App\Quickbook;
use Tests\TestCase;
use App\User;
use App\Contractor;
use App\Customer;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class CustomerTest extends TestCase
{
    use DatabaseMigrations;

    /**  @test */
//    function check_customer_is_in_the_user_table() {
//        //
//        factory(User::class)->create([
//            'name' => 'Brenda',
//            'usertype' => 'customer'
//        ]);
//        factory(User::class)->create([
//            'name' => 'Bren',
//            'usertype' => 'customer'
//        ]);
//        factory(User::class)->create([
//            'name' => 'Bre',
//            'usertype' => 'customer'
//        ]);
////        $customer = factory(Customer::class)->create([
////            'user_id' => $user->id
////        ]);
//
//        $this->assertEquals(3, count(User::getCustomersInUserTableByName('Bre')));
//
//    }

    /**  @test */
    function get_customers_associated_to_contractors()
    {
        //

        $this->withoutExceptionHandling();

        $user1 = factory(User::class)->create([
            'name' => 'Brenda',
            'usertype' => 'customer'
        ]);
        $user2 = factory(User::class)->create([
            'name' => 'Bren',
            'usertype' => 'customer'
        ]);
        $user3 = factory(User::class)->create([
            'name' => 'Bre',
            'usertype' => 'customer'
        ]);

        $cust1 = factory(Customer::class)->create([
            'user_id' => $user1->id,
            'location_id' => 1,
        ]);

        $cust2 = factory(Customer::class)->create([
            'user_id' => $user2->id,
            'location_id' => 2,
        ]);

        $cust3 = factory(Customer::class)->create([
            'user_id' => $user3->id,
            'location_id' => 3
        ]);


        $user4 = factory(User::class)->create([
            'name' => 'Bre',
        ]);
        $cont = factory(Contractor::class)->create([
            'user_id' => $user4->id
        ]);

        $contcust1 = new ContractorCustomer();
        $contcust1->contractor_user_id = $cont->id;
        $contcust1->customer_user_id = $cust1->id;

//        $contcust2 = new ContractorCustomer();
//        $contcust2->contractor_user_id = $user2->id;
//        $contcust2->customer_user_id = $cust2->id;

        $contcust3 = new ContractorCustomer();
        $contcust3->contractor_user_id = $cont->id;
        $contcust3->customer_user_id = $cust3->id;

        $this->actingAs($user4);

        $response = $this->json('GET', 'api/customer/search', ['query' => 'Bre']);

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => 1
            ])->assertJsonFragment([
                'id' => 3
            ])->assertJsonMissing(
                [
                    'id' => 2
                ]
            );
    }

}
