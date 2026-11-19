<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableClMaterialContents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cl_material_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('material_id')->unsigned()->index();
            $table->integer('sequence');
            $table->text('content');
            $table->enum('status', ['Approved', 'NotApproved']);
            $table->integer('total_question')->default(0);
            $table->timestamps();

            //Create FK
            $table->foreign('material_id')->references('id')->on('cl_material_headers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cl_material_contents');
    }
}
