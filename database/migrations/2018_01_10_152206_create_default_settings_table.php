<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('default_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key_name')->unique();
            $table->string('value');
            $table->enum('type', ['club', 'staff', 'product']);
            $table->enum('category', ['open_hour', 'club_service_fee', 'tax_config']);
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
        Schema::dropIfExists('default_settings');
    }
}
