<?php

use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('chart_of_accounts')->insert([

            //Kas dan Bank
            ['coa_number' => '1-1100', 'coa_name' => 'Kas', 'coa_abv_name' => 'Kas', 'main_account_id' => '1', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-1110', 'coa_name' => 'Bank IDR - BCA AC 2820408988', 'coa_abv_name' => 'Bank IDR - BCA AC 2820408988', 'main_account_id' => '1', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-1111', 'coa_name' => 'Bank IDR - BCA AC 2827535318', 'coa_abv_name' => 'Bank IDR - BCA AC 2827535318', 'main_account_id' => '1', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-1112', 'coa_name' => 'Bank IDR - BNI AC 5353181817', 'coa_abv_name' => 'Bank IDR - BNI AC 5353181817', 'main_account_id' => '1', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-1113', 'coa_name' => 'Bank IDR - Mandiri  AC 1300053531870', 'coa_abv_name' => 'Bank IDR - Mandiri  AC 1300053531870', 'main_account_id' => '1', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-1114', 'coa_name' => 'Bank USD - BCA', 'coa_abv_name' => 'Bank USD - BCA ', 'main_account_id' => '1', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],

            //Piutang
            ['coa_number' => '1-1210', 'coa_name' => 'Piutang Usaha', 'coa_abv_name' => 'Piutang Usaha', 'main_account_id' => '3', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-1220', 'coa_name' => 'Piutang Karyawan', 'coa_abv_name' => 'Piutang Karyawan ', 'main_account_id' => '3', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-1230', 'coa_name' => 'Piutang Pemegang Saham ', 'coa_abv_name' => 'Piutang Pemegang Saham', 'main_account_id' => '3', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-1240', 'coa_name' => 'Piutang Lain - Lain', 'coa_abv_name' => 'Piutang Lain - Lain', 'main_account_id' => '3', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-1250', 'coa_name' => 'Uang Muka Pembelian', 'coa_abv_name' => 'Uang Muka Pembelian', 'main_account_id' => '3', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-1260', 'coa_name' => 'Pendapatan Jasa di Terima di Muka ', 'coa_abv_name' => 'Pendapatan Jasa di Terima di Muka ', 'main_account_id' => '3', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],

            //Biaya Dibayar Dimuka
            ['coa_number' => '1-1410', 'coa_name' => 'Sewa Dibayar Dimuka', 'coa_abv_name' => 'Sewa Dibayar Dimuka', 'main_account_id' => '5', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-1420', 'coa_name' => 'Asuransi Dibayar Dimuka', 'coa_abv_name' => 'Asuransi Dibayar Dimuka', 'main_account_id' => '5', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-1430', 'coa_name' => 'PPN Masukan', 'coa_abv_name' => 'PPN Masukan', 'main_account_id' => '5', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-1440', 'coa_name' => 'PPN Keluaran', 'coa_abv_name' => 'PPN Keluaran', 'main_account_id' => '5', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-1450', 'coa_name' => 'PPN Yang Belum Dikreditkan', 'coa_abv_name' => 'PPN Yang Belum Dikreditkan', 'main_account_id' => '5', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-1460', 'coa_name' => 'Pajak Dibayar Dimuka PPH 25', 'coa_abv_name' => 'Pajak Dibayar Dimuka PPH 25', 'main_account_id' => '5', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-1470', 'coa_name' => 'Biaya Dibayar Dimuka', 'coa_abv_name' => 'Biaya Dibayar Dimuka', 'main_account_id' => '5', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],

            //Tanah
            ['coa_number' => '1-2200', 'coa_name' => 'Bangunan', 'coa_abv_name' => 'Bangunan', 'main_account_id' => '7', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-2300', 'coa_name' => 'Kendaraan', 'coa_abv_name' => 'Kendaraan', 'main_account_id' => '7', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-2400', 'coa_name' => 'Inventaris Kantor', 'coa_abv_name' => 'Inventaris Kantor', 'main_account_id' => '7', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-2500', 'coa_name' => 'Software', 'coa_abv_name' => 'Software', 'main_account_id' => '7', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-2600', 'coa_name' => 'Mesin', 'coa_abv_name' => 'Mesin', 'main_account_id' => '7', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],

            //Akumulasi Penyusutan
            ['coa_number' => '1-3100', 'coa_name' => 'Ak Penyusutan Bangunan', 'coa_abv_name' => 'Ak Penyusutan Bangunan', 'main_account_id' => '8', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-3200', 'coa_name' => 'Ak Penyusutan Kendaraan', 'coa_abv_name' => 'Ak Penyusutan Kendaraan', 'main_account_id' => '8', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-3300', 'coa_name' => 'Ak Penyusutan Inventaris Kantor', 'coa_abv_name' => 'Ak Penyusutan Inventaris Kantor', 'main_account_id' => '8', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '1-3400', 'coa_name' => 'Ak Amortisasi Mesin ', 'coa_abv_name' => 'Ak Amortisasi Mesin ', 'main_account_id' => '8', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],

            //Hutang Dagang
            ['coa_number' => '2-1000', 'coa_name' => 'Hutang Usaha', 'coa_abv_name' => 'Hutang Usaha', 'main_account_id' => '10', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '2-2000', 'coa_name' => 'Hutang Bank', 'coa_abv_name' => 'Hutang Bank', 'main_account_id' => '10', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '2-1100', 'coa_name' => 'Hutang Sewa ', 'coa_abv_name' => 'Hutang Sewa ', 'main_account_id' => '10', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],

            //Biaya Yang Masih Harus Dibayar
            ['coa_number' => '2-3100', 'coa_name' => 'Pajak YMH Dibayar - PPN', 'coa_abv_name' => 'Pajak YMH Dibayar - PPN', 'main_account_id' => '11', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '2-3200', 'coa_name' => 'Pajak YMH Dibayar - PPh 21', 'coa_abv_name' => 'Pajak YMH Dibayar - PPh 21', 'main_account_id' => '11', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '2-3300', 'coa_name' => 'Pajak YMH Dibayar - PPh 23', 'coa_abv_name' => 'Pajak YMH Dibayar - PPh 23', 'main_account_id' => '11', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '2-3400', 'coa_name' => 'Pajak YMH Dibayar - PPh 4(2)', 'coa_abv_name' => 'Pajak YMH Dibayar - PPh 4(2)', 'main_account_id' => '11', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '2-3500', 'coa_name' => 'Pajak YMH Dibayar - PPh 25', 'coa_abv_name' => 'Pajak YMH Dibayar - PPh 25', 'main_account_id' => '11', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '2-3600', 'coa_name' => 'Pajak YMH Dibayar - PPh 29', 'coa_abv_name' => 'Pajak YMH Dibayar - PPh 29', 'main_account_id' => '11', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '2-3700', 'coa_name' => 'Pajak YMH Dibayar - lain lain', 'coa_abv_name' => 'Pajak YMH Dibayar - lain lain', 'main_account_id' => '11', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '2-3800', 'coa_name' => 'Uang Muka Penjualan', 'coa_abv_name' => 'Uang Muka Penjualan', 'main_account_id' => '11', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '2-3900', 'coa_name' => 'By. Yg Masih Harus Dibayar', 'coa_abv_name' => 'By. Yg Masih Harus Dibayar', 'main_account_id' => '11', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],

            //Hutang Lain-Lain
            ['coa_number' => '2-4100', 'coa_name' => 'Hutang Pemegang Saham', 'coa_abv_name' => 'Hutang Pemegang Saham', 'main_account_id' => '12', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '2-4200', 'coa_name' => 'Hutang Lain - Lain', 'coa_abv_name' => 'Hutang Lain - Lain', 'main_account_id' => '12', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],

            //Modal
            ['coa_number' => '3-1000', 'coa_name' => 'Modal Saham', 'coa_abv_name' => 'Modal Saham', 'main_account_id' => '13', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],

            //Laba Ditahan
            ['coa_number' => '3-2100', 'coa_name' => 'R/E sampai dengan tahun lalu', 'coa_abv_name' => 'R/E sampai dengan tahun lalu', 'main_account_id' => '14', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '2-2200', 'coa_name' => 'R/E tahun berjalan', 'coa_abv_name' => 'R/E tahun berjalan', 'main_account_id' => '14', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],

            //Pendapatan Usaha
            ['coa_number' => '4-1000', 'coa_name' => 'Penjualan Jasa', 'coa_abv_name' => 'Penjualan Jasa', 'main_account_id' => '15', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '4-2000', 'coa_name' => 'Potongan Harga', 'coa_abv_name' => 'Potongan Harga', 'main_account_id' => '15', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '4-3000', 'coa_name' => 'Retur Penjualan', 'coa_abv_name' => 'Retur Penjualan', 'main_account_id' => '15', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '4-4000', 'coa_name' => 'Penjualan Produk', 'coa_abv_name' => 'Penjualan Produk', 'main_account_id' => '15', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '4-5000', 'coa_name' => 'Penjualan Sewa Loker ', 'coa_abv_name' => 'Penjualan Sewa Loker ', 'main_account_id' => '15', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '4-6000', 'coa_name' => 'Penjualan lainnya', 'coa_abv_name' => 'Penjualan lainnya', 'main_account_id' => '15', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],

            //Harga Pokok Produksi
            ['coa_number' => '5-1000', 'coa_name' => 'Kenaikan (Penurunan) Persediaan', 'coa_abv_name' => 'Kenaikan (Penurunan) Persediaan', 'main_account_id' => '16', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '5-2000', 'coa_name' => 'Beban Packing', 'coa_abv_name' => 'Beban Packing', 'main_account_id' => '16', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],

            //Beban Penjualan
            ['coa_number' => '6-1000', 'coa_name' => 'Beban Pemasaran', 'coa_abv_name' => 'Beban Pemasaran', 'main_account_id' => '17', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-1100', 'coa_name' => 'Beban Aksesoris', 'coa_abv_name' => 'Beban Aksesoris', 'main_account_id' => '17', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],

            //Beban Administrasi dan Umum
            ['coa_number' => '6-2100', 'coa_name' => 'Gaji', 'coa_abv_name' => 'Gaji', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2101', 'coa_name' => 'BPJS', 'coa_abv_name' => 'BPJS', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2102', 'coa_name' => 'Biaya Keperluan Kantor', 'coa_abv_name' => 'Biaya Keperluan Kantor', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2103', 'coa_name' => 'Beban Konsumsi', 'coa_abv_name' => 'Beban Konsumsi', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2104', 'coa_name' => 'Beban Listrik', 'coa_abv_name' => 'Beban Listrik', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2105', 'coa_name' => 'Beban Telephone / Fax / Hand Phone', 'coa_abv_name' => 'Beban Telephone / Fax / Hand Phone', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2106', 'coa_name' => 'Beban Internet', 'coa_abv_name' => 'Beban Internet', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2107', 'coa_name' => 'Beban Air', 'coa_abv_name' => 'Beban Air', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2108', 'coa_name' => 'Beban Bahan Bakar', 'coa_abv_name' => 'Beban Bahan Bakar', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2109', 'coa_name' => 'Beban Pemeliharaan Kendaraan', 'coa_abv_name' => 'Beban Pemeliharaan Kendaraan', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2110', 'coa_name' => 'Beban Pemeliharaan Peralatan', 'coa_abv_name' => 'Beban Pemeliharaan Peralatan', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2111', 'coa_name' => 'Beban Perjalanan Dinas', 'coa_abv_name' => 'Beban Perjalanan Dinas', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2112', 'coa_name' => 'Beban Pengiriman', 'coa_abv_name' => 'Beban Pengiriman', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2113', 'coa_name' => 'Beban ATK', 'coa_abv_name' => 'Beban ATK', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2114', 'coa_name' => 'Beban Asuransi', 'coa_abv_name' => 'Beban Asuransi', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2115', 'coa_name' => 'Beban Pajak Kendaraan', 'coa_abv_name' => 'Beban Pajak Kendaraan', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2116', 'coa_name' => 'Beban Administrasi Bank', 'coa_abv_name' => 'Beban Administrasi Bank', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2117', 'coa_name' => 'Beban Bunga Pinjaman Bank', 'coa_abv_name' => 'Beban Bunga Pinjaman Bank', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2118', 'coa_name' => 'Beban Perizinan', 'coa_abv_name' => 'Beban Perizinan', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2119', 'coa_name' => 'Jasa Konsultasi/Tenaga ahli', 'coa_abv_name' => 'Jasa Konsultasi/Tenaga ahli', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2120', 'coa_name' => 'Beban parkir, retribusi, tol, dll', 'coa_abv_name' => 'Beban parkir, retribusi, tol, dll', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2121', 'coa_name' => 'Beban Sewa', 'coa_abv_name' => 'Beban Sewa', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2122', 'coa_name' => 'Beban Iuran/Sumbangan', 'coa_abv_name' => 'Beban Iuran/Sumbangan', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2123', 'coa_name' => 'Beban Pajak', 'coa_abv_name' => 'Beban Pajak', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2124', 'coa_name' => 'Beban Penyusutan', 'coa_abv_name' => 'Beban Penyusutan', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2125', 'coa_name' => 'Beban Pelatihan dan Seminar', 'coa_abv_name' => 'Beban Pelatihan dan Seminar', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2126', 'coa_name' => 'Beban Teknologi Informasi', 'coa_abv_name' => 'Beban Teknologi Informasi', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2127', 'coa_name' => 'Beban Pemeliharaan Gedung', 'coa_abv_name' => 'Beban Pemeliharaan Gedung', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '6-2128', 'coa_name' => 'Beban Lain -Lain', 'coa_abv_name' => 'Beban Lain -Lain', 'main_account_id' => '18', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],

            //Pendapatan Lain-Lain
            ['coa_number' => '7-1000', 'coa_name' => 'Pendapatan Jasa Giro', 'coa_abv_name' => 'Pendapatan Jasa Giro', 'main_account_id' => '19', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['coa_number' => '7-2000', 'coa_name' => 'Pendapatan Lain - lain', 'coa_abv_name' => 'Pendapatan Lain - lain', 'main_account_id' => '19', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],

            //Beban Lain-Lain
            ['coa_number' => '8-1100', 'coa_name' => 'Pajak Bunga Bank', 'coa_abv_name' => 'Pajak Bunga Bank', 'main_account_id' => '20', 'user_id' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
