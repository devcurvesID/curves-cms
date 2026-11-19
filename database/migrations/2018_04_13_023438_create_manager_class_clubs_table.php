<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManagerClassClubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manager_class_clubs', function (Blueprint $table) {
            $table->integer('manager_id')->unsigned()->index();
            $table->integer('club_id')->unsigned()->index();
            $table->timestamps();

            $table->primary(['manager_id', 'club_id']);

            $table->foreign('manager_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('club_id')->references('id')->on('club_infos')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manager_class_clubs');
    }
}
