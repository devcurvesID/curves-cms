<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCaManagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ca_managements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('club_id')->unsigned();
            $table->string('type');
            $table->string('code');
            $table->date('ca_date');
            $table->timestamps();

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
        Schema::dropIfExists('ca_managements');
    }
}
