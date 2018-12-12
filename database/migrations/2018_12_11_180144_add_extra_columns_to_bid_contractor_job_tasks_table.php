<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraColumnsToBidContractorJobTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bid_contractor_job_task', function (Blueprint $table) {
            //
            $table->date('proposed_start_date')->nullable();
            $table->text('bid_description')->nullable();
            $table->boolean('accepted')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bid_contractor_job_task', function (Blueprint $table) {
            //
            $table->dropColumn('proposed_start_date');
            $table->dropColumn('bid_description');
            $table->dropColumn('accepted');
        });
    }
}
