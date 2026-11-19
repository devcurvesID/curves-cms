<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableClCoachPass extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cl_coach_pass', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('material_content_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('material_content_id')->references('id')->on('cl_material_contents');
            $table->foreign('user_id')->references('id')->on('users');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cl_coach_pass');
    }
}
