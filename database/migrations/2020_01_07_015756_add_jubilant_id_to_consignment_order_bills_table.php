<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJubilantIdToConsignmentOrderBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consignment_order_bills', function (Blueprint $table) {
            $table->dropForeign('consignment_order_bills_club_dest_id_foreign');
            $table->dropColumn('club_dest_id');

            $table->integer('jubilant_id')->unsigned();

            $table->foreign('jubilant_id')->references('id')->on('mst_jubilants');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consignment_order_bills', function (Blueprint $table) {
            $table->integer('club_dest_id')->unsigned();
            $table->foreign('club_dest_id')->references('id')->on('club_infos');

            $table->dropForeign('consignment_order_bills_jubilant_id_foreign');
            $table->dropColumn('jubilant_id');
        });
    }
}
