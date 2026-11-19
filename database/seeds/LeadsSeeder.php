<?php

use Illuminate\Database\Seeder;

class LeadsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('buddy_referral_leads')->insert([
            ['fullname' => 'Maria Vania', 'phone' => '1234567', 'is_br' => 'NotBR', 'source_id' => '2', 'staff_id' => '1', 'status_gm' => 'Guest', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['fullname' => 'Daisy Ridley', 'phone' => '1234568', 'is_br' => 'NotBR', 'source_id' => '2', 'staff_id' => '1', 'status_gm' => 'Guest', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['fullname' => 'Gal Gadot', 'phone' => '1234569', 'is_br' => 'NotBR', 'source_id' => '2', 'staff_id' => '1', 'status_gm' => 'Guest', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')]
        ]);
    }
}
