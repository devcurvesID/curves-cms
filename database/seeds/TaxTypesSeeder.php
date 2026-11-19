<?php

use Illuminate\Database\Seeder;

class TaxTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tax_types')->insert([
            ['tax_type' => 'PPN', 'status' => 'Active', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
