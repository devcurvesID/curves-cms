<?php

use Illuminate\Database\Seeder;

class ThirtyDaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mst_thirty_days')->insert([
            ['menu' => 'Analisis Kebugaran Tubuh', 'sequence' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['menu' => 'Latihan Pertama Kali', 'sequence' => '2', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['menu' => '6 Latihan dalam 2 Minggu', 'sequence' => '3', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['menu' => 'Pertemuan 2 Minggu', 'sequence' => '4', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['menu' => 'Tantangan 30 Hari Selesai', 'sequence' => '5', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['menu' => 'Penimbangan & Pengukuran', 'sequence' => '6', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
