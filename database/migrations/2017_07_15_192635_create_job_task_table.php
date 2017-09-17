<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // These are the tasks that are associated to a particular job.
        // A job has many tasks and a particular task can be associated to a
        // particular job.
        Schema::create('job_task', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id');
            $table->integer('task_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::drop('job_task');
    }
}
