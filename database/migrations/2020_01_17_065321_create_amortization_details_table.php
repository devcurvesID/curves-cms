<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmortizationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amortization_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('amortization_header_id')->unsigned();
            $table->integer('club_id')->unsigned();
            $table->date('amortization_date');
            $table->integer('month_number');
            $table->float('amount_paid')->default(0);
            $table->enum('status', ['Pending', 'Published'])->default('Published');
            $table->enum('accounting_status', ['Pending', 'Published'])->default('Pending');
            $table->timestamps();

            $table->foreign('amortization_header_id')->references('id')->on('amortization_headers');
            $table->foreign('club_id')->references('id')->on('club_infos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('amortization_details');
    }
}
