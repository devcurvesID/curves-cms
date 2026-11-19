<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffCertificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_certifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('staff_id')->unsigned();
            $table->integer('certification_id')->unsigned();
            $table->string('certification_number')->default('0000');
            $table->integer('level')->default('1');
            $table->date('start_date')->nullable();
            $table->date('thru_date')->nullable();
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('certification_id')->references('id')->on('mst_certifications')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_certifications');
    }
}
