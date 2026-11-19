<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGuestMembershipMemberIdColumnToGuestWosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guest_wos', function (Blueprint $table) {
            $table->integer('guest_membership_member_id')->unsigned();

            $table->foreign('guest_membership_member_id')->references('id')->on('guest_membership_members');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guest_wos', function (Blueprint $table) {
            $table->dropColumn('guest_membership_member_id');
        });
    }
}
