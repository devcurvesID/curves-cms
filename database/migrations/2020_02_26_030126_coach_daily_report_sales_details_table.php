<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CoachDailyReportSalesDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coach_daily_report_sales_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('coach_daily_report_header_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->string('type');
            $table->integer('total')->default(0);
            $table->timestamps();

            $table->foreign('coach_daily_report_header_id')->references('id')->on('coach_daily_report_headers');
            $table->foreign('category_id')->references('id')->on('mst_coach_daily_report_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coach_daily_report_sales_details');
    }
}
