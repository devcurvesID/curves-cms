<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClubCodeColumnToClubInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('club_infos', function (Blueprint $table) {
            $table->string('club_code', 10)->nullable();
        });

        Schema::table('workouts', function (Blueprint $table) {
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
        Schema::table('club_infos', function (Blueprint $table) {
            $table->dropColumn('club_code');
        });

        Schema::table('workouts', function (Blueprint $table) {
            $table->dropForeign('workouts_club_id_foreign');
        });
    }
}
