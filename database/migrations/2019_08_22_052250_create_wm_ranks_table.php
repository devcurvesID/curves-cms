<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWmRanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wm_ranks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('wm_one');
            $table->integer('wm_two');
            $table->float('body_fat_diff')->default(0.0);
            $table->float('weigh_diff')->default(0.0);
            $table->float('size_diff')->default(0.0);
            $table->integer('day_diff')->default(0);
            $table->integer('wo_count')->default(0);
            $table->enum('type',['monthly','overall'])->default('monthly');
            $table->timestamps();

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
        Schema::dropIfExists('wm_ranks');
    }
}
