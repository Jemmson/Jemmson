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
}
