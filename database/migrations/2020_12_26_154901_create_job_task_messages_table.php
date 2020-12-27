<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobTaskMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_task_messages', function (Blueprint $table) {
            $table->id();
            $table->integer('job_task_id');
            $table->integer('user_id');
            $table->string('message');
            $table->string('username');
            $table->boolean('for_customer');
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
        Schema::dropIfExists('job_task_messages');
    }
}
