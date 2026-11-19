<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountingClubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting_clubs', function (Blueprint $table) {
            $table->integer('accounting_id')->unsigned()->index();
            $table->integer('club_id')->unsigned()->index();
            $table->timestamps();

            $table->primary(['accounting_id', 'club_id']);

            $table->foreign('accounting_id')->references('id')->on('accounting_accounts')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('accounting_clubs');
    }
}
