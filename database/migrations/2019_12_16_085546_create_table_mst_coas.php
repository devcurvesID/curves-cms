<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMstCoas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_coas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('coa_number')->unique();
            $table->string('coa_name')->unique();
            $table->string('coa_abv_name')->unique();
            $table->integer('main_account_id')->unsigned();
            $table->timestamps();

            $table->foreign('main_account_id')->references('id')->on('main_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_coas');
    }
}
