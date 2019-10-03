<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractorSubcontractorPreferredPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractor_subcontractor_preferred_payment', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('job_task_id')->nullable();
            $table->bigInteger('contractor_id')->nullable();
            $table->bigInteger('sub_id')->nullable();
            $table->string('contractor_preferred_payment_type')->nullable();
            $table->string('subcontractor_preferred_payment_type')->nullable();
            $table->string('agreed_upon_preferred_payment_type')->nullable();
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
        Schema::dropIfExists('contractor_subcontractor_preferred_payment');
    }
}
