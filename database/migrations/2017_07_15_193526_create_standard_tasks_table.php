<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStandardTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This table is used to create names for tasks that will be
        // standard so that the contractor will be less likely in enter
        // a custom task. this will help with find cross user statistics
        Schema::create('standardTasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('task_name');
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
        Schema::drop('standardTasks');
    }
}
