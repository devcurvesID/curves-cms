<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSequenceColumnToClubInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('club_infos', function (Blueprint $table) {
            $table->integer('current_sequence')->unsigned()->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('club_infos', function (Blueprint $table) {
            $table->dropColumn('current_sequence');
        });
    }
}
