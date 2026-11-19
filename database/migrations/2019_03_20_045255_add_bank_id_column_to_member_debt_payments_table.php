<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBankIdColumnToMemberDebtPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_debt_payments', function (Blueprint $table) {
            $table->integer('bank_id')->unsigned()->default(1);

            $table->foreign('bank_id')->references('id')->on('mst_banks');
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
            $table->dropColumn('bank_id');
        });
    }
}
