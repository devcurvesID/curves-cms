<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToMemberStatusTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_status', function (Blueprint $table) {
            $table->enum('payment_status', ['NotSet', 'Set'])->default('NotSet');
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
            $table->dropColumn('payment_status');
        });
    }
}
