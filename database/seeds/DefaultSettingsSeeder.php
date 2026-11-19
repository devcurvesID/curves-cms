<?php

use Illuminate\Database\Seeder;

class DefaultSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('default_settings')->insert([
            ['key_name' => 'start_day', 'value' => 'Monday', 'type' => 'club', 'category' => 'open_hour', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'end_day', 'value' => 'Friday', 'type' => 'club', 'category' => 'open_hour', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'open_hour', 'value' => '10:00', 'type' => 'club', 'category' => 'open_hour', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'closed_hour', 'value' => '13:00', 'type' => 'club', 'category' => 'open_hour', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_m2m_sf', 'value' => '1500000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_m2m_mf', 'value' => '1300000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_m2m_dc', 'value' => '0', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_m2m_dc_val', 'value' => '0', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_m2m_tax', 'value' => 'Yes', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_ad_sf', 'value' => '1500000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_ad_mf', 'value' => '690000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_ad_dc', 'value' => '0', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_ad_dc_val', 'value' => '0', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_ad_tax', 'value' => 'Yes', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_pp_sf', 'value' => '1500000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_pp_mf', 'value' => '590000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_pp_dc', 'value' => '66.67', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_pp_dc_val', 'value' => '1000000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_pp_tax', 'value' => 'Yes', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_pp_sf_18', 'value' => '1500000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_pp_mf_18', 'value' => '540000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_pp_dc_18', 'value' => '66.67', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_pp_dc_val_18', 'value' => '1000000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'regular_pp_tax_18', 'value' => 'Yes', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'student_pp_6_sf', 'value' => '1500000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'student_pp_6_mf', 'value' => '600000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'student_pp_6_dc', 'value' => '100', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'student_pp_6_dc_val', 'value' => '1500000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'student_pp_6_tax', 'value' => 'Yes', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'student_pp_12_sf', 'value' => '1500000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'student_pp_12_mf', 'value' => '500000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'student_pp_12_dc', 'value' => '100', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'student_pp_12_dc_val', 'value' => '1500000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'student_pp_12_tax', 'value' => 'Yes', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'corp_ad_sf', 'value' => '1500000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'corp_ad_mf', 'value' => '690000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'corp_ad_dc', 'value' => '66', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'corp_ad_dc_val', 'value' => '1000000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'corp_ad_tax', 'value' => 'Yes', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'corp_pp_sf', 'value' => '1500000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'corp_pp_mf', 'value' => '590000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'corp_pp_dc', 'value' => '66', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'corp_pp_dc_val', 'value' => '1000000', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'corp_pp_tax', 'value' => 'Yes', 'type' => 'club', 'category' => 'club_service_fee', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key_name' => 'ppn', 'value' => '10', 'type' => 'club', 'category' => 'tax_config', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')]
        ]);
    }
}
