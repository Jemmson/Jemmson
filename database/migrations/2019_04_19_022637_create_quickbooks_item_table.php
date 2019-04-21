<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuickbooksItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quickbooks_item', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('fully_qualified_name')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('type')->nullable();
            $table->string('payment_method_ref')->nullable();
            $table->string('avg_cost')->nullable();
            $table->bigInteger('item_id');
            $table->bigInteger('contractor_id');
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
        Schema::dropIfExists('quickbooks_item');
    }
}
