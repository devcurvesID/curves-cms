<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstPromoProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_promo_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('promo_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('qty')->default(0);
            $table->timestamps();

            $table->foreign('promo_id')->references('id')->on('mst_products')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('mst_products')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('mst_products', function (Blueprint $table) {
            $table->boolean('is_promo')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_promo_products');

        Schema::table('mst_products', function (Blueprint $table) {
            $table->dropColumn('is_promo');
        });
    }
}
