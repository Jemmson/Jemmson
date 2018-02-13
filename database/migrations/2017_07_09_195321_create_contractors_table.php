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
            $table->integer('location_id')->unique(); 
            
            $table->string('company_name')->nullable();
            $table->string('company_logo_name')->nullable();

            $table->boolean('email_method_of_contact')->nullable();
            $table->boolean('sms_method_of_contact')->nullable();
            $table->boolean('phone_method_of_contact')->nullable();
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
