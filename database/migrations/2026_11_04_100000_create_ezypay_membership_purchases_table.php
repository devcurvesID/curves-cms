<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ezypay_membership_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id')->unique();
            $table->unsignedBigInteger('membership_type_id');
            $table->string('customer_id_ezypay');
            $table->string('plan_id_ezypay');
            $table->string('subscription_id_ezypay')->nullable();
            $table->string('status', 30);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ezypay_membership_purchases');
    }
};
