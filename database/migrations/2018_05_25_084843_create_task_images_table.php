<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id')->unsigned();
            $table->integer('job_task_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('public_id')->nullable(true);
            $table->integer('version')->nullable(true);
            $table->string('signature')->nullable(true);
            $table->integer('width')->nullable(true);
            $table->integer('height')->nullable(true);
            $table->string('format')->nullable(true);
            $table->string('resource_type')->nullable(true);
            $table->integer('bytes')->nullable(true);
            $table->string('type')->nullable(true);
            $table->string('etag')->nullable(true);
            $table->boolean('placeholder')->nullable(true);
            $table->string('url')->nullable(true);
            $table->string('secure_url')->nullable(true);
            $table->boolean('overwritten')->nullable(true);
            $table->string('original_filename')->nullable(true);
            $table->softDeletes();
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
        Schema::dropIfExists('task_images');
    }
}
