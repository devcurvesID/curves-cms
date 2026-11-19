<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSourceDetailToBuddyReferralLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buddy_referral_leads', function (Blueprint $table) {
            $table->string('source_detail')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('buddy_referral_leads', function (Blueprint $table) {
            $table->dropColumn('source_detail');
        });
    }
}
