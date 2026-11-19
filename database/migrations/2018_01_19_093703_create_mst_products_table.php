<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_type_id')->unsigned();
            $table->string('item_name')->unique();
            $table->integer('default_price')->default('0');
            $table->enum('is_taxable', ['Yes', 'No'])->default('Yes');
            $table->enum('accessibility', ['Retail', 'Member', 'Reward'])->default('Retail');
            $table->enum('status', ['Active', 'Inactive'])->default('Inactive');
            $table->string('product_image', 255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('product_type_id')->references('id')->on('mst_product_types');
        });

        Schema::table('reward_histories', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('mst_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reward_histories', function (Blueprint $table) {
            $table->dropForeign('reward_histories_product_id_foreign');
        });

        Schema::dropIfExists('mst_products');
    }
}
