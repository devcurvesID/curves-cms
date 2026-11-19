<?php

use Illuminate\Database\Seeder;

class AddJubilantToClubInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('club_infos')->update([
            'jubilant_id' => 1,
        ]);
    }
}
