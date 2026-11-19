<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCoaIdColumnToMstCostRevenuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_cost_revenues', function (Blueprint $table) {
            $table->integer('coa_id')->unsigned();

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
        Schema::table('mst_cost_revenues', function (Blueprint $table) {
            $table->dropColumn('coa_id');
        });
    }
}
