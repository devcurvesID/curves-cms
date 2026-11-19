<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpdateDateToLogProductInvAndLogTransferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_inventories', function (Blueprint $table) {
            $table->date('latest_update_date')->nullable();
        });

        Schema::table('log_product_inventory_transfers', function (Blueprint $table) {
            $table->date('update_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_inventories', function (Blueprint $table) {
            $table->dropColumn('latest_update_date');
        });

        Schema::table('log_product_inventory_transfers', function (Blueprint $table) {
            $table->dropColumn('update_date');
        });
    }
}
