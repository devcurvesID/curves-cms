<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstProvincesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_provinces', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('country_id')->unsigned();
            $table->string('province')->unique();
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('mst_countries')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_provinces');
    }
}
