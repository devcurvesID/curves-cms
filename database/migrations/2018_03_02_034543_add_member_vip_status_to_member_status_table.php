<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMemberVipStatusToMemberStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_status', function (Blueprint $table) {
            $table->enum('member_vip_status', ['General', 'VIP'])->default('General');
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
            $table->dropColumn('member_vip_status');
        });
    }
}
