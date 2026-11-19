<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffInactivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_inactives', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('staff_id')->unsigned();
            $table->enum('inactive_type', ['Permission', 'Resign']);
            $table->text('reason')->nullable();
            $table->date('from_date');
            $table->date('thru_date')->nullable();
            $table->enum('status', ['Ongoing', 'Done', 'Reactivated']);
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_inactives');
    }
}
