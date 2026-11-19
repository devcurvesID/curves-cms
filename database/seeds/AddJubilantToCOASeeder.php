<?php

use Illuminate\Database\Seeder;

class AddJubilantToCOASeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('chart_of_accounts')->update([
            'jubilant_id' => 1,
        ]);
    }
}
