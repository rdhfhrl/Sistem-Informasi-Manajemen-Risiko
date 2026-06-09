<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiskAnalysisSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Membuat data analisis risiko...');
        
        // Ambil semua risiko
        $risks = DB::table('risk')->get();
        
        if ($risks->isEmpty()) {
            $this->command->error('❌ Data risiko tidak ditemukan. Jalankan RiskSeeder terlebih dahulu.');
            return;
        }

        $analyses = [];
        
        foreach ($risks as $risk) {
            // Setiap risiko memiliki minimal 1 analisis (sama dengan data di tabel risk)
            // Untuk beberapa risiko tambahkan analisis sejarah
            
            // Analisis pertama (data dari tabel risk)
            $analysisDate1 = Carbon::parse($risk->last_analysis_date);
            $analyses[] = [
                'risk_analysis_risk_id' => $risk->risk_id,
                'likelihood_level' => $risk->likelihood_level,
                'impact_level' => $risk->impact_level,
                'risk_score' => $risk->risk_score,
                'risk_level' => $risk->risk_level,
                'analysis_date' => $analysisDate1->format('Y-m-d'),
                'created_at' => $analysisDate1,
                'updated_at' => $analysisDate1,
            ];
            
            // 30% risiko memiliki analisis kedua (sebelumnya)
            if (rand(1, 100) <= 30) {
                $analysisDate2 = $analysisDate1->copy()->subDays(rand(15, 60));
                $likelihood2 = max(1, min(5, $risk->likelihood_level + rand(-1, 1)));
                $impact2 = max(1, min(5, $risk->impact_level + rand(-1, 1)));
                $score2 = $likelihood2 * $impact2;
                $level2 = $this->calculateRiskLevel($score2);
                
                $analyses[] = [
                    'risk_analysis_risk_id' => $risk->risk_id,
                    'likelihood_level' => $likelihood2,
                    'impact_level' => $impact2,
                    'risk_score' => $score2,
                    'risk_level' => $level2,
                    'analysis_date' => $analysisDate2->format('Y-m-d'),
                    'created_at' => $analysisDate2,
                    'updated_at' => $analysisDate2,
                ];
            }
            
            // 10% risiko memiliki analisis ketiga
            if (rand(1, 100) <= 10) {
                $analysisDate3 = $analysisDate1->copy()->subDays(rand(90, 180));
                $likelihood3 = max(1, min(5, $risk->likelihood_level + rand(-2, 2)));
                $impact3 = max(1, min(5, $risk->impact_level + rand(-2, 2)));
                $score3 = $likelihood3 * $impact3;
                $level3 = $this->calculateRiskLevel($score3);
                
                $analyses[] = [
                    'risk_analysis_risk_id' => $risk->risk_id,
                    'likelihood_level' => $likelihood3,
                    'impact_level' => $impact3,
                    'risk_score' => $score3,
                    'risk_level' => $level3,
                    'analysis_date' => $analysisDate3->format('Y-m-d'),
                    'created_at' => $analysisDate3,
                    'updated_at' => $analysisDate3,
                ];
            }
        }
        
        // Insert data analisis
        DB::table('risk_analyses')->insert($analyses);
        
        $totalAnalyses = DB::table('risk_analyses')->count();
        $averagePerRisk = $totalAnalyses / $risks->count();
        
        $this->command->info("✓ {$totalAnalyses} data analisis risiko berhasil ditambahkan");
        $this->command->info("  - Rata-rata: " . round($averagePerRisk, 2) . " analisis per risiko");
    }
    
    private function calculateRiskLevel($score)
    {
        if ($score >= 20) return 'sangat_tinggi';
        if ($score >= 15) return 'tinggi';
        if ($score >= 10) return 'sedang';
        if ($score >= 5) return 'rendah';
        return 'sangat_rendah';
    }
}