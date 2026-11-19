<?php

use Illuminate\Database\Seeder;

class PointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mst_points')->insert([
        	['name' => 'ST6 Lunas', 'type' => 'Membership', 'type_detail' => 'Lunas', 'target_point' => 'Staff','point' => 3, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'PP6 Lunas', 'type' => 'Membership', 'type_detail' => 'Lunas', 'target_point' => 'Staff' ,'point' => 4, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'ST12 Lunas', 'type' => 'Membership', 'type_detail' => 'Lunas', 'target_point' => 'Staff' ,'point' => 5, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'PP12 Lunas', 'type' => 'Membership', 'type_detail' => 'Lunas', 'target_point' => 'Staff' ,'point' => 6, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'PP18 Lunas', 'type' => 'Membership', 'type_detail' => 'Lunas', 'target_point' => 'Staff' ,'point' => 8, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'PP24 Lunas', 'type' => 'Membership', 'type_detail' => 'Lunas', 'target_point' => 'Staff' ,'point' => 10, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'FS Lunas', 'type' => 'Membership', 'type_detail' => 'Lunas', 'target_point' => 'Staff' ,'point' => 1, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'ST6 DP+Cicilan1', 'type' => 'Membership', 'type_detail' => 'DP+Cicilan1', 'target_point' => 'Staff' ,'point' => 2, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'PP6 DP+Cicilan1', 'type' => 'Membership', 'type_detail' => 'DP+Cicilan1', 'target_point' => 'Staff' ,'point' => 3, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'ST12 DP+Cicilan1', 'type' => 'Membership', 'type_detail' => 'DP+Cicilan1', 'target_point' => 'Staff' ,'point' => 3, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'PP12 DP+Cicilan1', 'type' => 'Membership', 'type_detail' => 'DP+Cicilan1', 'target_point' => 'Staff' ,'point' => 4, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'PP18 DP+Cicilan1', 'type' => 'Membership', 'type_detail' => 'DP+Cicilan1', 'target_point' => 'Staff' ,'point' => 5, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'PP24 DP+Cicilan1', 'type' => 'Membership', 'type_detail' => 'DP+Cicilan1', 'target_point' => 'Staff' ,'point' => 6, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'ST6 DP', 'type' => 'Membership', 'type_detail' => 'DP', 'target_point' => 'Staff' ,'point' => 1, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'PP6 DP', 'type' => 'Membership', 'type_detail' => 'DP', 'target_point' => 'Staff' ,'point' => 2, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'ST12 DP', 'type' => 'Membership', 'type_detail' => 'DP', 'target_point' => 'Staff' ,'point' => 1, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'PP12 DP', 'type' => 'Membership', 'type_detail' => 'DP', 'target_point' => 'Staff' ,'point' => 2, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'PP18 DP', 'type' => 'Membership', 'type_detail' => 'DP', 'target_point' => 'Staff' ,'point' => 2, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'PP24 DP', 'type' => 'Membership', 'type_detail' => 'DP', 'target_point' => 'Staff' ,'point' => 2, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'ST6 <DP', 'type' => 'Membership', 'type_detail' => '<DP', 'target_point' => 'Staff' ,'point' => 0, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'PP6 <DP', 'type' => 'Membership', 'type_detail' => '<DP', 'target_point' => 'Staff' ,'point' => 0, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'ST12 <DP', 'type' => 'Membership', 'type_detail' => '<DP', 'target_point' => 'Staff' ,'point' => 0, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'PP12 <DP', 'type' => 'Membership', 'type_detail' => '<DP', 'target_point' => 'Staff' ,'point' => 0, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'PP18 <DP', 'type' => 'Membership', 'type_detail' => '<DP', 'target_point' => 'Staff' ,'point' => 0, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'PP24 <DP', 'type' => 'Membership', 'type_detail' => '<DP', 'target_point' => 'Staff' ,'point' => 0, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'Make APO', 'type' => 'APO', 'type_detail' => 'APO', 'target_point' => 'Staff' ,'point' => 1, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'Make APO and Show Up', 'type' => 'APO', 'type_detail' => 'Show Up', 'target_point' => 'Staff' ,'point' => 5, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'BR', 'type' => 'BR', 'type_detail' => 'BR', 'target_point' => 'Staff' ,'point' => 1, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'BR and Show Up', 'type' => 'BR', 'type_detail' => 'BR Show Up', 'target_point' => 'Staff' ,'point' => 5, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'NN', 'type' => 'NN', 'type_detail' => 'NN', 'target_point' => 'Staff' ,'point' => 1, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'NN Active', 'type' => 'NN', 'type_detail' => 'Active', 'target_point' => 'Staff' ,'point' => 1, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'NN Reject, TA, SS', 'type' => 'NN', 'type_detail' => 'Reject, TA, SS', 'target_point' => 'Staff' ,'point' => 0, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'NN APO', 'type' => 'NN', 'type_detail' => 'APO', 'target_point' => 'Staff' ,'point' => 2, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'Flag A', 'type' => 'Flag', 'type_detail' => 'Flag A', 'target_point' => 'Staff' ,'point' => 10, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'Flag B', 'type' => 'Flag', 'type_detail' => 'Flag B', 'target_point' => 'Staff' ,'point' => 6, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'Flag C', 'type' => 'Flag', 'type_detail' => 'Flag C', 'target_point' => 'Staff' ,'point' => 3, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'Flag D', 'type' => 'Flag', 'type_detail' => 'Flag D', 'target_point' => 'Staff' ,'point' => 1, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'Flag E', 'type' => 'Flag', 'type_detail' => 'Flag E', 'target_point' => 'Staff' ,'point' => 0, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        	['name' => 'Point Untuk % Sales', 'type' => 'Sales', 'type_detail' => 'Point No Sales', 'target_point' => 'Staff' ,'point' => -2, 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
        ]);
    }
}
