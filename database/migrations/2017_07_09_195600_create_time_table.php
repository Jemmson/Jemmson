<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This table is meant to record the time that a contractor
        // spends on each particular task
        Schema::create('time', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contractor_id');
            $table->integer('job_id');
            $table->integer('task_id');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
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
        Schema::drop('time', function (Blueprint $table) {
            //
        });
    }
}
