<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class createBidContractorJobTaskTable extends Migration
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
            $table->integer('contractor_id')->unsigned();
            $table->integer('job_task_id')->unsigned();
            $table->bigInteger('bid_price')->nullable()->default(0.00);
            $table->string('status')->default('bid_task.initiated');
            $table->date('proposed_start_date')->nullable();
            $table->text('bid_description')->nullable();
            $table->boolean('accepted')->nullable()->default(0);
            $table->string('payment_type')->nullable();
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
        Schema::dropIfExists('bid_contractor_job_task');
    }
}
