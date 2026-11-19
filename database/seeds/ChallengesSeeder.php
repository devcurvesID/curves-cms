<?php

use Illuminate\Database\Seeder;

class ChallengesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('challenges')->insert([
            ['challenge' => '100x Workout', 'type' => 'workout', 'variable' => '100', 'sequence' => '1', 'variable_target' => '100', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['challenge' => '200x Workout', 'type' => 'workout', 'variable' => '200', 'sequence' => '2', 'variable_target' => '200', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['challenge' => '300x Workout', 'type' => 'workout', 'variable' => '300', 'sequence' => '3', 'variable_target' => '300', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['challenge' => '400x Workout', 'type' => 'workout', 'variable' => '400', 'sequence' => '4', 'variable_target' => '400', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['challenge' => '500x Workout', 'type' => 'workout', 'variable' => '500', 'sequence' => '5', 'variable_target' => '500', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['challenge' => '600x Workout', 'type' => 'workout', 'variable' => '600', 'sequence' => '6', 'variable_target' => '600', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['challenge' => '700x Workout', 'type' => 'workout', 'variable' => '700', 'sequence' => '7', 'variable_target' => '700', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['challenge' => '800x Workout', 'type' => 'workout', 'variable' => '800', 'sequence' => '8', 'variable_target' => '800', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['challenge' => '900x Workout', 'type' => 'workout', 'variable' => '900', 'sequence' => '9', 'variable_target' => '900', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['challenge' => '1000x Workout', 'type' => 'workout', 'variable' => '1000', 'sequence' => '10', 'variable_target' => '1000', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['challenge' => '30 Days Challenge', 'type' => 'day', 'variable' => '30', 'sequence' => '1', 'variable_target' => '8', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['challenge' => '60 Days Challenge', 'type' => 'day', 'variable' => '60', 'sequence' => '2', 'variable_target' => '16', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['challenge' => '90 Days Challenge', 'type' => 'day', 'variable' => '90', 'sequence' => '3', 'variable_target' => '24', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
