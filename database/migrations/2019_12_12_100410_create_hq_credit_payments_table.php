<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHqCreditPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hq_credit_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hq_credit_id')->unsigned();
            $table->string('payment_number')->unique();
            $table->date('payment_date');
            $table->float('total');
            $table->string('information')->nullable();
            $table->integer('payment_type_id')->unsigned();
            $table->integer('bank_id')->unsigned();
            $table->integer('staff_id')->unsigned();
            $table->timestamps();

            $table->foreign('hq_credit_id')->references('id')->on('hq_credits');
            $table->foreign('payment_type_id')->references('id')->on('mst_payment_methods');
            $table->foreign('bank_id')->references('id')->on('mst_banks');
            $table->foreign('staff_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hq_credit_payments');
    }
}
