<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTasksContractorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobsTasksContractors', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('jobId');
            $table->bigInteger('taskId');
            $table->bigInteger('contractorId');
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
        Schema::table('jobsTasksContractors', function (Blueprint $table) {
            //
        });
    }
}
