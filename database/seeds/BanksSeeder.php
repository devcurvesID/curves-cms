<?php

use Illuminate\Database\Seeder;

class BanksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mst_banks')->insert([
            ['bank_name' => 'Default', 'bank_abv' => 'DEFAULT', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['bank_name' => 'Bank Central Asia', 'bank_abv' => 'BCA', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['bank_name' => 'Bank Negara Indonesia', 'bank_abv' => 'BNI', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['bank_name' => 'Mandiri', 'bank_abv' => 'Mandiri', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
