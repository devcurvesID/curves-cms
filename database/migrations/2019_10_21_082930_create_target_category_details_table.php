<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTargetCategoryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('target_category_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('target_category_id')->unsigned();
            $table->enum('target_interval', ['Daily', 'Weekly', 'Monthly']);
            $table->integer('target')->default(0);
            $table->enum('type', ['Staff', 'Club']);
            $table->enum('status', ['Active', 'Inactive']);

            $table->timestamps();

            $table->foreign('target_category_id')->references('id')->on('target_categories');
            $table->unique(['target_category_id', 'target_interval', 'type'], 'target_generate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('target_category_details');
    }
}
