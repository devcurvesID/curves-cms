<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTargetCategoryIdColumnToMstPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_points', function (Blueprint $table) {
            $table->integer('target_category_id')->unsigned()->default(1);

            $table->foreign('target_category_id')->references('id')->on('target_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_points', function (Blueprint $table) {
            $table->dropForeign('mst_points_target_category_id_foreign');
            $table->dropColumn('target_category_id');
        });
    }
}
