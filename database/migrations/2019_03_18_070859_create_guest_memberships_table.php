<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestMembershipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_memberships', function (Blueprint $table) {
            $table->increments('id');
            $table->string('gm_name')->unique();
            $table->enum('duration_type', ['Year', 'Month', 'Week', 'Day']);
            $table->integer('duration_long');
            $table->integer('max_workout');
            $table->date('from_date');
            $table->date('thru_date');
            $table->integer('price')->default(0);
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
        Schema::dropIfExists('guest_memberships');
    }
}
