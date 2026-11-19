<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClubIdColumnToLogMemberMembershipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_member_memberships', function (Blueprint $table) {
            $table->integer('club_id')->unsigned()->default('1')->nullable();
        });

        Schema::table('workouts', function (Blueprint $table) {
            $table->integer('club_id')->unsigned()->default('1')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_member_memberships', function (Blueprint $table) {
            $table->dropColumn('club_id');
        });

        Schema::table('workouts', function (Blueprint $table) {
            $table->dropColumn('club_id');
        });
    }
}
