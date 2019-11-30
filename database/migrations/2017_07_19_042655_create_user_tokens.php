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
          $table->integer('job_id')->unsigned()->nullable();
          $table->integer('user_id')->unsigned();
          $table->timestamp('expires_at')->nullable();
          $table->string('job_step')->nullable();
          $table->string('job_task_step')->nullable();
          $table->string('sub_step')->nullable();
          $table->string('token');
          $table->string('type')->nullable();
          $table->timestamps();
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
