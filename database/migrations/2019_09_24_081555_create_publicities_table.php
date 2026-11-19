<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publicities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('photo');
            $table->string('headline');
            $table->date('from_date');
            $table->date('thru_date');
            $table->text('note')->nullable();
            $table->enum('status', ['publish', 'draft'])->default('draft');

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
        Schema::dropIfExists('publicities');
    }
}
