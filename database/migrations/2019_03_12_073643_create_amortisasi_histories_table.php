<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmortisasiHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amortisasi_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('period', 20);
            $table->float('debit_balance')->default(0);
            $table->float('credit_balance')->default(0);
            $table->integer('club_id')->unsigned();
            $table->enum('status', ['default', 'edited']);
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
        Schema::dropIfExists('amortisasi_histories');
    }
}
