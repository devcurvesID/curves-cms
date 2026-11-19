<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoachMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coach_members', function (Blueprint $table) {
            $table->integer('member_id')->unsigned()->primary('member_id');
            $table->integer('coach_id')->unsigned();
            $table->timestamps();

            $table->foreign('member_id')->references('id')->on('users');
            $table->foreign('coach_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coach_members');
    }
}
