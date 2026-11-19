<?php

use Illuminate\Database\Seeder;

class MemberSourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('member_sources')->insert([
            ['source' => 'BR', 'source_type' => 'BRs', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['source' => 'Media Koran', 'source_type' => 'Branding', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['source' => 'Televisi', 'source_type' => 'Branding', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['source' => 'Internet (Situs Korporasi)', 'source_type' => 'Branding', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['source' => 'Papan Nama', 'source_type' => 'Branding', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['source' => 'Rekan Strategis', 'source_type' => 'Branding', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['source' => 'Branding / Merk Lainnya', 'source_type' => 'Branding', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['source' => 'Surat Langsung (Buku Telepon)', 'source_type' => 'AGP', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['source' => 'Brosur via Surat', 'source_type' => 'AGP', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['source' => 'Brosur Langsung', 'source_type' => 'AGP', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['source' => 'Mitra Komunitas (CP)', 'source_type' => 'AGP', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['source' => 'Guest Pass (Eksternal)', 'source_type' => 'AGP', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['source' => 'Guest Pass (Internal)', 'source_type' => 'AGP', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['source' => 'Facebook', 'source_type' => 'AGP', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['source' => 'Instagram', 'source_type' => 'AGP', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['source' => 'Whatsapp Atau Sosial Media Lainnya', 'source_type' => 'AGP', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')],
            ['source' => 'Leadbox', 'source_type' => 'AGP', 'created_at' => \Carbon\Carbon::now('Asia/Jakarta'), 'updated_at' => \Carbon\Carbon::now('Asia/Jakarta')]
        ]);
    }
}
