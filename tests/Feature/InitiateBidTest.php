<?php

namespace Tests\Feature;

use App\Location;
use App\Quickbook;
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
    use Setup;

    public function testThatTheCorrectTokenComesBack()
    {

        $this->withoutExceptionHandling();

        $general = $this->createAUser('contractor', 1, 1, [], [
            'company_name' => 'Albertsons',
            'free_jobs' => 5
        ]);

        $response = $this->actingAs($general)->json('POST', '/initiate-bid',
            [
                'customerName' => 'karen willis',
                'email' => $this->faker->email,
                'firstName' => 'karen',
                'lastName' => 'willis',
                'jobName' => 'pool work',
                'phone' => '4807034902',
                'quickbooks_id' => '',
            ]);

        $response->assertSeeText('Bid was created');

//        Contractor can create new a new job
        $this->assertDatabaseHas('contractors', [
            "user_id" => $general->id
        ]);

//        dd(json_encode($general->contractor()));
//        echo json_encode($general->contractor());

        $this->assertGreaterThan(0, $general->contractor()->get()->first()->free_jobs);

//        Check a customer has been added to the database
        $newCustomer = User::where('name', '=', 'karen willis')->get()->first();

        $this->assertDatabaseHas('users', [
            "id" => $newCustomer->id,
            "location_id" => null,
            "usertype" => "customer",
            "password_updated" => 0,
            'phone' => '4807034902'
        ]);

        $this->assertDatabaseHas('customers', [
            "user_id" => $newCustomer->id
        ]);

//**    Check that customer and contractor are associated to each other in the database
        $this->assertDatabaseHas('contractor_customer', [
            "contractor_user_id" => $general->id,
            "customer_user_id" => $newCustomer->id
        ]);


//      Check that a job has been created
        $this->assertDatabaseHas('jobs', [
                'contractor_id' => $general->id,
                'customer_id' => $newCustomer->id,
                'job_name' => "pool work",
                'status' => "bid.initiated",
                'location_id' => null
        ]);

//      Check the customer has been notified
        $this->assertDatabaseHas('user_tokens', [
            "user_id" => $newCustomer->id
        ]);

        $this->assertDatabaseHas('customers', [
            "user_id" => $newCustomer->id,
            "location_id" => null,
            "email_method_of_contact" => null,
            "phone_method_of_contact" => null,
            "sms_method_of_contact" => null,
            "notes" => null
        ]);

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


//    public function test_that_when_a_customer_is_added_and_the_contractor_uses_qb_the_right_fields_in_the_database_are_created()
//    {
//
//        $contractor = $this->addingAContractor();
//
//        $qb = new Quickbook();
//        $qb->user_id = $contractor->id;
//        $qb->company_id = '123146242868259';
//        $qb->refresh_token = 'AB11571185621YRpVrLwAM5cCQZKCc3lif0qZmCKdG3CyODYf3';
//        $qb->refresh_token_expires_at = 1571185621;
//        $qb->refresh_token_validation_period = 8656222;
//        $qb->save();
//
//        $response = $this->actingAs($contractor)->json('POST', '/initiate-bid',
//            [
//                'customerName' => 'karen willis',
//                'email' => $this->faker->email,
//                'firstName' => 'karen',
//                'lastName' => 'willis',
//                'jobName' => 'pool work',
//                'phone' => '4807034902',
//                'quickbooks_id' => '',
//            ]);
//
//        $this->assertEquals('Bid was created', $response->content());
//
//        $this->assertDatabaseHas('users', [
//            'name' => 'karen willis',
//            'first_name' => 'karen',
//            'last_name' => 'willis',
//            'password_updated' => 0,
//            'phone' => '4807034902',
//        ]);
//
//        $user = User::select()->where('name', '=', 'karen willis')->get()->first();
//
//        $this->assertDatabaseHas('customers', [
//            'user_id' => $user->id
//        ]);
//
//        $this->assertDatabaseHas('contractor_customer', [
//            'contractor_user_id' => $contractor->id,
//            'customer_user_id' => $user->id
//        ]);
//
//        $cc = ContractorCustomer::where([
//            'contractor_user_id' => $contractor->id,
//            'customer_user_id' => $user->id
//        ])->get()->first();
//
//        $this->assertEmpty(false, empty($cc->quickbooks_id));
//
//        $this->assertDatabaseHas('jobs', [
//            'customer_id' => $user->id,
//            'contractor_id' => $contractor->id,
//            'job_name' => 'pool work'
//        ]);
//
//    }

    private function addingAContractor()
    {
        $contractor = factory(User::class)->create([
            'password_updated' => 1,
            'usertype' => 'contractor',
        ]);

        $location = factory(Location::class)->create();

        $cont = factory(Contractor::class)->create([
            'location_id' => $location->id,
            'user_id' => $contractor->id,
            'accounting_software' => 'quickBooks'
        ]);

        $contractor->location_id = $location->id;
        $contractor->save();

        return $contractor;
    }
}
