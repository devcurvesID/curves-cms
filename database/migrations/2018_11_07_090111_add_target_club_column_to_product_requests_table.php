<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTargetClubColumnToProductRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_requests',function(Blueprint $table) {
            $table->integer('target_club')->unsigned()->default(1);

            $table->foreign('target_club')->references('id')->on('club_infos')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_requests',function(Blueprint $table){
            $table->dropColumn('target_club');
        });
    }
}
