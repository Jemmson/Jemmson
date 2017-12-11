<?php

use Illuminate\Database\Seeder;
use App\Contractor;
use App\Job;
use App\Task;
use Illuminate\Support\Facades\DB;

class ContractorJobTask extends Seeder
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
        for ($i = 1; $i < 5; $i++) {
            for ($k = 1; $k < 10; $k++) {
                for ($j = 1; $j < 4; $j++) {
                    $contractor = Contractor::find($k);
                    $job = Job::find($i);
                    $task = Task::find($j);
//                    DB::table("INSERT INTO contractor_job_task SET (contractor_id, job_id, task_id) VALUES ($contractor->id,$job->id,$task->id)");
                    DB::table('contractor_job_task')->insert(
                        [
                            'contractor_id' => $contractor->id,
                            'job_id' => $job->id,
                            'task_id' => $task->id,
                        ]
                    );
                }
            }
        }
    }
}
