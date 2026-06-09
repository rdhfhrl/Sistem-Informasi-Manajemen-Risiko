<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiskEvaluationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Membuat data evaluasi risiko...');
        
        // Ambil risiko dengan level sedang ke atas
        $risks = DB::table('risk')
            ->whereIn('risk_level', ['sedang', 'tinggi', 'sangat_tinggi'])
            ->get();
        
        if ($risks->isEmpty()) {
            $this->command->error('❌ Data risiko tidak ditemukan. Jalankan RiskSeeder terlebih dahulu.');
            return;
        }

        $evaluations = [];
        $priorityOptions = ['rendah', 'sedang', 'tinggi', 'sangat tinggi'];
        $mitigationDecisions = [
            'Diterima (Accept) - Risiko diterima tanpa tindakan khusus',
            'Ditransfer (Transfer) - Risiko dialihkan ke pihak lain',
            'Dimitigasi (Mitigate) - Dilakukan tindakan pengurangan risiko',
            'Dihindari (Avoid) - Aktivitas diubah untuk menghindari risiko',
            'Ditunda (Postpone) - Penanganan risiko ditunda',
        ];
        
        foreach ($risks as $risk) {
            // 70% risiko memiliki evaluasi
            if (rand(1, 100) <= 70) {
                $evaluationDate = Carbon::parse($risk->created_at)->addDays(rand(10, 30));
                $priority = $priorityOptions[array_rand($priorityOptions)];
                
                // Proyeksi skor risiko (biasanya lebih rendah dari skor saat ini)
                $projectedScore = max(1, $risk->risk_score - rand(0, 5));
                
                $evaluations[] = [
                    'risk_evaluation_risk_id' => $risk->risk_id,
                    'risk_evaluation_priority' => $priority,
                    'mitigation_decision' => $mitigationDecisions[array_rand($mitigationDecisions)],
                    'projected_risk_score' => $projectedScore,
                    'evaluation_date' => $evaluationDate->format('Y-m-d'),
                    'created_at' => $evaluationDate,
                    'updated_at' => $evaluationDate,
                ];
            }
        }

        DB::table('risk_evaluations')->insert($evaluations);
        
        $totalEvaluations = DB::table('risk_evaluations')->count();
        
        $this->command->info("✓ {$totalEvaluations} data evaluasi risiko berhasil dibuat");
        
        // Statistik berdasarkan prioritas
        $priorityStats = DB::table('risk_evaluations')
            ->select('risk_evaluation_priority', DB::raw('COUNT(*) as count'))
            ->groupBy('risk_evaluation_priority')
            ->get();
            
        foreach ($priorityStats as $stat) {
            $this->command->info("  - Prioritas {$stat->risk_evaluation_priority}: {$stat->count}");
        }
    }
}