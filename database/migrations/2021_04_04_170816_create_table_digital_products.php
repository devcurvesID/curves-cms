<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDigitalProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('digital_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('item_name');
            $table->float('default_price');
            $table->string('item_image')->nullable();
            $table->text('notes')->nullable();
            $table->date('deleted_date')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('digital_products');
    }
}
