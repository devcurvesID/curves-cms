<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstNoSaleReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_no_sale_reasons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nosale_reason', 255)->unique();
            $table->timestamps();
        });

        Schema::table('no_sale_reasons', function (Blueprint $table) {
            $table->foreign('no_sale_reason_id')->references('id')->on('mst_no_sale_reasons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('no_sale_reasons', function (Blueprint $table) {
            $table->dropForeign('no_sale_reasons_no_sale_reason_id_foreign');
        });

        Schema::dropIfExists('mst_no_sale_reasons');
    }
}
