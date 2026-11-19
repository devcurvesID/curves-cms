<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonthlyEndingBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_ending_balances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('jubilant_id')->unsigned();
            $table->integer('club_id')->unsigned()->nullable();
            $table->integer('coa_id')->unsigned();
            $table->date('recap_date');
            $table->float('debit')->default(0);
            $table->float('credit')->default(0);
            $table->enum('d_or_c', ['Debit', 'Credit']);
            $table->timestamps();

//            $table->foreign('jubilant_id')->references('id')->on('mst_jubilants');
//            $table->foreign('club_id')->references('id')->on('club_infos');
//            $table->foreign('coa_id')->references('id')->on('chart_of_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monthly_ending_balances');
    }
}
