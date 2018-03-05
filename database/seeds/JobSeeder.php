<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Contractor;
use App\Customer;
use App\Job;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $k = 0;
        $faker = Faker\Factory::create();
        $data = [
                    'status' => "bid.in_progress",
                    'customer_id' => 3,
                    'contractor_id' => 1,
                    'completed_bid_date' => $faker->dateTime,
                    'bid_price' => 100,
                    'agreed_start_date' => $faker->dateTime,
                    'agreed_end_date' => $faker->dateTime,
                    'actual_end_date' => $faker->dateTime,
                    'job_name' => 'Pool',
        ];
        Job::create($data);


        $data = [
                    'status' => "bid.in_progress",
                    'customer_id' => 4,
                    'contractor_id' => 2,
                    'completed_bid_date' => $faker->dateTime,
                    'bid_price' => 100,
                    'agreed_start_date' => $faker->dateTime,
                    'agreed_end_date' => $faker->dateTime,
                    'actual_end_date' => $faker->dateTime,
                    'job_name' => 'Bathroom remodel',
        ];
        Job::create($data);
        // for ($i = 1; $i < 5; $i++) {
        //     for ($j = 1; $j < 10; $j++) {
        //         $contractor = Contractor::find($i);
        //         $customer = Customer::find($j);
        //         $data = [
        //             'id' => ++$k,
        //             'status' => "initiated",
        //             'customer_id' => $customer->id,
        //             'contractor_id' => $contractor->id,
        //             'address_line_1' => $faker->streetAddress,
        //             'address_line_2' => $faker->word,
        //             'city' => $faker->city,
        //             'state' => 'AZ',
        //             'zip' => $faker->postcode,
        //             'completed_bid_date' => $faker->dateTime,
        //             'bid_price' => rand(10, 100) . "." . rand(10, 99),
        //             'agreed_start_date' => $faker->dateTime,
        //             'agreed_end_date' => $faker->dateTime,
        //             'actual_end_date' => $faker->dateTime,
        //             'job_name' => $faker->company,
        //         ];
        //         Job::create($data);
        //     }
        // }
    }
}
