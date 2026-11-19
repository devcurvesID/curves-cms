<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoachDailyReportHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coach_daily_report_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('coach_id')->unsigned();
            $table->date('date');
            $table->timestamps();

            $table->foreign('coach_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coach_daily_report_headers');
    }
}
