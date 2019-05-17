<?php

namespace Tests\Feature;

use App\Classes\Quickbooks\FakeQuickbooksGateway;
use App\Classes\Quickbooks\QuickbooksGateway;
use App\Quickbook;
use Tests\TestCase;
use App\User;
use App\Contractor;
use App\QuickbooksContractor;
use App\Customer;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class QuickbooksContractorTest extends TestCase
{

    use DatabaseMigrations;
    
    /**  @test */
    function Quickbooks_contractor_should_exist_in_quickbooks_table() {

        $this->withExceptionHandling();

        factory(QuickbooksContractor::class)->create([
            'given_name' => 'Sasha',
            'family_name' => 'Piraboo',
            'primary_phone' => '4807559000',
            'primary_email_addr' => 'spiraboo@freeman.com',
            'company_name' => 'Freeman Sporting Goods'
        ]);

        $email = 'spiraboo@freeman.com';
        $phone = '4807559000';
        $givenName = 'Sasha';
        $familyName = 'Piraboo';
        $companyName = 'Freeman Sporting Goods';

        $this->assertEquals(true, QuickbooksContractor::ContractorExists(
            $email,
            $phone,
            $givenName,
            $familyName,
            $companyName
        ));
    }

    /**  @test */
    function split_name_return_name_array() {
        // 


    }
}
