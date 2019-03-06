<?php

namespace Tests\Feature;

use App\Customer;
use App\Quickbook;
use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class QuickbookTest extends TestCase
{
    use DatabaseMigrations;

    /**  @test */
    function will_generate_csrf_token() {
        //
        $qb = new Quickbook();
        $guid = $qb->generateCsrf();
        $this->assertNotEquals('', $guid);
    }

    /**  @test */
    function will_save_generated_token_to_the_database() {
        //
        $qb = new Quickbook();
        $guid = $qb->generateCsrf();
        $qb->addGuidToTable($guid);
        $this->assertDatabaseHas('quickbook_csrf_tokens',[
           'guid' => $guid,
           'consumed' => false,
           'expired' => false
        ]);
    }

    /**  @test */
    function should_check_that_the_guid_that_was_returned_is_in_the_database() {
        //
        $qb = new Quickbook();
        $guid = $qb->generateCsrf();
        $qb->addGuidToTable($guid);
        $this->assertEquals(true, $qb->checkGuidIsInDb($guid));
    }

    /**  @test */
    function check_that_the_guid_was_created_within_tthe_last_5_minutes() {
        //
        $qb = new Quickbook();
        $guid = $qb->generateCsrf();
        $qb->addGuidToTable($guid);
        $this->assertEquals(true, $qb->checkGuidIsLessThan5MinutesOld($guid));
    }

    /**  @test */
    function check_that_the_guid_was_not_created_within_the_last_5_minutes() {
        //
        $qb = new Quickbook();
        $guid = $qb->generateCsrf();
        $qb->addGuidToTable($guid);

        $date = Carbon::now()->addMinutes(10);

        $statement = "Update quickbook_csrf_tokens set created_at = '".$date."' where guid = '".$guid."'";
        DB::select($statement);


        $this->assertEquals(false, $qb->checkGuidIsLessThan5MinutesOld($guid));
    }

    /**  @test */
    function the_csrf_token_should_be_consumed_if_the_guid_is_valid() {
        //
        $qb = new Quickbook();
        $guid = $qb->generateCsrf();
        $qb->addGuidToTable($guid);
        $qb->consumeToken($guid);

        $this->assertDatabaseHas('quickbook_csrf_tokens', [
           'guid' => $guid,
            'consumed' => 1
        ]);
    }
    
//    /**  @test */
    function check_that_the_token_has_not_expired() {
        // 
        
    }
    
//    /**  @test */
    function update_token_if_token_has_expired() {
        // 
        
    }
    
//    /**  @test */
    function create_a_data_service_object() {
        // 
        
    }
    
//    /**  @test */
    function create_an_estimate() {
        // 
        
    }
    
//    /**  @test */
    function query_a_customer_in_quickBooks() {
        // 

    }
    
//    /**  @test */
    function create_a_customer_in_quickBooks() {
        // 
        $customer = User::create([
            'name' => 'Shawn Pike',
//                    'email' => $email,
            'phone' => '4807034902',
            'usertype' => 'customer',
            'password_updated' => false,
            'password' => bcrypt('asdasd'),
        ]);

        $qb = new Quickbook();

        $resultObj = $qb->addCustomer($customer);

        var_dump($resultObj);

    }
    
//    /**  @test */
    function update_a_customer_in_quickbooks() {
        // 
        
    }
    
//    /**  @test */
    function delete_a_customer_in_quickBooks() {
        // 
        
    }

    /**  @test */
    function verify_guid_is_valid_in_the_db() {
        //
        $qb = new Quickbook();
        $guid = $qb->generateCsrf();
//        $date = Carbon::now();
//        $statement = "Insert into quickbook_csrf_tokens (created_at, guid) values ('".$date."', '".$guid."')";
//        DB::select($statement);
        $qb->addGuidToTable($guid);
        $this->assertEquals(true, $qb->checkIfGuidIsValid($guid));
    }

    /**  @test */
    function verify_guid_is_not_valid_in_the_db() {
        //
        $qb = new Quickbook();
        $guid = $qb->generateCsrf();
        $date = Carbon::now()->addMinutes(10);
        $statement = "Insert into quickbook_csrf_tokens (created_at, guid) values ('".$date."', '".$guid."')";
        DB::select($statement);
//        $qb->addGuidToTable($guid);
        $this->assertEquals(false, $qb->checkIfGuidIsValid($guid));
    }

//    /**  @test */
    function verify_guid_from_the_request() {
        //
        $qb = new Quickbook();
        $guid = $qb->generateCsrf();
        $request = "{ 'guid': $guid, 'method': 'getCompany' }";
        $requestGuid =  $qb->getGuidFromRequest($request);

        $this->assertEquals($guid, $requestGuid);
    }

}
