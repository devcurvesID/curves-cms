<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstAccessMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_access_menus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->unique();
            $table->string('menu_description', 255)->nullable();
            $table->integer('default_role')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('default_role')->references('id')->on('mst_roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_access_menus');
    }
}
