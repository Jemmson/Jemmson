<?php
//
//namespace Tests\Feature;
//
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithoutMiddleware;
//use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Illuminate\Foundation\Testing\DatabaseTransactions;
//use Carbon\Carbon;
//
//class ApiTest extends TestCase
//{
//    /**
//     * Test job actions
//     *
//     * @return void
//     */
//    public function test_job_accept()
//    {
//        $response =$this->json('POST',
//                               'api/job/action',
//                               ['job_id' => 1, 'action' => 'accept']);
//        $response->assertJson(['job_accepted' => true]);
//    }
//
//    /**
//     * Test job actions
//     *
//     * @return void
//     */
//    public function test_job_approved()
//    {
//        $response =$this->json('POST',
//                               'api/job/action',
//                               ['job_id' => 1, 'action' => 'approve']);
//        $response->assertJson(['job_approved' => true]);
//    }
//
//    /**
//     * Test job actions
//     *
//     * @return void
//     */
//    public function test_job_decline()
//    {
//        $response =$this->json('POST',
//                               'api/job/action',
//                               ['job_id' => 1, 'action' => 'decline']);
//        $response->assertJson(['job_declined' => true]);
//    }
//
//    /**
//     * Create job
//     *
//     * @return void
//     */
//    public function test_job_create()
//    {
//        $job_name = 'Test Job # ' . rand(0,9999);
//        $response = $this->json('POST',
//                               'api/job',
//                               ['customer_id' => 1,
//                               'contractor_id' => 2,
//                               'job_name' => $job_name,
//                               'address_line_1' => '7834 W Hollywood Blvd',
//                               'city' => 'phoenix',
//                               'state' => 'AZ',
//                               'zip' => '86023',
//                               'bid_price' => '9.93',
//                               'agreed_start_date' => Carbon::now()->toDateTimeString(),
//                               'agreed_end_date' => Carbon::now()->toDateTimeString()]);
//
//        $this->assertDatabaseHas('jobs', ['job_name' => $job_name]);
//    }
//
//    /**
//     * Create job
//     *
//     * @return void
//     */
//    public function test_job_create_fail()
//    {
//        $job_name = 'Test Job # ' . rand(0,9999);
//        $response = $this->json('POST',
//                               'api/job',
//                               ['customer_id' => 1,
//                               'contractor_id' => 2,
//                               'job_name' => $job_name,
//                               'address_line_1' => '7834 W Hollywood Blvd',
//                               'city' => 'phoenix',
//                               'state' => 'AZ',
//                               'zip' => '86023']);
//        $response->assertJson(['error' => 'Missing fields']);
//    }
//
//}
