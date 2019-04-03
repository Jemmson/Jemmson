<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuickbooksContractorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quickbooks_contractor', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('quickbooks_id');
            $table->bigInteger('contractor_id');
            $table->bigInteger('sub_contractor_id');
            $table->string('given_name');
            $table->string('middle_name');
            $table->string('family_name');
            $table->string('fully_qualified_name');
            $table->string('primary_phone');
            $table->string('primary_email_addr');
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
        Schema::dropIfExists('quickbooks_contractor');
    }
}
