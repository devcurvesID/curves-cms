<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_status', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->primary('user_id');
            $table->enum('status', ['Active', 'Inactive', 'Collective', 'Reactivation', 'Stopped', 'Hold']);
            $table->integer('membership_type_id')->unsigned();
            $table->date('from_date');
            $table->date('thru_date');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('membership_type_id')->references('id')->on('membership_types')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_status');
    }
}
