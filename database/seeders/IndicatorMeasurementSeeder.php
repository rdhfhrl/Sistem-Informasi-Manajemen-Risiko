<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IndicatorMeasurementSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Membuat data pengukuran indikator...');
        
        // Dapatkan semua indikator
        $indicators = DB::table('risk_indicators')->get();
        
        if ($indicators->isEmpty()) {
            $this->command->error('❌ Data indikator tidak ditemukan. Jalankan RiskIndicatorSeeder terlebih dahulu.');
            return;
        }

        // Dapatkan beberapa user untuk measured_by
        $users = DB::table('users')
            ->where('role', 'unit_pemilik_risiko')
            ->limit(5)
            ->get()
            ->pluck('id')
            ->toArray();
            
        if (empty($users)) {
            $this->command->error('❌ User tidak ditemukan.');
            return;
        }

        $measurements = [];
        $now = Carbon::now();
        
        foreach ($indicators as $indicator) {
            // Setiap indikator memiliki 3-8 pengukuran dalam 6 bulan terakhir
            $numMeasurements = rand(3, 8);
            $lastMeasurementDate = null;
            
            for ($i = 0; $i < $numMeasurements; $i++) {
                if ($lastMeasurementDate) {
                    $measurementDate = Carbon::parse($lastMeasurementDate)->subDays(rand(7, 30));
                } else {
                    $measurementDate = $now->copy()->subDays(rand(0, 180));
                }
                
                // Nilai pengukuran sekitar threshold ± 20%
                $threshold = (float) $indicator->threshold;
                $variance = $threshold * (rand(-20, 20) / 100);
                $measuredValue = max(0.01, $threshold + $variance);
                $measuredValue = round($measuredValue, 2);
                
                // Notes berdasarkan apakah melebihi threshold
                $exceeds = $measuredValue > $threshold;
                $notes = $exceeds 
                    ? 'Nilai melebihi ambang batas. Perlu tindakan korektif.'
                    : 'Nilai dalam batas normal.';
                    
                // Tambahkan variasi notes
                $noteVariations = [
                    'Pengukuran rutin',
                    'Hasil monitoring mingguan',
                    'Pengecekan setelah tindakan korektif',
                    'Pengukuran khusus',
                    'Verifikasi data',
                ];
                
                if (rand(0, 10) > 3) {
                    $notes = $noteVariations[array_rand($noteVariations)] . '. ' . $notes;
                }
                
                $measurements[] = [
                    'risk_indicator_id' => $indicator->risk_indicator_id,
                    'measured_value' => $measuredValue,
                    'measurement_date' => $measurementDate->format('Y-m-d'),
                    'notes' => $notes,
                    'measured_by' => $users[array_rand($users)],
                    'created_at' => $measurementDate,
                    'updated_at' => $measurementDate,
                ];
                
                $lastMeasurementDate = $measurementDate->format('Y-m-d');
            }
        }

        DB::table('indicator_measurements')->insert($measurements);
        
        $totalMeasurements = DB::table('indicator_measurements')->count();
        
        // Hitung statistik
        $exceededCount = DB::table('indicator_measurements as m')
            ->join('risk_indicators as i', 'm.risk_indicator_id', '=', 'i.risk_indicator_id')
            ->whereRaw('m.measured_value > i.threshold')
            ->count();
            
        $averageMeasurements = $totalMeasurements / max(1, $indicators->count());
        
        $this->command->info("✓ {$totalMeasurements} data pengukuran indikator berhasil dibuat");
        $this->command->info("  - Rata-rata: " . round($averageMeasurements, 2) . " pengukuran per indikator");
        $this->command->info("  - Melebihi threshold: {$exceededCount} pengukuran");
        
        // Update indicator dengan data pengukuran terbaru
        foreach ($indicators as $indicator) {
            $latestMeasurement = DB::table('indicator_measurements')
                ->where('risk_indicator_id', $indicator->risk_indicator_id)
                ->orderBy('measurement_date', 'desc')
                ->first();
                
            if ($latestMeasurement) {
                // Bisa digunakan untuk update field current_value jika ada
                // (Tergantung struktur tabel yang sebenarnya)
            }
        }
    }
}