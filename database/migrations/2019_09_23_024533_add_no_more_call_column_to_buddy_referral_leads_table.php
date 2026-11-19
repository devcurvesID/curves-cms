<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNoMoreCallColumnToBuddyReferralLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buddy_referral_leads', function (Blueprint $table) {
            $table->boolean('no_more_call')->default(false);
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
            $table->dropColumn('no_more_call');
        });
    }
}
