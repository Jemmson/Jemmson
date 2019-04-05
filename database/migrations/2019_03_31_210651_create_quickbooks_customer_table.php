<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuickbooksCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quickbooks_customer', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('quickbooks_id');
            $table->bigInteger('contractor_id');
            $table->bigInteger('customer_id');
            $table->string('given_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('family_name')->nullable();
            $table->string('fully_qualified_name')->nullable();
            $table->string('primary_phone')->nullable();
            $table->string('primary_email_addr')->nullable();
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
        Schema::dropIfExists('quickbooks_customer');
    }
}
