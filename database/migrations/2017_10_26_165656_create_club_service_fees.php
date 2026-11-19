<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClubServiceFees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_service_fees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('club_id')->unsigned();
            $table->integer('membership_type_id')->unsigned();
            $table->integer('sf_amount');
            $table->enum('sf_freq', ['One Time', 'Monthly']);
            $table->integer('mf_amount');
            $table->enum('mf_freq', ['One Time', 'Monthly']);
            $table->float('pre_pay_discount', 6, 3)->default('0');
            $table->integer('pre_pay_discount_value')->default('0');
            $table->integer('agreement_renewal_type_id')->unsigned();
            $table->enum('is_taxable', ['Yes', 'No']);
            $table->timestamp('from_date');
            $table->timestamp('thru_date')->nullable();
            $table->timestamps();

            $table->foreign('club_id')->references('id')->on('club_infos')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('club_service_fees');
    }
}
