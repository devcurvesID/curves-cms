<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIgnoreWaColumnToMemberStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_status', function (Blueprint $table) {
            $table->boolean('ignore_wa')->default(false);
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
            $table->dropColumn('ignore_wa');
        });
    }
}
