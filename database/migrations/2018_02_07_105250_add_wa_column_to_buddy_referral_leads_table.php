<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWaColumnToBuddyReferralLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buddy_referral_leads', function (Blueprint $table) {
            $table->string('wa_number', 20)->nullable();
            $table->string('email', 50)->nullable();
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
            $table->dropColumn('wa_number');
            $table->dropColumn('email');
        });
    }
}
