<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnClubIdToStaffAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_attendances',function (Blueprint $table){
            $table->integer('club_id')->unsigned();

            $table->foreign('club_id')->references('id')->on('club_infos')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_attendances',function (Blueprint $table){
            $table->dropColumn('club_id');
        });
    }
}
