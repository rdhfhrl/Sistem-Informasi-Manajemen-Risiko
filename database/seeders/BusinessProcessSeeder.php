<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessProcessSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Membuat proses bisnis untuk UPTD Medan...');
        
        // Dapatkan UPTD Medan
        $uptd = DB::table('organizations')
            ->where('organization_type', 'UPTD')
            ->where('organization_name', 'LIKE', '%Medan%')
            ->first();
                
        if (!$uptd) {
            $this->command->error('❌ UPTD tidak ditemukan. Jalankan OrganizationSeeder terlebih dahulu.');
            return;
        }

        $processes = [
            [
                'business_process_name' => 'Perencanaan Teknis Proyek Jalan dan Jembatan',
                'business_process_description' => 'Proses perencanaan teknis termasuk desain, studi kelayakan, dan perencanaan anggaran'
            ],
            [
                'business_process_name' => 'Pengadaan Material dan Jasa Konstruksi',
                'business_process_description' => 'Proses pengadaan material konstruksi dan jasa kontraktor untuk proyek infrastruktur'
            ],
            [
                'business_process_name' => 'Pelaksanaan Konstruksi Jalan dan Jembatan',
                'business_process_description' => 'Proses pelaksanaan fisik pembangunan dan perbaikan infrastruktur'
            ],
            [
                'business_process_name' => 'Pengawasan dan Monitoring Proyek',
                'business_process_description' => 'Proses monitoring progress proyek dan evaluasi kinerja kontraktor'
            ],
            [
                'business_process_name' => 'Penerapan Sistem Manajemen Keselamatan Konstruksi (SMKK)',
                'business_process_description' => 'Proses implementasi dan monitoring keselamatan konstruksi berdasarkan RKK'
            ],
            [
                'business_process_name' => 'Pemeliharaan dan Perawatan Infrastruktur',
                'business_process_description' => 'Proses perawatan dan pemeliharaan jalan dan jembatan yang sudah dibangun'
            ],
        ];

        $processData = [];
        foreach ($processes as $process) {
            $processData[] = [
                'business_process_organization_id' => $uptd->organization_id,
                'business_process_name' => $process['business_process_name'],
                'business_process_description' => $process['business_process_description'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('business_processes')->insert($processData);
        
        $totalProcesses = DB::table('business_processes')->count();
        $this->command->info("✓ {$totalProcesses} proses bisnis berhasil dibuat untuk UPTD Medan");
    }
}