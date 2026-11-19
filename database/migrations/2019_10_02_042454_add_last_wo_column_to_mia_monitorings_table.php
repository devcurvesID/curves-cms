<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastWoColumnToMiaMonitoringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mia_monitorings', function (Blueprint $table) {
            $table->date('last_wo');
        });    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mia_monitorings', function (Blueprint $table) {
            $table->dropColumn('last_wo');
        });    }
}
