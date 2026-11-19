<?php

use Illuminate\Database\Seeder;

class StaffManagementAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mst_access_menus')->insert([
            ['key' => 'setting_staff_management', 'menu_description' => 'Hak Akses Manajemen Staff', 'default_role' => '4', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);

        DB::table('user_access')->insert([
            ['user_id' => '1', 'menu_id' => '7', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
