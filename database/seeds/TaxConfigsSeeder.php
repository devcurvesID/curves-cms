<?php

use Illuminate\Database\Seeder;

class TaxConfigsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tax_configs')->insert([
            ['club_id' => '1', 'tax_type_id' => '1', 'rate' => '10', 'from_date' => \Carbon\Carbon::now('Asia/Jakarta'), 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
