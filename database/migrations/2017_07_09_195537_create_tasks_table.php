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
            $table->bigInteger('proposed_cust_price')->nullable();
            $table->bigInteger('average_cust_price')->nullable();
            $table->bigInteger('proposed_sub_price')->nullable();
            $table->bigInteger('average_sub_price')->nullable();
            $table->string('description')->nullable();
            $table->string('fully_qualified_name')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('type')->nullable();
            $table->string('payment_method_ref')->nullable();
            $table->string('avg_cost')->nullable();
            $table->bigInteger('item_id')->nullable();
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
