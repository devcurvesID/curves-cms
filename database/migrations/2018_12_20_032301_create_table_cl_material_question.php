<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableClMaterialQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cl_material_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->text('question');
            $table->integer('material_content_id')->unsigned()->index();
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
        Schema::dropIfExists('cl_material_questions');
    }
}
