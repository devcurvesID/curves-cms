<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('member_id')->unsigned();
            $table->integer('staff_id')->unsigned();
            $table->text('report');
            $table->text('note')->nullable();
            $table->timestamp('contact_date');
            $table->timestamps();

            $table->foreign('member_id')->references('id')->on('users');
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
        Schema::dropIfExists('staff_reports');
    }
}
