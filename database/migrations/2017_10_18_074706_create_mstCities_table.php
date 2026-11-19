<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_cities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('province_id')->unsigned();
            $table->string('city');
            $table->timestamps();

            $table->foreign('province_id')->references('id')->on('mst_provinces')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_cities');
    }
}
