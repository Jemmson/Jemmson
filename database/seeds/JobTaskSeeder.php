<?php

use Illuminate\Database\Seeder;
use App\Job;
use App\Task;
use App\JobTask;
use App\Contractor;
use Carbon\Carbon;

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

        $job = Job::find(1);
        $job->status = __('bid.in_progress');
        $job->save();

        $task = new JobTask();
        $task->task_id = 1;
        $task->job_id = 1;
        $task->contractor_id = 1;
        $task->cust_final_price = 100;
        $task->sub_final_price = rand(10, 50);
        $task->start_date = Carbon::now();
        $task->status = __('bid_task.bid_sent');
        $task->save();

//        $job = Job::find(2);
//        $job->status = __('bid.in_progress');
//        $job->save();
//
//        $task = new JobTask();
//        $task->task_id = 2;
//        $task->job_id = 2;
//        $task->contractor_id = 2;
//        $task->cust_final_price = 100;
//        $task->sub_final_price = rand(20, 50);
//        $task->start_date = Carbon::now();
//        $task->status = __('bid_task.bid_sent');
//        $task->save();



        // for ($j = 1; $j < 5; $j++) {
        //     $contractor = Contractor::find(1);
        //     for ($k = 1; $k < 5; $k++) {
        //         $job = Job::find($j);
        //         $job->status = config('app.bidIsInProgress');
        //         $job->save();
        //         $task = Task::find($k);
        //         $job->tasks()->attach($task);
        //         $task = $job->tasks()->find($task->id);
        //         $task->pivot->contractor_id = $contractor->id;
        //         $task->pivot->cust_final_price = rand(1, 1000);
        //         $task->pivot->sub_final_price = rand(1, 1000);
        //         $task->pivot->status = config('app.taskIsInitiated');
        //         $task->pivot->save();
        //     }
        // }

        // for ($j = 10; $j < 14; $j++) {
        //     $contractor = Contractor::find(2);
        //     for ($k = 5; $k < 10; $k++) {
        //         $job = Job::find($j);
        //         $job->status = config('app.bidIsInProgress');
        //         $job->save();
        //         $task = Task::find($k);
        //         $job->tasks()->attach($task);
        //         $task = $job->tasks()->find($task->id);
        //         $task->pivot->contractor_id = $contractor->id;
        //         $task->pivot->cust_final_price = rand(1, 1000);
        //         $task->pivot->sub_final_price = rand(1, 1000);
        //         $task->pivot->status = config('app.taskIsInitiated');
        //         $task->pivot->save();
        //     }
        // }

        // for ($j = 19; $j < 25; $j++) {
        //     $contractor = Contractor::find(3);
        //     for ($k = 10; $k < 15; $k++) {
        //         $job = Job::find($j);
        //         $job->status = config('app.bidIsInProgress');
        //         $job->save();
        //         $task = Task::find($k);
        //         $job->tasks()->attach($task);
        //         $task = $job->tasks()->find($task->id);
        //         $task->pivot->contractor_id = $contractor->id;
        //         $task->pivot->cust_final_price = rand(1, 1000);
        //         $task->pivot->sub_final_price = rand(1, 1000);
        //         $task->pivot->status = config('app.taskIsInitiated');
        //         $task->pivot->save();
        //     }
        // }

        // for ($j = 28; $j < 32; $j++) {
        //     $contractor = Contractor::find(4);
        //     for ($k = 15; $k < 20; $k++) {
        //         $job = Job::find($j);
        //         $job->status = config('app.bidIsInProgress');
        //         $job->save();
        //         $task = Task::find($k);
        //         $job->tasks()->attach($task);
        //         $task = $job->tasks()->find($task->id);
        //         $task->pivot->contractor_id = $contractor->id;
        //         $task->pivot->cust_final_price = rand(1, 1000);
        //         $task->pivot->sub_final_price = rand(1, 1000);
        //         $task->pivot->status = config('app.taskIsInitiated');
        //         $task->pivot->save();
        //     }
        // }
    }
}
