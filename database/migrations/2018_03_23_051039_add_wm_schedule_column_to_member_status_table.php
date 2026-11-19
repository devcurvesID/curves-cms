<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWmScheduleColumnToMemberStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_status', function (Blueprint $table) {
            $table->enum('wm_schedule', ['Set', 'NotSet'])->default('NotSet');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_status', function (Blueprint $table) {
            $table->dropColumn('wm_schedule');
        });
    }
}
