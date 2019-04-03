<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuickbookIdColumnToContractorCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contractor_customer', function (Blueprint $table) {
            //
            $table->bigInteger('quickbooks_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contractor_customer', function (Blueprint $table) {
            //
            $table->dropColumn('quickbooks_id');
        });
    }
}
