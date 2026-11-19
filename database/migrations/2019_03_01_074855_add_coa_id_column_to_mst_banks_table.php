<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCoaIdColumnToMstBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_banks', function (Blueprint $table) {
            $table->string('bank_account_number')->nullable();
            $table->integer('coa_id')->unsigned()->nullable();

            $table->foreign('coa_id')->references('id')->on('chart_of_accounts')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_banks', function (Blueprint $table) {
            $table->dropColumn('coa_id');
            $table->dropColumn('bank_account_number');
        });
    }
}
