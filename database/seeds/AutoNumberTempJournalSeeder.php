<?php

use Illuminate\Database\Seeder;

class AutoNumberTempJournalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('auto_numbers')->insert([
            ['name' => 'TempJournal', 'current_number' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
