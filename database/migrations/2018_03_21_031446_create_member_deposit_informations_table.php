<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberDepositInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_deposit_informations', function (Blueprint $table) {
            $table->integer('member_deposit_id')->unsigned()->primary('member_deposit_id');
            $table->text('information');
            $table->timestamps();

            $table->foreign('member_deposit_id')->references('id')->on('member_deposits')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_deposit_informations');
    }
}
