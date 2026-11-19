<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberHealthHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_health_histories', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->primary('user_id');
            $table->string('doctor_name', 50)->nullable();
            $table->string('doctor_contact_number', 20)->nullable();
            $table->text('comment', 255)->nullable();
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
        Schema::dropIfExists('member_health_histories');
    }
}
