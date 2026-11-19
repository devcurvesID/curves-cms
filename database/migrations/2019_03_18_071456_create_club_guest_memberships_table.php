<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClubGuestMembershipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_guest_memberships', function (Blueprint $table) {
            $table->integer('gm_id')->unsigned();
            $table->integer('club_id')->unsigned();
            $table->timestamps();

            $table->primary(['gm_id', 'club_id']);

            $table->foreign('gm_id')->references('id')->on('guest_memberships');
            $table->foreign('club_id')->references('id')->on('club_infos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('club_guest_memberships');
    }
}
