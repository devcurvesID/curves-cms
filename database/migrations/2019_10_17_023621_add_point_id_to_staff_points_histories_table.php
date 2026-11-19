<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPointIdToStaffPointsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_points_histories', function (Blueprint $table) {
            $table->integer('point_id')->unsigned();
            $table->enum('category', ['Weekly', 'Monthly'])->default('Weekly');

            $table->foreign('point_id')->references('id')->on('mst_points');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_points_histories', function (Blueprint $table) {
            $table->dropColumn('point_id');
            $table->dropColumn('category');
        });
    }
}
