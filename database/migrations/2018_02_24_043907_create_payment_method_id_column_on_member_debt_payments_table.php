<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentMethodIdColumnOnMemberDebtPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_debt_payments', function (Blueprint $table) {
            $table->integer('payment_method_id')->unsigned();

            $table->foreign('payment_method_id')->references('id')->on('mst_payment_methods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_debt_payments', function (Blueprint $table) {
            $table->dropColumn('payment_method_id');
        });
    }
}
