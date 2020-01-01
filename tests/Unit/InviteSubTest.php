<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\UtilitiesTrait;
use Tests\TestCase;
use Tests\Feature\Traits\UserTrait;
use App\User;

class InviteSubTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    use UtilitiesTrait;
    use UserTrait;

    /**  @test */
    function that_a_sub_exists_if_they_are_currently_in_the_database()
    {
        //
        $sub = $this->createContractor([
            "phone" => "1234567",
            "email" => "sub@sub.com",
        ]);

        $subFromDb = User::getContractorByPhone($sub->phone);

        $this->assertEquals($sub->phone, $subFromDb->phone);

    }

    /**  @test */
    function that_I_am_able_to_pull_back_only_one_sub_if_there_are_more_than_one_user_with_the_same_phone_but_only_one_of_them_is_a_contractor()
    {
        //
        $sub = $this->createContractor([
            "phone" => "1234567",
            "email" => "sub@sub.com",
        ]);
        $this->createCustomer([
            "phone" => "1234567",
            "email" => "cust@cust.com",
        ]);

        $subFromDb = User::getContractorByPhone($sub->phone);

        $this->assertEquals($sub->phone, $subFromDb->phone);
    }

    /**  @test */
    function that_null_is_returned_if_no_user_is_in_the_db()
    {
        //

        $subFromDb = User::getContractorByPhone('1237898');

        $this->assertNull($subFromDb);

    }

    /**  @test */
    function that_null_is_returned_if_the_number_exists_for_a_customer_but_not_a_sub()
    {
        //
        $this->createCustomer([
            "phone" => "1234567",
            "email" => "cust@cust.com",
        ]);

        $subFromDb = User::getContractorByPhone("1234567");

        $this->assertNull($subFromDb);
    }

    /**  @test */
    function that_there_are_two_contractors_with_the_same_mobile_phone_number_then_throw_DuplicateContractorException()
    {
        //
        $sub = $this->createContractor([
            "phone" => "1234567",
            "email" => "sub@sub.com",
        ]);
        //
        $this->createContractor([
            "phone" => "1234567",
            "email" => "sub1@sub1.com",
        ]);

        $subFromDb = User::getContractorByPhone($sub->phone);
        $this->assertEquals($sub->phone, $subFromDb->phone);

    }
}
