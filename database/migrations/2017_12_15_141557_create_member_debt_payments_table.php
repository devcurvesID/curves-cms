<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberDebtPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_debt_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('member_payment_id')->unsigned();
            $table->string('payment_number', 255)->unique();
            $table->integer('amount_due')->default(0);
            $table->integer('amount_paid')->default(0);
            $table->integer('rest_of_bill')->default(0);
            $table->timestamp('payment_date');
            $table->timestamps();

            $table->foreign('member_payment_id')->references('id')->on('member_payments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_debt_payments');
    }
}
