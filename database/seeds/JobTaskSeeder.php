<?php

use Illuminate\Database\Seeder;
use App\Job;
use App\Task;

class JobTaskSeeder extends Seeder
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
            for ($j = 1; $j < 10; $j++) {
                $job = Job::find($i);
                $task = Task::find($j);
                $job->tasks()->attach($task);
                $task = $job->tasks()->find($task->id);
                $task->pivot->cust_final_price = rand(1, 1000);
                $task->pivot->sub_final_price = rand(1, 1000);
                $task->pivot->status = 'initiated';
                $task->pivot->save();
            }
        }
    }
}
