<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiskIndicatorSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Membuat data indikator risiko...');
        
        // Ambil risiko dengan level sedang ke atas
        $risks = DB::table('risk')
            ->whereIn('risk_level', ['sedang', 'tinggi', 'sangat_tinggi'])
            ->limit(30) // Hanya untuk 30 risiko teratas
            ->get();
        
        if ($risks->isEmpty()) {
            $this->command->error('❌ Data risiko tidak ditemukan. Jalankan RiskSeeder terlebih dahulu.');
            return;
        }

        $indicators = [];
        $indicatorTypes = ['akar_masalah', 'penyebab', 'dampak', 'lainnya'];
        
        $indicatorTemplates = [
            'akar_masalah' => [
                ['Kurangnya pengawasan lapangan', 'Tingkat pengawasan harian di bawah 80%'],
                ['Ketidakcukupan anggaran', 'Deviasi anggaran melebihi 15% dari rencana'],
                ['Kompetensi SDM rendah', 'Rasio pekerja bersertifikat di bawah 60%'],
            ],
            'penyebab' => [
                ['Curah hujan tinggi', 'Intensitas hujan > 50mm/jam'],
                ['Keterlambatan pengiriman material', 'Material terlambat > 7 hari'],
                ['Turnover tenaga kerja', 'Tingkat turnover > 20% per bulan'],
            ],
            'dampak' => [
                ['Keterlambatan proyek', 'Progress terlambat > 10% dari jadwal'],
                ['Biaya tambahan', 'Cost overrun > 15% dari anggaran'],
                ['Kualitas menurun', 'Hasil uji lab di bawah standar'],
            ],
        ];
        
        $units = ['%', 'hari', 'orang', 'mm', 'Rp', 'unit', 'kali'];
        
        foreach ($risks as $risk) {
            // 1-3 indikator per risiko
            $numIndicators = rand(1, 3);
            
            for ($i = 0; $i < $numIndicators; $i++) {
                $indicatorType = $indicatorTypes[array_rand($indicatorTypes)];
                $template = $indicatorTemplates[$indicatorType] ?? $indicatorTemplates['akar_masalah'];
                $selectedTemplate = $template[array_rand($template)];
                
                $createdAt = Carbon::parse($risk->created_at)->addDays(rand(5, 15));
                
                $indicators[] = [
                    'risk_indicator_risk_id' => $risk->risk_id,
                    'indicator_type' => $indicatorType,
                    'indicator_name' => $selectedTemplate[0],
                    'indicator_description' => $selectedTemplate[1],
                    'threshold' => rand(50, 100) + (rand(0, 99) / 100), // Nilai 50.00 - 100.99
                    'unit' => $units[array_rand($units)],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }
        }

        DB::table('risk_indicators')->insert($indicators);
        
        $totalIndicators = DB::table('risk_indicators')->count();
        
        // Statistik berdasarkan tipe
        $typeStats = DB::table('risk_indicators')
            ->select('indicator_type', DB::raw('COUNT(*) as count'))
            ->groupBy('indicator_type')
            ->get();
            
        $this->command->info("✓ {$totalIndicators} data indikator risiko berhasil dibuat");
        
        foreach ($typeStats as $stat) {
            $typeLabel = match($stat->indicator_type) {
                'akar_masalah' => 'Akar Masalah',
                'penyebab' => 'Penyebab',
                'dampak' => 'Dampak',
                'lainnya' => 'Lainnya',
                default => $stat->indicator_type
            };
            $this->command->info("  - {$typeLabel}: {$stat->count}");
        }
    }
}