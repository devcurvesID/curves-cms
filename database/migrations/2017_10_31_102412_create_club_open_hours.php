<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClubOpenHours extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_open_hours', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('club_id')->unsigned();
            $table->enum('from_day', ['Sunday', 'Monday', 'Tuesday', 'Wednessday', 'Thursday', 'Friday', 'Saturday']);
            $table->enum('thru_day', ['Sunday', 'Monday', 'Tuesday', 'Wednessday', 'Thursday', 'Friday', 'Saturday']);
            $table->timeTz('from_hour');
            $table->timeTz('thru_hour');
            $table->timestamps();
			
            $table->foreign('club_id')->references('id')->on('club_infos')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('club_open_hours');
    }
}
