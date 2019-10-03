<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTokens extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
      Schema::create(
          'user_tokens', function (Blueprint $table) {
          $table->increments('id');
          $table->string('token');
          $table->integer('user_id')
              ->unsigned();
          $table->timestamp('created_at')
              ->nullable();
          $table->timestamp('expires_at')
              ->nullable();
          $table->softDeletes();
      }
      );
  }
  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
      Schema::dropIfExists('user_tokens');
  }
}
