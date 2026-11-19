<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberMessageNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_message_notes', function (Blueprint $table) {
            $table->integer('user_id')->insigned()->primary('user_id');
            $table->text('notes')->nullable();
            $table->date('from_date')->nullable();
            $table->date('thru_date')->nullable();
            $table->integer('predefined_message')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_message_notes');
    }
}
