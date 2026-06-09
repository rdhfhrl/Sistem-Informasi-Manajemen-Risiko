<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiskMonitoringSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Membuat data pemantauan risiko...');
        
        // Ambil risiko yang sudah memiliki mitigasi
        $risks = DB::table('risk')
            ->whereIn('risk_id', function($query) {
                $query->select('risk_mitigation_risk_id')
                    ->from('risk_mitigations')
                    ->whereIn('status', ['dalam proses', 'selesai']);
            })
            ->get();
        
        if ($risks->isEmpty()) {
            $this->command->error('❌ Data risiko dengan mitigasi tidak ditemukan. Jalankan RiskMitigationSeeder terlebih dahulu.');
            return;
        }

        $monitorings = [];
        
        $monitoredByOptions = [
            'Aidul Akbar Lubis',
            'Elvida Medica S.',
            'Indra Zunaidi',
            'Jhon Michael Sitepu',
            'Tim Pengawas Proyek',
            'Petugas K3',
        ];
        
        $monitoringResults = [
            'Mitigasi berjalan sesuai rencana',
            'Ada kendala teknis yang perlu penanganan',
            'Perlu penyesuaian jadwal pelaksanaan',
            'Hasil sesuai ekspektasi',
            'Diperlukan tindakan korektif',
            'Progress sesuai target',
        ];
        
        $recommendations = [
            'Perlu penambahan sumber daya',
            'Monitoring lebih intensif diperlukan',
            'Koordinasi dengan kontraktor perlu ditingkatkan',
            'Dokumentasi perlu diperbaiki',
            'Pelaporan perlu lebih rinci',
            'Tidak ada rekomendasi khusus',
        ];
        
        foreach ($risks as $risk) {
            // Risiko tinggi: 2-4 pemantauan, sedang: 1-2 pemantauan
            $numMonitorings = in_array($risk->risk_level, ['tinggi', 'sangat_tinggi']) 
                ? rand(2, 4) 
                : rand(1, 2);
            
            $lastMonitoringDate = null;
            
            for ($i = 1; $i <= $numMonitorings; $i++) {
                if ($lastMonitoringDate) {
                    $monitoringDate = Carbon::parse($lastMonitoringDate)->addDays(rand(14, 30));
                } else {
                    // Pemantauan pertama sekitar 2-4 minggu setelah mitigasi dibuat
                    $firstMitigation = DB::table('risk_mitigations')
                        ->where('risk_mitigation_risk_id', $risk->risk_id)
                        ->orderBy('created_at')
                        ->first();
                    
                    $monitoringDate = $firstMitigation 
                        ? Carbon::parse($firstMitigation->created_at)->addDays(rand(14, 28))
                        : Carbon::parse($risk->created_at)->addDays(rand(15, 30));
                }
                
                // Skor risiko saat pemantauan (biasanya menurun seiring waktu)
                $currentRiskScore = max(1, $risk->risk_score - ($i * rand(1, 3)));
                $currentRiskLevel = $this->calculateRiskLevel($currentRiskScore);
                
                // Rating efektivitas (1-5)
                $effectivenessRating = rand(3, 5); // Biasanya cukup efektif sampai sangat efektif
                
                // Tanggal pemantauan berikutnya (untuk pemantauan terakhir, null)
                $nextMonitoringDate = $i < $numMonitorings 
                    ? $monitoringDate->copy()->addDays(rand(14, 30))
                    : null;
                
                $monitorings[] = [
                    'risk_monitoring_risk_id' => $risk->risk_id,
                    'monitoring_date' => $monitoringDate->format('Y-m-d'),
                    'current_risk_score' => $currentRiskScore,
                    'current_risk_level' => $currentRiskLevel,
                    'monitoring_result' => $monitoringResults[array_rand($monitoringResults)],
                    'monitoring_report' => 'Laporan pemantauan risiko ' . $risk->risk_code . ' tanggal ' . $monitoringDate->format('d F Y'),
                    'effectiveness_rating' => $effectivenessRating,
                    'monitored_by' => $monitoredByOptions[array_rand($monitoredByOptions)],
                    'next_monitoring_date' => $nextMonitoringDate?->format('Y-m-d'),
                    'recommendations' => $recommendations[array_rand($recommendations)],
                    'created_at' => $monitoringDate,
                    'updated_at' => $monitoringDate,
                ];
                
                $lastMonitoringDate = $monitoringDate->format('Y-m-d');
            }
        }
        
        // Insert data monitoring
        DB::table('risk_monitorings')->insert($monitorings);
        
        $totalMonitorings = DB::table('risk_monitorings')->count();
        $averageEffectiveness = DB::table('risk_monitorings')->avg('effectiveness_rating');
        
        $this->command->info("✓ {$totalMonitorings} data pemantauan risiko berhasil ditambahkan");
        $this->command->info("  - Rata-rata efektivitas: " . round($averageEffectiveness, 2) . "/5");
        
        // Update last_monitoring_date di tabel risk
        foreach ($risks as $risk) {
            $latestMonitoring = DB::table('risk_monitorings')
                ->where('risk_monitoring_risk_id', $risk->risk_id)
                ->orderBy('monitoring_date', 'desc')
                ->first();
            
            if ($latestMonitoring) {
                DB::table('risk')
                    ->where('risk_id', $risk->risk_id)
                    ->update([
                        'last_monitoring_date' => $latestMonitoring->monitoring_date,
                        'updated_at' => now(),
                    ]);
            }
        }
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