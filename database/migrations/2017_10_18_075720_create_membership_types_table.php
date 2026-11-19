<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembershipTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('membership_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('membership_type_name')->unique();
            $table->enum('membership_type', ['Regular', 'Corporate', 'Student', 'Staff']);
            $table->enum('duration', ['1', '6', '12', '18']);
            $table->enum('payment_type', ['Cash', 'AD', 'Prepaid', 'Staff']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('membership_types');
    }
}
