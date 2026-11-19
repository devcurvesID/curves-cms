<?php

use Illuminate\Database\Seeder;

class AccessMenusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mst_access_menus')->insert([
            ['key' => 'setting_club_choose', 'menu_description' => 'Hak Akses Memilih Klub selain Klub Utama', 'default_role' => '4', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key' => 'setting_staff_club_choose', 'menu_description' => 'Hak Akses Memilih Klub di Menu Staff', 'default_role' => '4', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key' => 'setting_product_type', 'menu_description' => 'Hak Akses Pengaturan Tipe Produk', 'default_role' => '3', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key' => 'setting_product', 'menu_description' => 'Hak Akses Pengaturan Produk (nama, kategori, harga patokan)', 'default_role' => '3', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key' => 'setting_inventory_club_choose', 'menu_description' => 'Hak Akses Memilih Klub di Menu Inventory', 'default_role' => '4', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['key' => 'setting_tax_type', 'menu_description' => 'Hak Akses Pengaturan Tipe Pajak', 'default_role' => '3', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
