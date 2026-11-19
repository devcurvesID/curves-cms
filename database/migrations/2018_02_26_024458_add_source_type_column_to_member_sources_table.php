<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSourceTypeColumnToMemberSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_sources', function (Blueprint $table) {
            $table->enum('source_type', ['BRs', 'Branding', 'AGP'])->default('AGP');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_sources', function (Blueprint $table) {
            $table->dropColumn('source_type');
        });
    }
}
