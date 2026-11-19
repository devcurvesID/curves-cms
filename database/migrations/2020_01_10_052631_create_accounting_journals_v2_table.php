<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountingJournalsV2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting_journals_v2', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('jubilant_id')->unsigned();
            $table->integer('coa_id')->unsigned();
            $table->date('trans_date');
            $table->date('bank_date')->nullable();
            $table->float('debit')->default(0);
            $table->float('credit')->default(0);
            $table->string('invoice_number');
            $table->string('information');
            $table->enum('d_or_c', ['Debit', 'Credit']);
            $table->timestamps();

            $table->foreign('jubilant_id')->references('id')->on('mst_jubilants');
            $table->foreign('coa_id')->references('id')->on('chart_of_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounting_journals_v2');
    }
}
