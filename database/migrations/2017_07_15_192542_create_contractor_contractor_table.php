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
        // This table is meant to create an association between a particular
        // contractor and the sub contractors that the contractor is associated with.
        // a sub can be associated to more than one contractor and
        // a contractor can be associated with more than one sub.
        Schema::create('contractor_contractor', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contractor_id');
            $table->integer('subcontractor_id');
//            $table->primary(['contractor_id', 'subcontractor_id']);
            $table->softDeletes();
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
        Schema::drop('contractor_contractor');
    }
}
