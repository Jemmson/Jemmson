<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStripeEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stripe_events', function (Blueprint $table) {
            $table->string('account_id')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('event_id');
            $table->string('event_type')->nullable();
            $table->json('event_payload');
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
        Schema::dropIfExists('account_updated');
    }
}
