<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractorTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // this table keeps track of the tasks that a contractor performs
        // and what their initial price is for these tasks. This price
        // will change to actual price based upon the negotiation the
        // contractor has with the customer
        Schema::create('contractor_task', function (Blueprint $table) {
            $table->integer('contractor_id');
            $table->integer('task_id');
            $table->bigInteger('base_price')->default(0);
            $table->primary(['contractor_id', 'task_id']);
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
        Schema::drop('contractor_task');
    }
}
