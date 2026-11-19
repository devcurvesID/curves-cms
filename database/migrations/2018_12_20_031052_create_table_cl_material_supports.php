<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableClMaterialSupports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cl_material_supports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('file_name');
            $table->integer('material_content_id')->unsinged()->index();
            $table->timestamps();

            //Create FK
            $table->foreign('material_content_id')->references('id')->on('cl_material_contents');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cl_material_supports');
    }
}
