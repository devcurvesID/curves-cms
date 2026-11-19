<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUniqueCoaNameCoaAbvNameFromChartOfAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropUnique('chart_of_accounts_coa_name_unique');
            $table->dropUnique('chart_of_accounts_coa_abv_name_unique');
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
            $table->unique('coa_name');
            $table->unique('coa_abv_name');
        });
    }
}
