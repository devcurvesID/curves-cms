<?php

use Illuminate\Database\Seeder;

class MembershipTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('membership_types')->insert([
            ['membership_type_name' => 'Month to Month', 'membership_type' => 'Regular', 'duration' => '1', 'payment_type' => 'Cash', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['membership_type_name' => 'Reguler Auto Debet (12 Bulan)', 'membership_type' => 'Regular', 'duration' => '12', 'payment_type' => 'AD', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['membership_type_name' => 'Reguler Pre-Paid (12 Bulan)', 'membership_type' => 'Regular', 'duration' => '12', 'payment_type' => 'Prepaid', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['membership_type_name' => 'Reguler Pre-Paid (18 Bulan)', 'membership_type' => 'Regular', 'duration' => '18', 'payment_type' => 'Prepaid', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['membership_type_name' => 'Pelajar/Mahasiswa Pre-Paid (6 Bulan)', 'membership_type' => 'Student', 'duration' => '6', 'payment_type' => 'Prepaid', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['membership_type_name' => 'Pelajar/Mahasiswa Pre-Paid (12 Bulan)', 'membership_type' => 'Student', 'duration' => '12', 'payment_type' => 'Prepaid', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['membership_type_name' => 'Corporate Auto Debet (12 Bulan)', 'membership_type' => 'Corporate', 'duration' => '12', 'payment_type' => 'AD', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['membership_type_name' => 'Corporate Pre-Paid (12 Bulan)', 'membership_type' => 'Corporate', 'duration' => '12', 'payment_type' => 'Prepaid', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['membership_type_name' => 'Staff', 'membership_type' => 'Staff', 'duration' => '12', 'payment_type' => 'Staff', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
