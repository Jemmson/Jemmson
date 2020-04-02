<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStripeUploadedFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stripe_uploaded_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_id');
            $table->string('file_id')->nullable();
            $table->string('filename')->nullable();
            $table->string('links_url')->nullable();
            $table->string('purpose')->nullable();
            $table->bigInteger('size')->nullable();
            $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->string('url')->nullable();
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
        Schema::dropIfExists('stripe_uploaded_files');
    }
}
