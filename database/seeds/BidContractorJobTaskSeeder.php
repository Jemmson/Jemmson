<?php

use Illuminate\Database\Seeder;
use App\Contractor;
use App\Job;
use App\Task;

class BidContractorJobTaskSeeder extends Seeder
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

        DB::table('bid_contractor_job_task')->insert(
                        [
                            'contractor_id' => 2,
                            'job_task_id' => 1,
                            'bid_price' => rand(1, 100),
                        ]
                    );

        DB::table('bid_contractor_job_task')->insert(
                        [
                            'contractor_id' => 3,
                            'job_task_id' => 1,
                            'bid_price' => rand(1, 100),
                        ]
                    );

//         for ($i = 1; $i < 5; $i++) {
//             for ($k = 1; $k < 10; $k++) {
//                 for ($j = 1; $j < 4; $j++) {
//                     $contractor = Contractor::find($k);
//                     $job = Job::find($i);
//                     $task = Task::find($j);
// //                    DB::table("INSERT INTO contractor_job_task SET (contractor_id, job_id, task_id) VALUES ($contractor->id,$job->id,$task->id)");
//                     DB::table('bid_contractor_job_task')->insert(
//                         [
//                             'contractor_id' => $contractor->id,
//                             'job_id' => $job->id,
//                             'task_id' => $task->id,
//                             'bid_price' => rand(1, 1000),
//                             'accepted' => 0
//                         ]
//                     );
//                 }
//             }
//         }
    }
}
