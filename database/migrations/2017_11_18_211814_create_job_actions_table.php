<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Carbon\Carbon;

class CreateJobActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'job_actions', 
            function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();

                $table->boolean('job_accepted')->default(false);
                $table->boolean('job_approved')->default(false);
                $table->boolean('job_declined')->default(false);

                $table->timestamp('job_accepted_updated_on')->default(Carbon::now());
                $table->timestamp('job_approved_updated_on')->default(Carbon::now());
                $table->timestamp('job_declined_updated_on')->default(Carbon::now());

                $table->integer('job_id')->unsigned();
                $table->foreign('job_id')
                        ->references('id')->on('jobs')
                        ->onDelete('cascade');
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
        Schema::dropIfExists('job_actions');
    }
}
