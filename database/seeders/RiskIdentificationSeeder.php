<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiskIdentificationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Membuat data identifikasi risiko...');
        
        // Ambil semua risiko
        $risks = DB::table('risk')->get();
        
        if ($risks->isEmpty()) {
            $this->command->error('❌ Data risiko tidak ditemukan. Jalankan RiskSeeder terlebih dahulu.');
            return;
        }

        $identifications = [];
        $identificationTypes = [
            'loss_type' => ['Reputasi', 'Operasional', 'Kepatuhan', 'Lainnya'],
            'violation_type' => ['Hukum', 'SOP', 'Kontrak', 'Lainnya'],
            'failure_type' => ['Manusia', 'Proses', 'Sistem', 'Lainnya'],
            'error_type' => ['Human Error', 'Technical Error', 'Lainnya'],
        ];
        
        foreach ($risks as $risk) {
            // 80% risiko memiliki identifikasi
            if (rand(1, 100) <= 80) {
                $createdAt = Carbon::parse($risk->created_at)->addDays(rand(1, 3));
                
                $identifications[] = [
                    'risk_identification_risk_id' => $risk->risk_id,
                    'loss_type' => rand(0, 10) > 2 ? $identificationTypes['loss_type'][array_rand($identificationTypes['loss_type'])] : null,
                    'violation_type' => rand(0, 10) > 3 ? $identificationTypes['violation_type'][array_rand($identificationTypes['violation_type'])] : null,
                    'failure_type' => rand(0, 10) > 2 ? $identificationTypes['failure_type'][array_rand($identificationTypes['failure_type'])] : null,
                    'error_type' => rand(0, 10) > 3 ? $identificationTypes['error_type'][array_rand($identificationTypes['error_type'])] : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }
        }

        DB::table('risk_identifications')->insert($identifications);
        
        $totalIdentifications = DB::table('risk_identifications')->count();
        $this->command->info("✓ {$totalIdentifications} data identifikasi risiko berhasil dibuat");
        
        // Statistik
        $lossTypeCount = DB::table('risk_identifications')->whereNotNull('loss_type')->count();
        $violationTypeCount = DB::table('risk_identifications')->whereNotNull('violation_type')->count();
        
        $this->command->info("  - Dengan jenis kerugian: {$lossTypeCount}");
        $this->command->info("  - Dengan jenis pelanggaran: {$violationTypeCount}");
    }
}