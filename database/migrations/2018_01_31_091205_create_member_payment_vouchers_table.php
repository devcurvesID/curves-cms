<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberPaymentVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_payment_vouchers', function (Blueprint $table) {
            $table->integer('member_payment_id')->unsigned()->primary('member_payment_id');
            $table->string('voucher_code')->nullable();
            $table->integer('voucher_value')->default('0');
            $table->timestamps();

            $table->foreign('member_payment_id')->references('id')->on('member_payments')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_payment_vouchers');
    }
}
