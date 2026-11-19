<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDigitalProductColumnToClubInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('club_infos', function (Blueprint $table) {
            $table->boolean('digital_product')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('club_infos', function (Blueprint $table) {
            $table->dropColumn('digital_product');
        });
    }
}
