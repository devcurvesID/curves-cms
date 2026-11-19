<?php

use Illuminate\Database\Seeder;

class AutoNumberMemorialJournalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('auto_numbers')->insert([
            ['name' => 'Memorial', 'current_number' => '1', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
