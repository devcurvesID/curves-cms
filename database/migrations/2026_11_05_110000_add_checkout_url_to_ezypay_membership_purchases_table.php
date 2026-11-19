<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ezypay_membership_purchases', function (Blueprint $table) {
            $table->text('checkout_url')->nullable();
        });
    }

    public function down()
    {
        Schema::table('ezypay_membership_purchases', function (Blueprint $table) {
            $table->dropColumn('checkout_url');
        });
    }
};
