<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Contractor;
use App\Customer;

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
        foreach (range(1,50) as $index) {
            $data = [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('secret'),
                'remember_token' => str_random(10),
                'usertype' => 'contractor'
            ];
            $user = User::create($data);

            $contractorData = [
                'user_id' => $user->id,
                'email_method_of_contact' => 'on',
                'address_line_1' => $faker->streetAddress,
                'address_line_2' => $faker->word,
                'city' => $faker->city,
                'state' => 'AZ',
                'zip' => $faker->postcode,
                'company_logo_name' => $faker->word,
                'sms_method_of_contact' => 'on',
                'phone_method_of_contact' => 'on',
                'phone_number' => $faker->phoneNumber,
                'company_name' => $faker->word,
            ];

            Contractor::create($contractorData);

            $data = [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('secret'),
                'remember_token' => str_random(10),
                'usertype' => 'customer'
            ];
            $user = User::create($data);

            $customerData = [
                'user_id' => $user->id,
                'email_method_of_contact' => 'on',
                'address_line_1' => $faker->streetAddress,
                'address_line_2' => $faker->word,
                'city' => $faker->city,
                'state' => 'AZ',
                'zip' => $faker->postcode,
                'notes' => $faker->paragraph,
                'phone_method_of_contact' => 'on',
                'sms_method_of_contact' => 'on',
                'phone_number' => $faker->phoneNumber,
            ];

            Customer::create($customerData);
        }
    }
}
