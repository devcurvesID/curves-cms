<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTshirtSizeToMemberPersonalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_personals', function (Blueprint $table) {
            $table->integer('tshirt_size')->unsigned()->default(3);

            $table->foreign('tshirt_size')->references('id')->on('mst_sizes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_personals', function (Blueprint $table) {
            $table->dropColumn('tshirt_size');
        });
    }
}
