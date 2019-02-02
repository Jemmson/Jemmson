<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuickBooksAccessTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quick_books_access_token', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id');
            $table->text('access_token_key');
            $table->string('token_type');
            $table->string('refresh_token');
            $table->bigInteger('access_token_expires_at');
            $table->bigInteger('refresh_token_expires_at');
            $table->bigInteger('access_token_validation_period');
            $table->bigInteger('refresh_token_validation_period');
            $table->string('client_id');
            $table->string('client_secret');
            $table->string('realm_id');
            $table->string('base_url');
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
        Schema::dropIfExists('_quick_books_access_token');
    }
}
