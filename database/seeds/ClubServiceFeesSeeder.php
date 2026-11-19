<?php

use Illuminate\Database\Seeder;

class ClubServiceFeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('club_service_fees')->insert([
            ['club_id' => '1', 'membership_type_id' => '1', 'sf_amount' => '1500000', 'sf_freq' => 'One Time', 'mf_amount' => '1300000', 'mf_freq' => 'Monthly', 'pre_pay_discount' => '0', 'pre_pay_discount_value' => '0', 'agreement_renewal_type_id' => '1', 'is_taxable' => 'Yes', 'from_date' => \Carbon\Carbon::now('Asia/Jakarta')->subDays(30), 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['club_id' => '1', 'membership_type_id' => '2', 'sf_amount' => '1500000', 'sf_freq' => 'One Time', 'mf_amount' => '690000', 'mf_freq' => 'Monthly', 'pre_pay_discount' => '0', 'pre_pay_discount_value' => '0', 'agreement_renewal_type_id' => '3', 'is_taxable' => 'Yes', 'from_date' => \Carbon\Carbon::now('Asia/Jakarta')->subDays(30), 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['club_id' => '1', 'membership_type_id' => '3', 'sf_amount' => '1500000', 'sf_freq' => 'One Time', 'mf_amount' => '590000', 'mf_freq' => 'Monthly', 'pre_pay_discount' => '66.7', 'pre_pay_discount_value' => '1000000', 'agreement_renewal_type_id' => '3', 'is_taxable' => 'Yes', 'from_date' => \Carbon\Carbon::now('Asia/Jakarta')->subDays(30), 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['club_id' => '1', 'membership_type_id' => '4', 'sf_amount' => '1500000', 'sf_freq' => 'One Time', 'mf_amount' => '540000', 'mf_freq' => 'Monthly', 'pre_pay_discount' => '66.7', 'pre_pay_discount_value' => '1000000', 'agreement_renewal_type_id' => '4', 'is_taxable' => 'Yes', 'from_date' => \Carbon\Carbon::now('Asia/Jakarta')->subDays(30), 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['club_id' => '1', 'membership_type_id' => '5', 'sf_amount' => '1500000', 'sf_freq' => 'One Time', 'mf_amount' => '600000', 'mf_freq' => 'Monthly', 'pre_pay_discount' => '100', 'pre_pay_discount_value' => '1500000', 'agreement_renewal_type_id' => '2', 'is_taxable' => 'Yes', 'from_date' => \Carbon\Carbon::now('Asia/Jakarta')->subDays(30), 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['club_id' => '1', 'membership_type_id' => '6', 'sf_amount' => '1500000', 'sf_freq' => 'One Time', 'mf_amount' => '500000', 'mf_freq' => 'Monthly', 'pre_pay_discount' => '100', 'pre_pay_discount_value' => '1500000', 'agreement_renewal_type_id' => '3', 'is_taxable' => 'Yes', 'from_date' => \Carbon\Carbon::now('Asia/Jakarta')->subDays(30), 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['club_id' => '1', 'membership_type_id' => '7', 'sf_amount' => '1500000', 'sf_freq' => 'One Time', 'mf_amount' => '690000', 'mf_freq' => 'Monthly', 'pre_pay_discount' => '66', 'pre_pay_discount_value' => '1000000', 'agreement_renewal_type_id' => '3', 'is_taxable' => 'Yes', 'from_date' => \Carbon\Carbon::now('Asia/Jakarta')->subDays(30), 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['club_id' => '1', 'membership_type_id' => '8', 'sf_amount' => '1500000', 'sf_freq' => 'One Time', 'mf_amount' => '590000', 'mf_freq' => 'Monthly', 'pre_pay_discount' => '66', 'pre_pay_discount_value' => '1000000', 'agreement_renewal_type_id' => '3', 'is_taxable' => 'Yes', 'from_date' => \Carbon\Carbon::now('Asia/Jakarta')->subDays(30), 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['club_id' => '1', 'membership_type_id' => '9', 'sf_amount' => '0', 'sf_freq' => 'One Time', 'mf_amount' => '0', 'mf_freq' => 'Monthly', 'pre_pay_discount' => '0', 'pre_pay_discount_value' => '0', 'agreement_renewal_type_id' => '1', 'is_taxable' => 'Yes', 'from_date' => \Carbon\Carbon::now('Asia/Jakarta')->subDays(30), 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
