<?php

use Illuminate\Database\Seeder;
use App\Location;


class LocationSeeder extends Seeder
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

        $location = [
            'user_id' => 1,
            'address_line_1' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'zip' => $faker->postcode,

        ];

        Location::create($location);

        $location = [
            'user_id' => 2,
            'address_line_1' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'zip' => $faker->postcode,

        ];

        Location::create($location);

        $location = [
            'user_id' => 3,
            'address_line_1' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'zip' => $faker->postcode,

        ];

        Location::create($location);

        $location = [
            'user_id' => 4,
            'address_line_1' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'zip' => $faker->postcode,

        ];

        Location::create($location);

        $location = [
            'user_id' => 5,
            'address_line_1' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'zip' => $faker->postcode,

        ];

        Location::create($location);



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
