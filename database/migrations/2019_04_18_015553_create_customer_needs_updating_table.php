<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerNeedsUpdatingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_needs_updating', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('contractor_id');
            $table->bigInteger('customer_id');
            $table->bigInteger('quickbooks_id');
            $table->boolean('needs_updating')->default(false);
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
        Schema::dropIfExists('customer_needs_updating');
    }
}
