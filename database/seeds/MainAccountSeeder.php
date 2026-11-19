<?php

use Illuminate\Database\Seeder;

class MainAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('main_accounts')->insert([
            ['account_number' => '1-0000', 'account_name' => 'Kas dan Bank', 'account_abv_name' => 'Kas dan Bank', 'account_category' => 'Header', 'account_type' => 'Aktiva', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '1-1190', 'account_name' => 'Ayat Silang', 'account_abv_name' => 'Ayat Silang', 'account_category' => 'Header', 'account_type' => 'Aktiva', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '1-1200', 'account_name' => 'Piutang', 'account_abv_name' => 'Piutang', 'account_category' => 'Header', 'account_type' => 'Aktiva', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '1-1300', 'account_name' => 'Persediaan Barang Dagang', 'account_abv_name' => 'Persediaan Barang Dagang', 'account_category' => 'Header', 'account_type' => 'Aktiva', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '1-1400', 'account_name' => 'Biaya Dibayar Dimuka', 'account_abv_name' => 'Biaya Dibayar Dimuka', 'account_category' => 'Header', 'account_type' => 'Aktiva', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '1-2000', 'account_name' => 'Aktiva Tetap', 'account_abv_name' => 'Aktiva Tetap', 'account_category' => 'Header', 'account_type' => 'Aktiva', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '1-2100', 'account_name' => 'Tanah', 'account_abv_name' => 'Tanah', 'account_category' => 'Header', 'account_type' => 'Aktiva', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '1-3000', 'account_name' => 'Akumulasi Penyusutan', 'account_abv_name' => 'Akumulasi Penyusutan', 'account_category' => 'Header', 'account_type' => 'Aktiva', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '1-4000', 'account_name' => 'Aktiva dalam Pembangunan', 'account_abv_name' => 'Aktiva dalam Pembangunan', 'account_category' => 'Header', 'account_type' => 'Aktiva', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '2-0000', 'account_name' => 'Hutang Dagang', 'account_abv_name' => 'Hutang Dagang', 'account_category' => 'Header', 'account_type' => 'Passiva', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '2-3000', 'account_name' => 'Biaya Yang Masih Harus Dibayar', 'account_abv_name' => 'Biaya Yang Masih Harus Dibayar', 'account_category' => 'Header', 'account_type' => 'Passiva', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '2-4000', 'account_name' => 'Hutang Lain-lain', 'account_abv_name' => 'Hutang Lain-lain', 'account_category' => 'Header', 'account_type' => 'Passiva', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '3-0000', 'account_name' => 'Modal', 'account_abv_name' => 'Modal', 'account_category' => 'Header', 'account_type' => 'Passiva', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '3-2000', 'account_name' => 'Laba Ditahan', 'account_abv_name' => 'Laba Ditahan', 'account_category' => 'Header', 'account_type' => 'Passiva', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '4-0000', 'account_name' => 'Pendapatan Usaha', 'account_abv_name' => 'Pendapatan Usaha', 'account_category' => 'Header', 'account_type' => 'Pendapatan', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '5-0000', 'account_name' => 'Harga Pokok Produksi', 'account_abv_name' => 'Harga Pokok Produksi', 'account_category' => 'Header', 'account_type' => 'Biaya', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '6-0000', 'account_name' => 'Beban Penjualan', 'account_abv_name' => 'Beban Penjualan', 'account_category' => 'Header', 'account_type' => 'Biaya', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '6-2000', 'account_name' => 'Beban Administrasi dan Umum', 'account_abv_name' => 'Beban Administrasi dan Umum', 'account_category' => 'Header', 'account_type' => 'Biaya', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '7-0000', 'account_name' => 'Pendapatan Lain-lain', 'account_abv_name' => 'Pendapatan Lain-lain', 'account_category' => 'Header', 'account_type' => 'Pendapatan', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '8-0000', 'account_name' => 'Beban Lain-lain', 'account_abv_name' => 'Beban Lain-lain', 'account_category' => 'Header', 'account_type' => 'Biaya', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['account_number' => '9-9999', 'account_name' => 'Income Summary', 'account_abv_name' => 'Income Summary', 'account_category' => 'Total', 'account_type' => 'Pendapatan', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
