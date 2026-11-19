<?php

use Illuminate\Database\Seeder;

class COAUpdateCreditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('chart_of_accounts')->insert([
            ['coa_number' => '1-1211', 'coa_name' => 'Piutang Produk Investor', 'coa_abv_name' => 'Piutang Produk Investor', 'main_account_id' => '3', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'jubilant_id' => '1'],
            ['coa_number' => '1-1212', 'coa_name' => 'Piutang Royalti Investor', 'coa_abv_name' => 'Piutang Royalti Investor', 'main_account_id' => '3', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'jubilant_id' => '1'],
            ['coa_number' => '1-1213', 'coa_name' => 'Piutang Manage Service Investor', 'coa_abv_name' => 'Piutang Manage Service Investor', 'main_account_id' => '3', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'jubilant_id' => '1'],
            ['coa_number' => '1-1214', 'coa_name' => 'Piutang Marketing Brand Investor', 'coa_abv_name' => 'Piutang Marketing Brand Investor', 'main_account_id' => '3', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'jubilant_id' => '1'],
        ]);
    }
}
