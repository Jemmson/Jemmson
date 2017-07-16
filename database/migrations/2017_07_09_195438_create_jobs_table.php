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
        Schema::table('jobs', function (Blueprint $table) {
            //
            $table->increments('id');
            $table->bigInteger('customer_id');
            $table->bigInteger('contractor_id');
            $table->string('address_line_1');
            $table->string('address_line_2');
            $table->string('city');
            $table->string('state');
            $table->string('zip');
            $table->dateTime('initiated_bid_date');
            $table->dateTime('completed_bid');
            $table->dateTime('initiated_bid_date');
            $table->integer('bid_price');
            $table->integer('bid_price');
            $table->dateTime('agreed_start_date');
            $table->dateTime('agreed_end_date');
            $table->dateTime('actual_end_date');
            $table->dateTime('actual_end_date');
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
        Schema::table('jobs', function (Blueprint $table) {
            //
        });
    }
}
