<?php

use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('questions')->insert([
            ['question' => 'Bagaimana latihan hari ini?', 'type' => 'Rating', 'rate_level' => '3', 'status' => 'Active', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['question' => 'Kebersihan klub?', 'type' => 'Rating', 'rate_level' => '5', 'status' => 'Active', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['question' => 'Kualitas coach hari ini?', 'type' => 'Rating', 'rate_level' => '5', 'status' => 'Active', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['question' => 'Masukan dan saran?', 'type' => 'Essay', 'rate_level' => '0', 'status' => 'Active', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
