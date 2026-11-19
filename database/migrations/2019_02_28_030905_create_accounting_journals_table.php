<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountingJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting_journals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('coa_id')->unsigned();
            $table->date('trans_date');
            $table->float('debit')->default(0);
            $table->float('credit')->default(0);
            $table->text('information');
            $table->integer('club_id')->unsigned();
            $table->timestamps();

            $table->foreign('coa_id')->references('id')->on('chart_of_accounts');
            $table->foreign('club_id')->references('id')->on('club_infos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounting_journals');
    }
}
