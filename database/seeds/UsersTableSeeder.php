<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Contractor;
use App\Customer;
use App\StripeExpress;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $faker = Faker\Factory::create();
        // foreach (range(1,50) as $index) {
        //     $data = [
        //         'name' => $faker->name,
        //         'email' => $faker->unique()->safeEmail,
        //         'password' => bcrypt('asdasd'),
        //         'remember_token' => str_random(10),
        //         'usertype' => 'contractor'
        //     ];
        //     $user = User::create($data);

        //     $contractorData = [
        //         'user_id' => $user->id,
        //         'email_method_of_contact' => '1',
        //         'address_line_1' => $faker->streetAddress,
        //         'address_line_2' => $faker->word,
        //         'city' => $faker->city,
        //         'state' => 'AZ',
        //         'zip' => $faker->postcode,
        //         'company_logo_name' => $faker->word,
        //         'sms_method_of_contact' => '1',
        //         'phone_method_of_contact' => '1',
        //         'phone_number' => $faker->phoneNumber,
        //         'company_name' => $faker->word,
        //     ];

        //     Contractor::create($contractorData);

        //     $data = [
        //         'name' => $faker->name,
        //         'email' => $faker->unique()->safeEmail,
        //         'password' => bcrypt('asdasd'),
        //         'remember_token' => str_random(10),
        //         'usertype' => 'customer'
        //     ];
        //     $user = User::create($data);

        //     $customerData = [
        //         'user_id' => $user->id,
        //         'email_method_of_contact' => '1',
        //         'address_line_1' => $faker->streetAddress,
        //         'address_line_2' => $faker->word,
        //         'city' => $faker->city,
        //         'state' => 'AZ',
        //         'zip' => $faker->postcode,
        //         'notes' => $faker->paragraph,
        //         'phone_method_of_contact' => '1',
        //         'sms_method_of_contact' => '1',
        //         'phone_number' => $faker->phoneNumber,
        //     ];

        //     Customer::create($customerData);
        // }

        $data = [
            'name' => 'Shawn Pike',
            'email' => "jemmsoninc@gmail.com",
            'password' => bcrypt('asdasd'),
            'remember_token' => str_random(10),
            'phone' => '4806226441',
            'usertype' => 'contractor',
            'location_id' => 1
        ];
        $user = User::create($data);

        $contractorData = [
            'user_id' => $user->id,
            'email_method_of_contact' => '1',
            'company_logo_name' => $faker->word,
            'sms_method_of_contact' => '1',
            'phone_method_of_contact' => '1',
            'company_name' => "Jemmson",
            'location_id' => 1
        ];

        Contractor::create($contractorData);

        $stripe = [
            'contractor_id' => 1,
            'access_token' => 'sk_test_vewQOBeHnMCFtgEjOLQmXrdD',
            'refresh_token' => 'rt_CMkeB75bV4BbR9v06ioFogUHLzoUSUkAhTyqwqV76CPlz0gi',
            'stripe_user_id' => 'acct_1By18sB4l1AzsWS0',
        ];

        StripeExpress::create($stripe);


        //--------------------------------------------------------------

        $data = [
            'name' => 'Susan Franchuk',
            'email' => "sfranchuk@cox.net",
            'password' => bcrypt('asdasd'),
            'remember_token' => str_random(10),
            'phone' => '6024326933',
            'usertype' => 'contractor',
            'location_id' => 2
        ];
        $user = User::create($data);

        $contractorData = [
            'user_id' => $user->id,
            'email_method_of_contact' => '1',
            'company_logo_name' => $faker->word,
            'sms_method_of_contact' => '1',
            'phone_method_of_contact' => '1',
            'company_name' => "Sew Fun",
            'location_id' => 2
        ];

        Contractor::create($contractorData);

        $stripe = [
            'contractor_id' => 2,
            'access_token' => 'sk_test_vewQOBeHnMCFtgEjOLQmXrdD',
            'refresh_token' => 'rt_CMkeB75bV4BbR9v06ioFogUHLzoUSUkAhTyqwqV76CPlz0gi',
            'stripe_user_id' => 'acct_1By18sB4l1AzsWS0',
        ];

        StripeExpress::create($stripe);


        //--------------------------------------------------------------

        $data = [
            'name' => 'Shawn Pike',
            'email' => "pike.shawn@gmail.com",
            'password' => bcrypt('asdasd'),
            'remember_token' => str_random(10),
            'phone' => '4807034902',
            'usertype' => 'contractor',
            'location_id' => 3
        ];
        $user = User::create($data);

        $contractorData = [
            'user_id' => $user->id,
            'email_method_of_contact' => '1',
            'company_logo_name' => $faker->word,
            'sms_method_of_contact' => '1',
            'phone_method_of_contact' => '1',
            'company_name' => "KPS Pools",
            'location_id' => 3
        ];

        Contractor::create($contractorData);

        $stripe = [
            'contractor_id' => 3,
            'access_token' => 'sk_test_2DL5LXhimtvvVfbhZNOaEYOG',
            'refresh_token' => 'rt_CMkY01KB2aW0XM0Q2XCKw8fNbH8kI3y1EnqfJ2mQ8LzfbbgC',
            'stripe_user_id' => 'acct_1By13dFOSJzZ3wkC',
        ];

        StripeExpress::create($stripe);


        //--------------------------------------------------------------

        $data = [
            'name' => "Kristen Battafarano",
            'email' => "kbattafarano@gmail.com",
            'password' => bcrypt('asdasd'),
            'remember_token' => str_random(10),
            'phone' => '6023508801',
            'usertype' => 'customer',
            'location_id' => 4
        ];
        $user = User::create($data);

        $customerData = [
            'user_id' => $user->id,
            'email_method_of_contact' => '1',
            'notes' => $faker->paragraph,
            'phone_method_of_contact' => '1',
            'sms_method_of_contact' => '1',
            'location_id' => 4
        ];

        Customer::create($customerData);



        //--------------------------------------------------------------


        $data = [
            'name' => "Jane Doe",
            'email' => "jane@example.com",
            'password' => bcrypt('asdasd'),
            'remember_token' => str_random(10),
            'phone' => '4901115555',
            'usertype' => 'customer',
            'location_id' => 5
        ];
        $user = User::create($data);

        $customerData = [
            'user_id' => $user->id,
            'email_method_of_contact' => '1',
            'notes' => $faker->paragraph,
            'phone_method_of_contact' => '1',
            'sms_method_of_contact' => '1',
            'location_id' => 5
        ];

        Customer::create($customerData);
    }
}
