<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestMonitoringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_monitorings', function (Blueprint $table) {
            $table->integer('guest_id')->unsigned()->primary('guest_id');
            $table->integer('success')->default(0);
            $table->integer('rejected')->default(0);
            $table->integer('hanging_out')->default(0);
            $table->integer('invalid')->default(0);
            $table->integer('call_count')->default(0);
            $table->dateTime('last_call')->nullable();
            $table->enum('last_call_status', ['Success', 'Rejected', 'Hanging', 'Invalid', 'Not Called'])->default('Not Called');
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
        Schema::dropIfExists('guest_monitorings');
    }
}
