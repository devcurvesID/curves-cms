<?php

use Illuminate\Database\Seeder;

class ClubsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('club_infos')->insert([
            /*['club_name' => 'curves fitness', 'country_id' => '1', 'province_id' => '1', 'city_id' => '6', 'postal' => '77669', 'address' => 'Jln. Aceh Besar Raya No. 5', 'phones' => '123412234', 'status' => 'Active', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['club_name' => 'curves abc', 'country_id' => '1', 'province_id' => '1', 'city_id' => '3', 'postal' => '77668', 'address' => 'Jln. Timur No. 6', 'phones' => '123412235', 'status' => 'Active', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],*/
            ['club_name' => 'Curves Fitness', 'postal' => '40164', 'address' => 'jln. surya sumantri no. 76, lt. 2', 'city' => 'bandung city', 'province' => 'west java', 'country' => 'indonesia', 'phones' => '08112266977', 'email' => 'info@curvesindonesia.com', 'facebook' => 'CurvesIndonesia', 'status' => 'Active', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
