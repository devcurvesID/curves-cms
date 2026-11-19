<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeighMeasureAttributesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weigh_measure_attributes', function (Blueprint $table) {
            $table->integer('wm_id')->unsigned()->index();
            $table->integer('wm_attribute_id')->unsigned()->index();
            $table->timestamps();

            $table->primary(['wm_id', 'wm_attribute_id']);

            $table->foreign('wm_id')->references('id')->on('weigh_measures')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('wm_attribute_id')->references('id')->on('mst_weigh_measure_attributes')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weigh_measure_attributes');
    }
}
