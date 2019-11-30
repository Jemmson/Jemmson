<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This table keeps track of all of the jobs and each job
        // has one customer and one or more contractors
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('agreed_end_date')->nullable();
            $table->dateTime('agreed_start_date')->nullable();
            $table->dateTime('actual_end_date')->nullable();
            $table->bigInteger('bid_price')->default(0.00);
            $table->dateTime('completed_bid_date')->nullable();
            $table->bigInteger('contractor_id');
            $table->bigInteger('customer_id');
            $table->text('declined_message')->nullable();
            $table->string('job_name');
            $table->integer('location_id')->usigned()->nullable();
            $table->string('paid_with_cash_message')->nullable();
            $table->string('qb_estimate_id')->default('NULL');
            $table->string('status')->nullable();
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
        Schema::drop('jobs');
    }
}
