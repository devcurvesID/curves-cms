<?php

use Illuminate\Database\Seeder;

class StaffsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            ['name' => 'Rey Skywalker', 'email' => 'rey@curves.com', 'password' => '$2y$10$aHRql1lpJDNsS56r7zC2P.j9eyYgVpJi/KF65DXuEKYQF8kj0E1qq', 'username' => 'reycurves', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'role_id' => '1', 'club_id' => '1']
        ]);
    }
}
