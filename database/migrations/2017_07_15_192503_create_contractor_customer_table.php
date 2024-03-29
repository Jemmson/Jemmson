<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractorCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // this table references the fact that a contractor can have
        // more than one customer and a customer can have more than
        // one contractor.
        Schema::create('contractor_customer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contractor_user_id');
            $table->integer('customer_user_id');
            $table->string('customer_stripe_id')->nullable();
            $table->softDeletes();
            $table->bigInteger('quickbooks_id')->nullable();
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
        Schema::drop('contractor_customer');
    }
}
