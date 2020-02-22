<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_group', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('general_id');
            $table->string('general_amount')->nullable();
            $table->string('sub_amount')->nullable();
            $table->string('jemmson_amount');
            $table->string('stripe_amount')->nullable();
            $table->bigInteger('job_id');
            $table->bigInteger('job_task_id')->nullable();
            $table->bigInteger('sub_id')->nullable();
            $table->bigInteger('customer_id');
            $table->string('transfer_group_guid');
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
        Schema::dropIfExists('transfer_group');
    }
}
