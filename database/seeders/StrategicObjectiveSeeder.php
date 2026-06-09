<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StrategicObjectiveSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Membuat tujuan strategis untuk UPTD Medan...');
        
        // Dapatkan UPTD Medan
        $uptd = DB::table('organizations')
            ->where('organization_type', 'UPTD')
            ->where('organization_name', 'LIKE', '%Medan%')
            ->first();
            
        if (!$uptd) {
            $this->command->error('❌ UPTD Medan tidak ditemukan. Jalankan OrganizationSeeder terlebih dahulu.');
            return;
        }

        $objectives = [
            [
                'strategic_objective_name' => 'Meningkatkan Kualitas Infrastruktur Jalan dan Jembatan di Wilayah Medan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'strategic_objective_name' => 'Mengoptimalkan Penggunaan Anggaran Proyek Infrastruktur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'strategic_objective_name' => 'Meningkatkan Keselamatan Konstruksi dan Pengguna Jalan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'strategic_objective_name' => 'Mengurangi Dampak Lingkungan dari Kegiatan Konstruksi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'strategic_objective_name' => 'Meningkatkan Kapasitas Sumber Daya Manusia dalam Pengelolaan Proyek',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Tambahkan organization_id
        foreach ($objectives as &$objective) {
            $objective['strategic_objective_organization_id'] = $uptd->organization_id;
        }

        DB::table('strategic_objectives')->insert($objectives);
        
        $totalObjectives = DB::table('strategic_objectives')->count();
        $this->command->info("✓ {$totalObjectives} tujuan strategis berhasil dibuat untuk UPTD Medan");
    }
}