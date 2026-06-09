<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Membuat data proyek jalan & jembatan (UPTD Medan)...');

        // Ambil UPTD Medan
        $uptd = DB::table('organizations')
            ->where('organization_type', 'UPTD')
            ->where('organization_name', 'LIKE', '%Medan%')
            ->first();

        if (!$uptd) {
            $this->command->error('❌ UPTD Medan tidak ditemukan. Jalankan OrganizationSeeder terlebih dahulu.');
            return;
        }

        /**
         * Data proyek jalan & jembatan
         * Berbasis program PUPR Sumut 2022–2025
         * Digunakan sebagai data simulasi akademik
         */
        $projects = [
            [
                'pro_nama' => 'Peningkatan Jalan Provinsi Medan – Tanjung Morawa',
                'pro_lokasi' => 'Kota Medan – Kabupaten Deli Serdang',
                'pro_deskripsi' => 'Peningkatan struktur perkerasan jalan provinsi untuk mendukung konektivitas wilayah Medan dan sekitarnya',
                'pro_tanggal_mulai' => '2022-04-01',
                'pro_tanggal_selesai' => '2023-12-31',
                'pro_status' => 'Selesai',
            ],
            [
                'pro_nama' => 'Rehabilitasi Jembatan Sungai Deli',
                'pro_lokasi' => 'UPTD Medan',
                'pro_deskripsi' => 'Rehabilitasi struktur atas dan bawah jembatan Sungai Deli akibat penurunan kondisi teknis',
                'pro_tanggal_mulai' => '2022-07-15',
                'pro_tanggal_selesai' => '2023-08-15',
                'pro_status' => 'Selesai',
            ],
            [
                'pro_nama' => 'Pemeliharaan Berkala Jalan Provinsi Wilayah Medan',
                'pro_lokasi' => 'Beberapa ruas jalan provinsi wilayah Medan',
                'pro_deskripsi' => 'Pemeliharaan rutin dan berkala jalan provinsi meliputi penambalan, overlay, dan perbaikan drainase',
                'pro_tanggal_mulai' => '2023-03-01',
                'pro_tanggal_selesai' => '2024-03-31',
                'pro_status' => 'Selesai',
            ],
            [
                'pro_nama' => 'Pembangunan Jalan Akses Kawasan Strategis Medan Utara',
                'pro_lokasi' => 'Medan Utara',
                'pro_deskripsi' => 'Pembangunan dan peningkatan jalan akses menuju kawasan strategis dan logistik',
                'pro_tanggal_mulai' => '2023-09-01',
                'pro_tanggal_selesai' => '2024-12-31',
                'pro_status' => 'Aktif',
            ],
            [
                'pro_nama' => 'Penggantian Jembatan Titi Keramat di Kab. Deli Serdang',
                'pro_lokasi' => 'Kabupaten Deli Serdang',
                'pro_deskripsi' => 'Penggantian jembatan Titi Keramat berdasarkan kontrak No. 602/UPTD PUPR.MDN DPUPR/1569/2025. Waktu pelaksanaan: 110 Hari Kalender',
                'pro_tanggal_mulai' => '2025-09-10',
                'pro_tanggal_selesai' => '2026-01-10',
                'pro_status' => 'Aktif',
            ],
            [
                'pro_nama' => 'Peningkatan Jalan Provinsi Wilayah Medan (Program 2025)',
                'pro_lokasi' => 'UPTD Medan',
                'pro_deskripsi' => 'Peningkatan kualitas jalan provinsi sebagai bagian dari program infrastruktur strategis 2025',
                'pro_tanggal_mulai' => '2025-02-01',
                'pro_tanggal_selesai' => '2025-11-30',
                'pro_status' => 'Aktif',
            ],
            [
                'pro_nama' => 'Perbaikan Struktur Jembatan Provinsi Wilayah Medan',
                'pro_lokasi' => 'UPTD Medan',
                'pro_deskripsi' => 'Perbaikan dan penguatan struktur jembatan provinsi untuk menjamin keselamatan lalu lintas',
                'pro_tanggal_mulai' => '2025-03-01',
                'pro_tanggal_selesai' => '2025-10-31',
                'pro_status' => 'Ditunda',
            ],
        ];

        $projectData = [];
        foreach ($projects as $project) {
            $projectData[] = array_merge($project, [
                'created_at' => Carbon::parse($project['pro_tanggal_mulai'])->subDays(14),
                'updated_at' => now(),
            ]);
        }

        DB::table('project')->insert($projectData);

        // Statistik
        $this->command->info('✓ Seeder proyek berhasil dijalankan');
        $this->command->info('  - Total proyek   : ' . DB::table('project')->count());
        $this->command->info('  - Aktif          : ' . DB::table('project')->where('pro_status', 'Aktif')->count());
        $this->command->info('  - Selesai        : ' . DB::table('project')->where('pro_status', 'Selesai')->count());
        $this->command->info('  - Ditunda        : ' . DB::table('project')->where('pro_status', 'Ditunda')->count());
    }
}