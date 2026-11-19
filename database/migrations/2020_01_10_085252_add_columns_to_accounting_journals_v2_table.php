<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToAccountingJournalsV2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounting_journals_v2', function (Blueprint $table) {
            $table->integer('club_id')->unsigned()->nullable();
            $table->integer('pair')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounting_journals_v2', function (Blueprint $table) {
            $table->dropColumn('club_id');
            $table->dropColumn('pair');
        });
    }
}
