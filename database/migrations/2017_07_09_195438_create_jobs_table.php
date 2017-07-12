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
            $table->bigInteger('customerId');
            $table->bigInteger('contractorId');
            $table->string('addressLine1');
            $table->string('addressLine2');
            $table->string('city');
            $table->string('state');
            $table->string('zip');
            $table->dateTime('initiatedBidDate');
            $table->dateTime('completedBid');
            $table->dateTime('initiatedBidDate');
            $table->dateTime('agreedStartDate');
            $table->dateTime('agreedEndDate');
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
