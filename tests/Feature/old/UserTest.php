<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Customer;
use App\User;

class UserTest extends TestCase
{

    use DatabaseMigrations;

    /**  @test */
    function check_if_the_user_exists_in_the_database_and_throw_an_error_if_the_customer_exists()
    {
        $user = factory(User::class)->create([
            'usertype' => 'customer',
            'phone' => '0000000000'
        ]);

        $data = [
            //
            'name' => 'new customer',
            'password' => bcrypt('asdasd'),
            'usertype' => 'customer',
            'phone' => '000000878700'
        ];

        $user = new User();

        $this->assertEquals(true, $user->checkIfUserExistsElseCreateUser($data));

    }

    /**  @test */
    function check_if_the_user_exists_in_the_database_by_duplicate_phone_number()
    {

        factory(User::class)->create([
            'usertype' => 'customer',
            'email' => 'ghjkghhj@hjk.com',
            'phone' => '0000000000',
        ]);

        $phone = '0000000000';

        $user = new User();

        $this->assertEquals(false, empty(User::checkIfUserExistsByPhoneNumber($phone)));

    }

    /**  @test */
    function check_if_the_user_exists_in_the_database_with_unique_phone_number()
    {

        factory(User::class)->create([
            'usertype' => 'customer',
            'email' => 'ghjkghhj@hjk.com',
            'phone' => '0000000000',
        ]);

        $phone = '0000000001';

        $user = new User();

        $this->assertEquals(true, empty(User::checkIfUserExistsByPhoneNumber($phone)));

    }

    /**  @test */
    function that_if_subs_bid_has_been_accepted_and_the_job_has_been_approved_then_another_sub_has_been_accepted_then_the_first_sub_is_denied_and_the_newly_accepted_sub_finishes_with_status_of_job_has_been_approved_by_customer() {

        // GIVEN

//        need a general contractor
//        need a customer
//        need two subs
//        need a job
//        need one task for that job
//        need sub status for sub A to be accepted
//        need sub status for sub B to be denied
//        need job status to be approved
//        need job task status to be approved

        // ACTION

//        need the general to accept a bid for sub B


        // ASSERTION

//        need sub B status to be accepted
//        need sub B status to be approved_by_customer
//        need jobtask contractor to be sub B's user ID
//        need sub B to be notified that the bid was accepted

    }


}
