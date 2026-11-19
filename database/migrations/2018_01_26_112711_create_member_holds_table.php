<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberHoldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_holds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('club_id')->unsigned();
            $table->integer('member_hold_reason_id')->unsigned();
            $table->text('member_hold_note', 255)->nullable();
            $table->date('from_date');
            $table->date('thru_date');
            $table->enum('status', ['Pending', 'OnHold', 'Cancelled', 'Finished'])->default('Pending');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('club_id')->references('id')->on('club_infos');
            $table->foreign('member_hold_reason_id')->references('id')->on('member_hold_reasons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_holds');
    }
}
