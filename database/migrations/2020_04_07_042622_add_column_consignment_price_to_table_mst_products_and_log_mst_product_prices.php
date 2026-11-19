<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnConsignmentPriceToTableMstProductsAndLogMstProductPrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_products', function (Blueprint $table) {
            $table->float('consignment_price')->default(0);
        });

        Schema::table('log_mst_product_prices', function (Blueprint $table) {
            $table->float('consignment_price')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_products', function (Blueprint $table) {
            $table->dropColumn('consignment_price');
        });

        Schema::table('log_mst_product_prices', function (Blueprint $table) {
            $table->dropColumn('consignment_price');
        });
    }
}
