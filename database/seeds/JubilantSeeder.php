<?php

use Illuminate\Database\Seeder;

class JubilantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mst_jubilants')->insert([
            ['jubilant_name' => 'PT Unity Brands Indonesia', 'type' => 'Main', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')]
        ]);
    }
}
