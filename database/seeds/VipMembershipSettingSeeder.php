<?php

use Illuminate\Database\Seeder;

class VipMembershipSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('membership_types')->insert([
            ['membership_type_name' => 'VIP Member', 'membership_type' => 'Regular', 'duration' => '1', 'payment_type' => 'Cash', 'is_vip' => 'vip', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);

        DB::table('club_service_fees')->insert([
            ['club_id' => '1', 'membership_type_id' => '10', 'sf_amount' => '0', 'sf_freq' => 'One Time', 'mf_amount' => '0', 'mf_freq' => 'Monthly', 'pre_pay_discount' => '0', 'pre_pay_discount_value' => '0', 'agreement_renewal_type_id' => '1', 'is_taxable' => 'No', 'from_date' => \Carbon\Carbon::now('Asia/Jakarta')->subDays(30), 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
