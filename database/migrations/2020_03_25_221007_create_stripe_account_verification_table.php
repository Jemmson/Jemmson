<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStripeAccountVerificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stripe_account_verification', function (Blueprint $table) {
            $table->string('account_id');
            $table->date('current_deadline')->nullable();
            $table->json('currently_due')->nullable();
            $table->string('disabled_reason')->nullable();
            $table->json('errors')->nullable();
            $table->json('eventually_due')->nullable();
            $table->json('past_due')->nullable();
            $table->json('pending_verification')->nullable();
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
        Schema::dropIfExists('stripe_account_verification');
    }
}
