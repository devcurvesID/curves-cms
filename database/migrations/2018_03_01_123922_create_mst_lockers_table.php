<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstLockersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_lockers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('duration')->default('1');
            $table->enum('duration_type', ['daily', 'monthly', 'yearly'])->default('monthly');
            $table->integer('price')->default('0');
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
        Schema::dropIfExists('mst_lockers');
    }
}
