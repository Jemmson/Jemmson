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
use Faker\Generator as Faker;


class CustomerTest extends TestCase
{
    use RefreshDatabase;


    /* @test */
    public function test_that_a_customer_can_update_notes_for_a_job()
    {

        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'usertype' => 'customer'
        ]);

        factory(Customer::class)->create([
           'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->json('POST', '/customer/updateCustomerNotes', [
            'customerNotesMessage' => 'my sentence',
            'customer_id' => $user->id
        ]);

        $response->assertJsonFragment([
           'success' => 'success'
        ]);

    }

}
