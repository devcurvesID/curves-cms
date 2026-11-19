<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUniqueCoaNumberFromChartOfAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropUnique('chart_of_accounts_coa_number_unique');
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
            $table->unique('coa_number');
        });
    }
}
