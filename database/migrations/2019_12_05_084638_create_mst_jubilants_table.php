<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstJubilantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_jubilants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('jubilant_name')->unique();
            $table->enum('type', ['Main', 'Franchise'])->default('Franchise');
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
        Schema::dropIfExists('mst_jubilants');
    }
}
