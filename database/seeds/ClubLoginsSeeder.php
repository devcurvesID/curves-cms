<?php

use Illuminate\Database\Seeder;

class ClubLoginsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('club_logins')->insert([
            ['club_id' => '1', 'username' => 'admincurves', 'password' => '123456', 'encrypted_password' => '$2y$10$1rieo0hvWA1.Si07u1PraumZmiVfrb2SbGMzPWsOaDyViNkVvYRrm', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
