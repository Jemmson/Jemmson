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
            $table->integer('bid_id')->unsigned()->nullable();
            $table->integer('contractor_id')->nullable();
            $table->bigInteger('cust_final_price')->unsigned()->nullable();
            $table->text('customer_message')->nullable();
            $table->text('declined_message')->nullable();
            $table->integer('job_id')->unsigned();
            $table->integer('location_id')->unsigned()->nullable();
            $table->string('paid_with_cash_message')->nullable();
            $table->tinyInteger('qty')->unsigned()->default(1);
            $table->timestamp('start_date')->nullable();
            $table->boolean('start_when_accepted')->default(false);
            $table->string('status')->nullable();
            $table->boolean('stripe')->default(false);
            $table->string('stripe_transfer_id')->nullable();
            $table->bigInteger('sub_final_price')->unsigned()->nullable();
            $table->text('sub_message')->nullable();
            $table->boolean('sub_sets_own_price_for_job')->default(1);
            $table->integer('task_id')->unsigned();
            $table->bigInteger('unit_price')->unsigned()->default(0);
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
