<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankClubTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_club', function (Blueprint $table) {
            $table->integer('id_bank')->unsigned()->index();
            $table->integer('club_id')->unsigned()->index();
            $table->timestamps();

            //Set PK
            $table->primary(['id_bank', 'club_id']);

            //Set FK
            $table->foreign('id_bank')->references('id')->on('mst_banks');
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
        Schema::dropIfExists('bank_club');
    }
}
