<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerDetailFieldJobTask extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_task', function (Blueprint $table) {
            $table->text('customer_message')->nullable();
            $table->text('sub_message')->nullable();
            $table->dropColumn('details');
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
            $table->dropColumn('customer_message');
            $table->dropColumn('sub_message');
            $table->text('details')->nullable();
        });
    }
}
