<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstPredefinedMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_predefined_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('message')->unique();
            $table->timestamps();
        });

        Schema::table('member_message_notes', function (Blueprint $table) {
            $table->foreign('predefined_message')->references('id')->on('mst_predefined_messages')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_message_notes', function (Blueprint $table) {
            $table->dropForeign('member_message_notes_predefined_message_foreign');
        });

        Schema::dropIfExists('mst_predefined_messages');
    }
}
