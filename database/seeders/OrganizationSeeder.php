<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Membuat data organisasi berdasarkan penelitian...');
        
        // 1. Buat Dinas PUPR Provinsi Sumatera Utara
        $dinasPUPRId = DB::table('organizations')->insertGetId([
            'organization_name' => 'Dinas Pekerjaan Umum dan Penataan Ruang Provinsi Sumatera Utara',
            'organization_type' => 'Dinas',
            'organization_code' => 'DPUPR-SU',
            'location' => 'Jalan Busi No.7D, Kota Medan',
            'organization_description' => 'Dinas PUPR Provinsi Sumatera Utara sebagai induk organisasi pengelola infrastruktur',
            'parent_id' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✓ Dinas PUPR Provinsi Sumatera Utara berhasil dibuat (ID: ' . $dinasPUPRId . ')');

        // 2. Buat UPTD Medan berdasarkan data penelitian
        $uptdId = DB::table('organizations')->insertGetId([
            'organization_name' => 'UPTD PUPR Medan',
            'organization_type' => 'UPTD',
            'organization_code' => 'UPTD-MEDAN',
            'location' => 'Medan, Sumatera Utara',
            'organization_description' => 'Unit Pelaksana Teknis Daerah PUPR Kota Medan - Menangani proyek jalan dan jembatan',
            'parent_id' => $dinasPUPRId,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✓ UPTD PUPR Medan berhasil dibuat (ID: ' . $uptdId . ')');
        
        // Verifikasi
        $totalOrganizations = DB::table('organizations')->count();
        $this->command->info("✓ Total organisasi: {$totalOrganizations} (1 Dinas + 1 UPTD)");
    }
}