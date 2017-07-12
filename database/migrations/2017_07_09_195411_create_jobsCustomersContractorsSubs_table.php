<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsCustomersContractorsSubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobCustomerContractorSub', function (Blueprint $table) {
            //
            $table->increments('id');
            $table->bigInteger('customerId');
            $table->bigInteger('contractorId');
            $table->bigInteger('jobId');
            $table->bigInteger('subContractorId');
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
        Schema::table('jobCustomerContractorSub', function (Blueprint $table) {
            //
        });
    }
}
