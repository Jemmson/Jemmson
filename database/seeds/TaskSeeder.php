<?php

use Illuminate\Database\Seeder;
use App\Contractor;
use App\Task;

class TaskSeeder extends Seeder
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
        $data = [
                    'name' => 'Fix Water Filter',
                    'standard_task_id' => rand(1, 4),
                    'contractor_id' => 1,
                    'proposed_cust_price' => rand(1, 1000),
                    'average_cust_price' => rand(1, 1000),
                    'proposed_sub_price' => rand(1, 1000),
                    'average_sub_price' => rand(1, 1000),
        ];
        Task::create($data);

        $data = [
                    'name' => 'Fix Toilet',
                    'standard_task_id' => rand(1, 4),
                    'contractor_id' => 2,
                    'proposed_cust_price' => rand(1, 1000),
                    'average_cust_price' => rand(1, 1000),
                    'proposed_sub_price' => rand(1, 1000),
                    'average_sub_price' => rand(1, 1000),
        ];
        Task::create($data);
        // for ($i = 1; $i < 5; $i++) {
        //     for ($j = 1; $j < 10; $j++) {
        //         $contractor = Contractor::find($i);
        //         $data = [
        //             'name' => $faker->name,
        //             'standard_task_id' => rand(1, 1000),
        //             'contractor_id' => $contractor->id,
        //             'proposed_cust_price' => rand(1, 1000),
        //             'average_cust_price' => rand(1, 1000),
        //             'proposed_sub_price' => rand(1, 1000),
        //             'average_sub_price' => rand(1, 1000),
        //         ];
        //         Task::create($data);
        //     }
        // }
    }
}
