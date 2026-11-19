<?php

use Illuminate\Database\Seeder;

class WeighAndMeasureAttributesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mst_weigh_measure_attributes')->insert([
            ['attribute_name' => 'Kolesterol Tinggi', 'description' => '', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['attribute_name' => 'Hidrasi', 'description' => '', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['attribute_name' => 'Metabolisme', 'description' => '', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['attribute_name' => 'Pola Makan', 'description' => '', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['attribute_name' => 'Nutrisi', 'description' => '', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['attribute_name' => 'Tidur', 'description' => '', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')]
        ]);
    }
}
