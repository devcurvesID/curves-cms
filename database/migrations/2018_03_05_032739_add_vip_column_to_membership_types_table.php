<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVipColumnToMembershipTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('membership_types', function (Blueprint $table) {
            $table->enum('is_vip', ['general', 'vip'])->default('general');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('membership_types', function (Blueprint $table) {
            $table->dropColumn('is_vip');
        });
    }
}
