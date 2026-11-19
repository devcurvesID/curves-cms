<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClubInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('club_name')->unique();
            /*$table->integer('country_id')->unsigned();
            $table->integer('province_id')->unsigned();
            $table->integer('city_id')->unsigned();*/
            $table->string('postal', 6)->nullable();
            $table->string('address', 100);
            $table->string('city', 50);
            $table->string('province', 50);
            $table->string('country', 50);
            $table->string('phones', 15);
            $table->string('email', 50)->nullable();
            $table->string('facebook', 50)->nullable();
            $table->string('photo')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();

            /*$table->foreign('country_id')->references('id')->on('mst_countries')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('province_id')->references('id')->on('mst_provinces')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('city_id')->references('id')->on('mst_cities')->onDelete('cascade')->onUpdate('cascade');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('club_infos');
    }
}
