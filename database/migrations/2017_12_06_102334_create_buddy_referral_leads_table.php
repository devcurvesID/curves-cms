<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuddyReferralLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buddy_referral_leads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fullname', 100);
            $table->string('phone', 50);
            $table->enum('is_br', ['BR', 'NotBR'])->default('NotBR');
            $table->integer('source_id')->unsigned()->nullable();
            $table->integer('staff_id')->unsigned();
            $table->integer('club_id')->unsigned();
            $table->enum('status_gm', ['Guest', 'Member'])->default('Guest');
            $table->timestamps();

            $table->foreign('source_id')->references('id')->on('member_sources');
            $table->foreign('staff_id')->references('id')->on('users');
            $table->foreign('club_id')->references('id')->on('club_infos');
        });

        Schema::table('guest_appointments', function (Blueprint $table) {
            $table->foreign('guest_id')->references('id')->on('buddy_referral_leads');
        });

        Schema::table('member_personals', function (Blueprint $table) {
            $table->foreign('guest_id')->references('id')->on('buddy_referral_leads');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guest_appointments', function (Blueprint $table) {
            $table->dropForeign('guest_appointments_guest_id_foreign');
        });

        Schema::table('member_personals', function (Blueprint $table) {
            $table->dropForeign('member_personals_guest_id_foreign');
        });

        Schema::dropIfExists('buddy_referral_leads');
    }
}
