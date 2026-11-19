<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToChartOfAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->integer('jubilant_id')->unsigned()->nullable();
            $table->foreign('jubilant_id')->references('id')->on('mst_jubilants');

            $table->unique(['coa_number', 'jubilant_id'], 'coa_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropForeign('chart_of_accounts_jubilant_id_foreign');
            $table->dropUnique('coa_number_unique');
        });
    }
}
