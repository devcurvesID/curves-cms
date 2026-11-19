<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('membership_types', 'plan_id_ezypay')) {
            Schema::table('membership_types', function (Blueprint $table) {
                $table->string('plan_id_ezypay')->nullable();
            });
        }
    }

    public function down()
    {
        // Preserve plan IDs, including columns that existed before this migration.
    }
};
