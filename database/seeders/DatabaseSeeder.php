<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Memulai proses seeding database berdasarkan penelitian...');
        $this->command->info('📋 Urutan seeding mengikuti foreign key constraints');
        
        // Urutan PENTING untuk menjaga foreign key constraints
        $this->call([
            OrganizationSeeder::class,
            UserSeeder::class,
            RiskCategorySeeder::class,
            ProjectSeeder::class,
            StrategicObjectiveSeeder::class,
            BusinessProcessSeeder::class,
            RiskSeeder::class,             
            RiskIdentificationSeeder::class,
            RiskAnalysisSeeder::class,
            RiskEvaluationSeeder::class,
            RiskIndicatorSeeder::class,
            IndicatorMeasurementSeeder::class, 
            RiskMitigationSeeder::class,
            RiskMonitoringSeeder::class,
            AuditSeeder::class,
            ReportScheduleSeeder::class,    
            ReportSeeder::class,            
        ]);
        
        $this->command->info('✅ Semua seeder berhasil dijalankan!');
        $this->command->info('📊 Statistik akhir database:');
        
        // Tampilkan statistik lengkap
        $tables = [
            'organizations' => 'Organisasi',
            'users' => 'User',
            'project' => 'Proyek',
            'risk_categories' => 'Kategori Risiko',
            'strategic_objectives' => 'Tujuan Strategis',
            'business_processes' => 'Proses Bisnis',
            'risk' => 'Risiko',
            'risk_identifications' => 'Identifikasi Risiko',
            'risk_analyses' => 'Analisis Risiko',
            'risk_evaluations' => 'Evaluasi Risiko',
            'risk_indicators' => 'Indikator Risiko',
            'indicator_measurements' => 'Pengukuran Indikator',
            'risk_mitigations' => 'Rencana Mitigasi',
            'risk_monitorings' => 'Pemantauan Risiko',
            'audits' => 'Audit',
            'report_schedules' => 'Jadwal Laporan',
            'reports' => 'Laporan',
        ];
        
        foreach ($tables as $table => $label) {
            try {
                $count = \DB::table($table)->count();
                $this->command->info("  - {$label}: {$count}");
            } catch (\Exception $e) {
                $this->command->warn("  - {$label}: Tabel tidak ditemukan atau error");
            }
        }
        
        // Statistik khusus untuk risiko
        $riskStats = \DB::table('risk')
            ->select('risk_level', \DB::raw('COUNT(*) as count'))
            ->groupBy('risk_level')
            ->get()
            ->pluck('count', 'risk_level');
            
        $this->command->info('📈 Distribusi Level Risiko:');
        foreach (['sangat_rendah', 'rendah', 'sedang', 'tinggi', 'sangat_tinggi'] as $level) {
            $count = $riskStats[$level] ?? 0;
            $percentage = $riskStats->sum() > 0 ? round(($count / $riskStats->sum()) * 100, 1) : 0;
            $levelLabel = match($level) {
                'sangat_rendah' => 'Sangat Rendah',
                'rendah' => 'Rendah',
                'sedang' => 'Sedang',
                'tinggi' => 'Tinggi',
                'sangat_tinggi' => 'Sangat Tinggi',
                default => $level
            };
            $this->command->info("    • {$levelLabel}: {$count} ({$percentage}%)");
        }
        
        $this->command->info('🎉 Database siap digunakan dengan data penelitian!');
    }
}