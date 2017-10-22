<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // this table keeps track of all of the users that are contractors
        Schema::create('contractors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unique(); 
            $table->string('email_method_of_contact')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('company_logo_name')->nullable();
            $table->string('sms_method_of_contact')->nullable();
            $table->string('phone_method_of_contact')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('company_name')->nullable();
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
        Schema::drop('contractors');
    }
}
