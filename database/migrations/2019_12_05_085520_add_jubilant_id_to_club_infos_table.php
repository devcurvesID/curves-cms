<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJubilantIdToClubInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('club_infos', function (Blueprint $table) {
            $table->integer('jubilant_id')->unsigned()->nullable();

            $table->foreign('jubilant_id')->references('id')->on('mst_jubilants');
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
            $table->dropForeign('club_infos_jubilant_id_foreign');
            $table->dropColumn('jubilant_id');
        });
    }
}
