<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestMembershipMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_membership_members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('guest_id')->unsigned();
            $table->integer('gm_id')->unsigned();
            $table->date('from_date');
            $table->date('thru_date');
            $table->timestamps();

            $table->foreign('guest_id')->references('id')->on('buddy_referral_leads');
            $table->foreign('gm_id')->references('id')->on('guest_memberships');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guest_membership_members');
    }
}
