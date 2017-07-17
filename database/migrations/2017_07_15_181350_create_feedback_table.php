<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This table is used to record the feedback of the contractors
        // and customers to improve the site.
        Schema::create('feedback', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->text('comment');
            $table->integer('page_id');
            $table->integer('page_from_id');
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
        //
        Schema::drop('feedback', function (Blueprint $table) {
            //
        });
    }
}
