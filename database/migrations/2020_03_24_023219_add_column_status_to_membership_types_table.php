<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStatusToMembershipTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('membership_types', function (Blueprint $table) {
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
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
            $table->dropColumn('status');
        });
    }
}
