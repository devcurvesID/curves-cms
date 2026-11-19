<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClubGlobalEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_global_event', function (Blueprint $table) {
            $table->integer('club_id')->unsigned()->index();
            $table->integer('global_event_id')->unsigned()->index();
            $table->timestamps();

            //Many to many pk
            $table->primary(['club_id','global_event_id']);

            //set fk
            $table->foreign('club_id')
                  ->references('id')
                  ->on('club_infos')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('global_event_id')
                  ->references('id')
                  ->on('global_events')
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
        Schema::dropIfExists('club_global_event');
    }
}
