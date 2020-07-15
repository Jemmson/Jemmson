<?php
//
//use Illuminate\Database\Migrations\Migration;
//use Illuminate\Database\Schema\Blueprint;
//use Illuminate\Support\Facades\Schema;
//
//class CreateSubscriptionsTable extends Migration
//{
//    /**
//     * Run the migrations.
//     *
//     * @return void
//     */
//    public function up()
//    {
//        Schema::create('subscriptions', function (Blueprint $table) {
//            $table->id();
//            $table->timestamps();
//            $table->bigInteger('user_id');
//            $table->string('name')->nullable();
//            $table->bigInteger('stripe_id')->nullable();
//            $table->string('stripe_status')->nullable();
//            $table->string('stripe_plan')->nullable();
//            $table->integer('quantity')->nullable();
//            $table->date('trial_ends_at')->nullable();
//            $table->date('ends_at')->nullable();
//        });
//    }
//
//    /**
//     * Reverse the migrations.
//     *
//     * @return void
//     */
//    public function down()
//    {
//        Schema::dropIfExists('subscriptions');
//    }
//}
