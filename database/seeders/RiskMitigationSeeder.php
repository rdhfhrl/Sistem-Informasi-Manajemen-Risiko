<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiskMitigationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Membuat data rencana mitigasi...');
        
        // Cek dulu apakah tabel risk sudah ada data
        $riskCount = DB::table('risk')->count();
        
        if ($riskCount == 0) {
            $this->command->error('❌ Data risiko tidak ditemukan. Jalankan RiskSeeder terlebih dahulu.');
            return;
        }
        
        // Ambil risiko dengan level tinggi dan sangat tinggi
        $highRisks = DB::table('risk')
            ->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
            ->get();
        
        // Juga ambil 15 risiko sedang
        $mediumRisks = DB::table('risk')
            ->where('risk_level', 'sedang')
            ->limit(15)
            ->get();
        
        $allRisks = $highRisks->merge($mediumRisks);
        
        if ($allRisks->isEmpty()) {
            $this->command->info('⚠️ Tidak ada risiko dengan level tinggi/sedang untuk dibuat mitigasi');
            return;
        }

        $mitigations = [];
        
        // Rencana mitigasi yang realistis sesuai kategori
        $mitigationPlans = [
            'Waktu' => [
                "Menyusun rencana kontingensi untuk menghadapi keterlambatan",
                "Mengoptimalkan schedule dengan metode critical path method",
                "Mengadakan rapat koordinasi rutin untuk memantau progress",
                "Menyiapkan buffer time untuk aktivitas kritis",
            ],
            'Lingkungan' => [
                "Mengimplementasikan sistem pengelolaan lingkungan",
                "Melakukan monitoring kualitas air dan udara rutin",
                "Menyusun rencana pengelolaan limbah konstruksi",
                "Melakukan revegetasi area yang terdampak",
            ],
            'Manajemen' => [
                "Meningkatkan frekuensi rapat koordinasi antar tim",
                "Mengembangkan sistem komunikasi terintegrasi",
                "Melakukan pelatihan manajemen proyek untuk tim",
                "Menerapkan sistem dokumentasi yang terstruktur",
            ],
            'Hukum' => [
                "Mengonsultasikan semua dokumen dengan legal department",
                "Melakukan due diligence terhadap semua pihak terkait",
                "Menyusun klausul perlindungan dalam kontrak",
                "Melakukan review regulasi secara berkala",
            ],
            'SDM' => [
                "Mengembangkan program pengembangan kompetensi",
                "Menyusun rencana suksesi untuk posisi kritis",
                "Menerapkan sistem reward and punishment yang jelas",
                "Melakukan rotasi pekerjaan untuk mengurangi kejenuhan",
            ],
            'K3' => [
                "Mengadakan safety induction secara rutin",
                "Menerapkan sistem permit to work untuk pekerjaan berisiko",
                "Melakukan inspeksi K3 harian dan mingguan",
                "Menyediakan APD yang sesuai standar",
            ],
        ];
        
        // Responsible parties berdasarkan data scan
        $responsibleParties = [
            "Aidul Akbar Lubis (Petugas K3)",
            "Elvida Medica S.M (Pelaksana)",
            "Indra Buradu (Mandor)",
            "Jhon Michael Sitepu (Wakil Direktur)",
            "Project Manager",
            "Site Manager",
            "Koordinator K3",
            "Supervisor Lapangan",
        ];
        
        // Status options dengan bobot
        $statusOptions = ['belum dimulai', 'dalam proses', 'selesai', 'ditunda', 'dibatalkan'];
        $statusWeights = [20, 40, 30, 5, 5]; // 40% dalam proses, 30% selesai
        
        foreach ($allRisks as $risk) {
            // Ambil kategori risiko
            $category = DB::table('risk_categories')
                ->where('risk_category_id', $risk->risk_category_id)
                ->first();
            
            $categoryName = $category ? $category->risk_category_name : 'K3';
            $categoryPlans = $mitigationPlans[$categoryName] ?? $mitigationPlans['K3'];
            
            // Setiap risiko tinggi memiliki 1-2 rencana mitigasi
            // Risiko sedang memiliki 0-1 rencana mitigasi
            $numMitigations = in_array($risk->risk_level, ['tinggi', 'sangat_tinggi']) 
                ? rand(1, 2) 
                : (rand(0, 10) > 5 ? 1 : 0);
            
            for ($i = 1; $i <= $numMitigations; $i++) {
                // Tentukan tanggal
                $createdAt = Carbon::parse($risk->created_at)->addDays(rand(7, 30));
                $deadline = $createdAt->copy()->addDays(rand(30, 120));
                
                // Tentukan status dengan bobot
                $status = $this->weightedRandom($statusOptions, $statusWeights);
                
                // Untuk status selesai, set updated_at mendekati deadline
                if ($status === 'selesai') {
                    $updatedAt = $deadline->copy()->subDays(rand(1, 7));
                } elseif ($status === 'dalam proses') {
                    $updatedAt = $createdAt->copy()->addDays(rand(10, 50));
                } else {
                    $updatedAt = $createdAt;
                }
                
                $mitigations[] = [
                    'risk_mitigation_risk_id' => $risk->risk_id,
                    'mitigation_plan' => $categoryPlans[array_rand($categoryPlans)],
                    'responsible_party' => $responsibleParties[array_rand($responsibleParties)],
                    'deadline' => $deadline->format('Y-m-d'),
                    'status' => $status,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ];
            }
        }
        
        if (empty($mitigations)) {
            $this->command->info('⚠️ Tidak ada data mitigasi yang dibuat');
            return;
        }
        
        try {
            // Insert data mitigasi
            DB::table('risk_mitigations')->insert($mitigations);
            
            $totalMitigations = DB::table('risk_mitigations')->count();
            
            // Statistik status - DENGAN QUERY YANG AMAN
            $statusStats = DB::table('risk_mitigations')
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get();
            
            $this->command->info("✓ {$totalMitigations} data rencana mitigasi berhasil ditambahkan");
            
            foreach ($statusStats as $stat) {
                $statusLabel = match($stat->status) {
                    'belum dimulai' => 'Belum Dimulai',
                    'dalam proses' => 'Dalam Proses',
                    'selesai' => 'Selesai',
                    'ditunda' => 'Ditunda',
                    'dibatalkan' => 'Dibatalkan',
                    default => $stat->status
                };
                $this->command->info("  - {$statusLabel}: {$stat->count}");
            }
            
        } catch (\Exception $e) {
            $this->command->error('❌ Error saat membuat data mitigasi: ' . $e->getMessage());
        }
    }
    
    private function weightedRandom($items, $weights) 
    {
        $total = array_sum($weights);
        $n = rand(1, $total);
        
        foreach ($weights as $i => $weight) {
            $n -= $weight;
            if ($n <= 0) {
                return $items[$i];
            }
        }
        
        return $items[0];
    }
}