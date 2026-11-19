<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTravelPassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travel_pass', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->date('from_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->date('thru_date')->nullable();
            $table->date('active_from_date')->nullable();
            $table->date('active_thru_date')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status',['Active','Finished']);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('travel_pass');
    }
}
