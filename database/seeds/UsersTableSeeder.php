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
        //         'password' => bcrypt('secret'),
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
        //         'password' => bcrypt('secret'),
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
                'email' => "shawn@example.com",
                'password' => bcrypt('secret'),
                'remember_token' => str_random(10),
                'phone' => '4901112222',
                'usertype' => 'contractor'
            ];
            $user = User::create($data);

            $contractorData = [
                'user_id' => $user->id,
                'email_method_of_contact' => '1',
                'company_logo_name' => $faker->word,
                'sms_method_of_contact' => '1',
                'phone_method_of_contact' => '1',
                'company_name' => $faker->word,
            ];

            Contractor::create($contractorData);

            $stripe = [
                'contractor_id' => 1,
                'access_token' => 'sk_test_vewQOBeHnMCFtgEjOLQmXrdD',
                'refresh_token' => 'rt_CMkeB75bV4BbR9v06ioFogUHLzoUSUkAhTyqwqV76CPlz0gi',
                'stripe_user_id' => 'acct_1By18sB4l1AzsWS0',
            ];

            StripeExpress::create($stripe);

            $data = [
                'name' => 'Daven Escobar',
                'email' => "daven@example.com",
                'password' => bcrypt('secret'),
                'remember_token' => str_random(10),
                'phone' => '4901113333',
                'usertype' => 'contractor'
            ];
            $user = User::create($data);

            $contractorData = [
                'user_id' => $user->id,
                'email_method_of_contact' => '1',
                'company_logo_name' => $faker->word,
                'sms_method_of_contact' => '1',
                'phone_method_of_contact' => '1',
                'company_name' => $faker->word,
            ];

            Contractor::create($contractorData);

            $stripe = [
                'contractor_id' => 2,
                'access_token' => 'sk_test_2DL5LXhimtvvVfbhZNOaEYOG',
                'refresh_token' => 'rt_CMkY01KB2aW0XM0Q2XCKw8fNbH8kI3y1EnqfJ2mQ8LzfbbgC',
                'stripe_user_id' => 'acct_1By13dFOSJzZ3wkC',
            ];

            StripeExpress::create($stripe);

            $data = [
                'name' => "John Doe",
                'email' => "john@example.com",
                'password' => bcrypt('secret'),
                'remember_token' => str_random(10),
                'phone' => '4901114444',
                'usertype' => 'customer'
            ];
            $user = User::create($data);

            $customerData = [
                'user_id' => $user->id,
                'email_method_of_contact' => '1',
                'notes' => $faker->paragraph,
                'phone_method_of_contact' => '1',
                'sms_method_of_contact' => '1',
            ];

            Customer::create($customerData);

            $data = [
                'name' => "Jane Doe",
                'email' => "jane@example.com",
                'password' => bcrypt('secret'),
                'remember_token' => str_random(10),
                'phone' => '4901115555',
                'usertype' => 'customer'
            ];
            $user = User::create($data);

            $customerData = [
                'user_id' => $user->id,
                'email_method_of_contact' => '1',
                'notes' => $faker->paragraph,
                'phone_method_of_contact' => '1',
                'sms_method_of_contact' => '1',
            ];

            Customer::create($customerData);
    }
}
