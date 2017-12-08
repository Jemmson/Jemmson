<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractorJobTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // this table refers to which contractor has which task for which job.
        // a job can have many contractors working on it.
        // each contractor can perform one or more tasks.
        // each job will have more than one task that will need to be performed.

        Schema::create('contractor_job_task', function (Blueprint $table) {
            $table->integer('contractor_id');
            $table->integer('job_id');
            $table->integer('task_id');
            $table->primary(['contractor_id', 'job_id', 'task_id']);
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
        Schema::drop('contractor_job_task');
    }
}
