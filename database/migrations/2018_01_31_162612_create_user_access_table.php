<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_access', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->index();
            $table->integer('menu_id')->unsigned()->index();
            $table->timestamps();

            $table->primary(['user_id', 'menu_id']);

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('menu_id')->references('id')->on('mst_access_menus')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_access');
    }
}
