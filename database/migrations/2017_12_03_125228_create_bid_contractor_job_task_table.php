<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractorBidTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bid_contractor_job_task', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contractor_id');
            $table->integer('job_id');
            $table->integer('task_id');
            $table->float('bid_price')->nullable()->default(0.00);
            $table->boolean('accepted')->nullable()->default(0);
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
        Schema::dropIfExists('bid_contractor_job_task');
    }
}
