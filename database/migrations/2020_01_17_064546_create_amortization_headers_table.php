<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmortizationHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amortization_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('membership_type_id')->unsigned();
            $table->enum('amortization_status', ['Ongoing', 'Done'])->default('Ongoing');
            $table->date('contract_start');
            $table->integer('membership_duration')->default(1);
            $table->integer('amortization_month')->default(1);
            $table->integer('rest_of_amortization_month')->default(0);
            $table->float('total_amount_due')->default(0);
            $table->float('monthly_bill')->default(0);
            $table->enum('status', ['Published', 'Closed'])->default('Published');
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
        Schema::dropIfExists('amortization_headers');
    }
}
