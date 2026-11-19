<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountingJubilantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting_jubilants', function (Blueprint $table) {
            $table->integer('accounting_id')->unsigned()->index();
            $table->integer('jubilant_id')->unsigned()->index();
            $table->timestamps();

            $table->primary(['accounting_id','jubilant_id']);

            $table->foreign('accounting_id')->references('id')->on('accounting_accounts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('jubilant_id')->references('id')->on('mst_jubilants')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounting_jubilants');
    }
}
