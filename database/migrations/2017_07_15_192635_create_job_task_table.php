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
        // particular job. the contractor id is the id of the contractor that will be performing the
        // particular job.
        Schema::create('job_task', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id')->unsigned();
            $table->integer('task_id')->unsigned();
            $table->integer('bid_id')->unsigned()->nullable();
            $table->integer('location_id')->unsigned()->nullable();
            
            $table->integer('contractor_id')->nullable();
            $table->string('status')->nullable();
            $table->text('details')->nullable();
            $table->float('cust_final_price')->unsigned()->nullable();
            $table->float('sub_final_price')->unsigned()->nullable();
            $table->boolean('start_when_accepted')->default(false);
            $table->timestamp('start_date')->nullable();
            
            $table->foreign('job_id')->references('id')->on('jobs');
            $table->foreign('task_id')->references('id')->on('tasks');
            
            $table->softDeletes();
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
