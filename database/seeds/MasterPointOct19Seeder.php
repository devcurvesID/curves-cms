<?php

use Illuminate\Database\Seeder;

class MasterPointOct19Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mst_points')->insert([
            ['name' => 'Gloves', 'type' => 'Product', 'type_detail' => 'Gloves', 'target_point' => 'Staff','point' => 3, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['name' => 'Kaos Kaki', 'type' => 'Product', 'type_detail' => 'Kaos Kaki', 'target_point' => 'Staff','point' => 1, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['name' => 'Bra', 'type' => 'Product', 'type_detail' => 'Bra', 'target_point' => 'Staff','point' => 2, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['name' => 'Loker 6 Bulan', 'type' => 'Locker', 'type_detail' => '6 Months', 'target_point' => 'Staff','point' => 2, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['name' => 'Loker 12 Bulan', 'type' => 'Locker', 'type_detail' => '12 Months', 'target_point' => 'Staff','point' => 3, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['name' => 'Target NN', 'type' => 'Target', 'type_detail' => 'Target NN', 'target_point' => 'Staff','point' => 10, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['name' => 'Target APO', 'type' => 'Target', 'type_detail' => 'Target APO', 'target_point' => 'Staff','point' => 10, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['name' => 'Target APO Show Up', 'type' => 'Target', 'type_detail' => 'Target APO Show Up', 'target_point' => 'Staff','point' => 20, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['name' => 'Target Sales', 'type' => 'Target', 'type_detail' => 'Target Sales', 'target_point' => 'Staff','point' => 30, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['name' => 'Target WO', 'type' => 'Target', 'type_detail' => 'Target WO', 'target_point' => 'Staff','point' => 0, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['name' => 'Target WM', 'type' => 'Target', 'type_detail' => 'Target WM', 'target_point' => 'Staff','point' => 0, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
