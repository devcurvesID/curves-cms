<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVoucherIdToProductRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_refunds', function (Blueprint $table){
            $table->integer('voucher_id')->unsigned()->nullable();

            $table->foreign('voucher_id')->references('id')->on('vouchers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_refunds',function(Blueprint $table){
            $table->dropColumn('voucher_id');
        });
    }
}
