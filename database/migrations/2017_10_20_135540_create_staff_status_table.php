<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_status', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->primary('user_id');
            $table->enum('status', ['Active', 'Inactive', 'Resigned'])->default('Active');
            $table->date('from_date')->nullable();
            $table->date('thru_date')->nullable();
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
        Schema::dropIfExists('staff_status');
    }
}
