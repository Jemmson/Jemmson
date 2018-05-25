<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubAddsOwnPriceColumnToJobTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_task', function (Blueprint $table) {
            $table->boolean('sub_sets_own_price_for_job')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_task', function (Blueprint $table) {
            //
            $table->boolean('sub_sets_own_price_for_job')->default(1);
        });
    }
}
