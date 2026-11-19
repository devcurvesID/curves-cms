<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_photos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('member_id')->unsigned();
            $table->string('photo', 255);
            $table->integer('album_group')->default('1');
            $table->timestamps();

            $table->foreign('member_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_photos');
    }
}
