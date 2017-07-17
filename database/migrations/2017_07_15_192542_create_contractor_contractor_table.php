<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractorContractorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('contractor_contractor', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contractor_id');
            $table->integer('subcontractor_id');
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
        Schema::table('contractor_contractor', function (Blueprint $table) {
            //
        });
    }
}
