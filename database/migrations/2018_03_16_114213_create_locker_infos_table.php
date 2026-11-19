<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLockerInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locker_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('locker_name');
            $table->integer('club_id')->unsigned();
            $table->timestamps();

            $table->foreign('club_id')->references('id')->on('club_infos')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('member_lockers', function (Blueprint $table) {
            $table->integer('locker_info_id')->unsigned();

            $table->foreign('locker_info_id')->references('id')->on('locker_infos')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_lockers', function (Blueprint $table) {
            $table->dropForeign('member_lockers_locker_info_id_foreign');
        });

        Schema::dropIfExists('locker_infos');
    }
}
