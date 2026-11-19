<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogManualTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_manual_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('acc_journal_debit_id')->unsigned();
            $table->integer('acc_journal_credit_id')->unsigned();
            $table->integer('club_id')->unsigned();
            $table->integer('coa_id')->unsigned();
            $table->integer('debit');
            $table->integer('credit');
            $table->string('information');
            $table->integer('coa_via_payment_id')->unsigned();
            $table->integer('account_id')->unsigned();
            $table->timestamps();

            $table->foreign('acc_journal_debit_id')->references('id')->on('accounting_journals');
            $table->foreign('acc_journal_credit_id')->references('id')->on('accounting_journals');
            $table->foreign('club_id')->references('id')->on('club_infos');
            $table->foreign('coa_id')->references('id')->on('chart_of_accounts');
            $table->foreign('coa_via_payment_id')->references('id')->on('chart_of_accounts');
            $table->foreign('account_id')->references('id')->on('accounting_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_manual_transactions');
    }
}
