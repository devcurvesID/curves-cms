<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('club_id')->unsigned();
            $table->integer('tax_type_id')->unsigned();
            $table->float('rate', 5, 3);
            $table->timestamp('from_date');
            $table->timestamp('thru_date')->nullable();
            $table->timestamps();

            $table->foreign('club_id')->references('id')->on('club_infos');
            $table->foreign('tax_type_id')->references('id')->on('tax_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tax_configs');
    }
}
