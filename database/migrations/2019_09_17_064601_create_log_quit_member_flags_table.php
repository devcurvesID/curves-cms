<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogQuitMemberFlagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_quit_member_flags', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('staff_id')->unsigned()->nullable();
            $table->integer('member_id')->unsigned();
            $table->string('flag', 1);
            $table->string('club');
            $table->string('membership_type');
            $table->date('cancellation_date');
            $table->timestamps();

            $table->foreign('member_id')->references('id')->on('users');
            $table->foreign('staff_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_quit_member_flags');
    }
}
