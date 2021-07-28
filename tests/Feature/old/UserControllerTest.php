<?php

namespace Tests\Feature;

use App\Http\Controllers\UserController;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{

    public function test_that_I_am_able_to_pull_back_a_mobile_phone_number()
    {

        $number = User::validatePhoneNumber('4807034902');

        echo $number;

    }

}
