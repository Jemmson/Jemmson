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
            $table->bigInteger('user_id');
            $table->boolean('sms_text');
            $table->string('preferred_method_of_contact');
            $table->string('address_line_1');
            $table->string('address_line_2');
            $table->string('city');
            $table->string('state');
            $table->string('zip');
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
        Schema::drop('contractors', function (Blueprint $table) {
            //
        });
    }
}
