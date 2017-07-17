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
        // This table represents the dom elements on a given page.
        // this table is used to understand user analytics to improve
        // the application
        Schema::create('elements', function (Blueprint $table) {
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
        Schema::down('elements', function (Blueprint $table) {
            //
        });
    }
}
