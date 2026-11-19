<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogHydrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_hydrations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->dateTime('hydration_datetime')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->double('hydration_value');
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
        Schema::dropIfExists('log_hydrations');
    }
}
