<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('elements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('element_id_name');
            $table->integer('page_id');
            $table->integer('user_id');
            $table->dateTime('time_entered');
            $table->dateTime('time_left');
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
        Schema::table('elements', function (Blueprint $table) {
            //
        });
    }
}
