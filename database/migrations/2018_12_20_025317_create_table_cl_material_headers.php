<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableClMaterialHeaders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cl_material_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('category_id')->unsigned()->index();
            $table->integer('sequences');
            $table->timestamps();

            //create FK
            $table->foreign('category_id')->references('id')->on('cl_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cl_material_headers');
    }
}
