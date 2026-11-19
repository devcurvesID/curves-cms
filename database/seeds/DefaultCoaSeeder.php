<?php

use Illuminate\Database\Seeder;

class DefaultCoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('main_accounts')->insert([
            ['id' => '22', 'account_number' => '0-0000', 'account_name' => 'Default', 'account_abv_name' => 'Default', 'account_category' => 'Header', 'account_type' => 'Aktiva', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);

        DB::table('chart_of_accounts')->insert([
            ['coa_number' => '0-0000', 'coa_name' => 'Default', 'coa_abv_name' => 'Default', 'main_account_id' => '22', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
