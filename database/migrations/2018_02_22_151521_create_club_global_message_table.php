<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClubGlobalMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_global_messages', function (Blueprint $table) {
            $table->integer('club_id')->unsigned()->index();
            $table->integer('global_message_id')->unsigned()->index();
            $table->timestamps();

            //Many to many pk
            $table->primary(['club_id','global_message_id']);

            //set fk
            $table->foreign('club_id')
                  ->references('id')
                  ->on('club_infos')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('global_message_id')
                  ->references('id')
                  ->on('global_messages')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('club_global_messages');
    }
}
