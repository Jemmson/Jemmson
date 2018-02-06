<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStripeExpressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stripe_expresses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contractor_id')->unsigned()->unique();

            $table->string('access_token')->nullable();
            $table->string('refresh_token')->nullable();
            $table->string('stripe_user_id')->nullable();
            $table->timestamps();

            $table->foreign('contractor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stripe_expresses');
    }
}