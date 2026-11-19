<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CoachDailyReportArDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coach_daily_report_ar_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('coach_daily_report_header_id')->unsigned();
            $table->integer('source_id')->unsigned();
            $table->integer('total_calling')->default(0);
            $table->integer('answer')->default(0);
            $table->integer('apo')->default(0);
            $table->integer('reschedule')->default(0);
            $table->integer('reject')->default(0);
            $table->integer('wrong_number')->default(0);
            $table->integer('invalid')->default(0);
            $table->integer('na')->default(0);
            $table->integer('leads')->default(0);
            $table->timestamps();

            $table->foreign('coach_daily_report_header_id')->references('id')->on('coach_daily_report_headers');
            $table->foreign('source_id')->references('id')->on('member_sources');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coach_daily_report_ar_details');
    }
}
