<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoachTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coach_targets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('coach_id')->unsigned();
            $table->integer('target_category_detail_id')->unsigned();
            $table->integer('target');
            $table->integer('acquired')->default(0);
            $table->enum('status', ['Active', 'Inactive']);

            $table->timestamps();

            $table->foreign('coach_id')->references('id')->on('users');
            $table->foreign('target_category_detail_id')->references('id')->on('target_category_details');

            $table->unique(['coach_id', 'target_category_detail_id'], 'coach_detail_target');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coach_targets');
    }
}
