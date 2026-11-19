<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE member_payments DROP CONSTRAINT IF EXISTS member_payments_payment_status_check');
        DB::statement("ALTER TABLE member_payments ADD CONSTRAINT member_payments_payment_status_check CHECK (payment_status IN ('Unpaid', 'Halfunpaid', 'Halfpaid', 'Paid'))");
    }

    public function down()
    {
        DB::statement('ALTER TABLE member_payments DROP CONSTRAINT IF EXISTS member_payments_payment_status_check');
        DB::statement("ALTER TABLE member_payments ADD CONSTRAINT member_payments_payment_status_check CHECK (payment_status IN ('Unpaid', 'Halfpaid', 'Paid'))");
    }
};
