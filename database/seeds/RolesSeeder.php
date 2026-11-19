<?php

use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mst_roles')->insert([
            ['role' => 'CEO', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['role' => 'COO', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['role' => 'Country Director', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['role' => 'Club Manager', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['role' => 'Club Team Leader', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['role' => 'Coach', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['role' => 'Member', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['role' => 'Admin Staff', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
