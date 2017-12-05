<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This table records all of the custom tasks that a
        // contractor enters. these tasks are associated with a
        // given job. also a standard task is associated with a
        // task
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('standard_task_id')->nullable();
            $table->integer('contractor_id')->nullable();
            $table->float('proposed_cust_price')->nullable();
            $table->float('average_cust_price')->nullable();
            $table->float('proposed_sub_price')->nullable();
            $table->float('average_sub_price')->nullable();
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
        Schema::drop('tasks');
    }
}
