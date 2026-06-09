<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiskSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Membuat 50 data risiko berdasarkan penelitian...');
        
        // Ambil ID dari tabel referensi
        $organizations = DB::table('organizations')->where('organization_type', 'UPTD')->pluck('organization_id')->toArray();
        $projects = DB::table('project')->pluck('pro_id')->toArray();
        $objectives = DB::table('strategic_objectives')->pluck('strategic_objective_id')->toArray();
        $processes = DB::table('business_processes')->pluck('business_process_id')->toArray();
        $categories = DB::table('risk_categories')->pluck('risk_category_id')->toArray();
        $users = DB::table('users')->where('role', 'unit_pemilik_risiko')->pluck('id')->toArray();
        
        if (empty($organizations) || empty($projects) || empty($objectives) || empty($processes) || empty($categories) || empty($users)) {
            $this->command->error('❌ Data referensi tidak lengkap. Pastikan semua seeder telah dijalankan.');
            return;
        }

        $risks = [];
        $riskCounter = 1;
        
        // Jenis pekerjaan dari dokumen scan
        $workTypes = [
            'Pekerjaan Baja Tulangan (BJTS 420B)',
            'Galian Struktur dengan Kedalaman 0-2 meter',
            'Pemancangan Tiang Pancang Beton Pratekan',
            'Pembongkaran Beton',
            'Pekerjaan Drainase',
            'Pekerjaan Perkerasan Jalan',
            'Pekerjaan Jembatan',
        ];
        
        // Buat 50 data risiko (berdasarkan data penelitian)
        for ($i = 1; $i <= 50; $i++) {
            // Generate kode risiko
            $riskCode = 'RISK-' . str_pad($riskCounter, 4, '0', STR_PAD_LEFT);
            
            // Pilih data acak
            $organizationId = $organizations[array_rand($organizations)];
            $projectId = $projects[array_rand($projects)];
            $objectiveId = $objectives[array_rand($objectives)];
            $processId = $processes[array_rand($processes)];
            $categoryId = $categories[array_rand($categories)];
            $userId = $users[array_rand($users)];
            
            // Dapatkan nama kategori untuk deskripsi yang sesuai
            $category = DB::table('risk_categories')->where('risk_category_id', $categoryId)->first();
            $workType = $workTypes[array_rand($workTypes)];
            $description = $this->getRiskDescription($category->risk_category_name, $workType);
            
            // Buat tanggal acak dalam 2 tahun terakhir
            $createdAt = Carbon::now()->subMonths(rand(0, 24))->subDays(rand(1, 30));
            $updatedAt = $createdAt->copy()->addDays(rand(1, 30));
            
            // Tentukan status
            $statusOptions = ['active', 'monitoring', 'mitigated'];
            $weights = [40, 40, 20]; // 40% active, 40% monitoring, 20% mitigated
            $status = $this->weightedRandom($statusOptions, $weights);
            
            // Data analisis
            $likelihood = rand(1, 5);
            $impact = rand(1, 5);
            $riskScore = $likelihood * $impact;
            $riskLevel = $this->calculateRiskLevel($riskScore);
            $lastAnalysisDate = $createdAt->copy()->addDays(rand(1, 14))->format('Y-m-d');
            
            // Tanggal identifikasi (sebelum created_at)
            $identifiedAt = $createdAt->copy()->subDays(rand(1, 7));
            
            $risks[] = [
                'risk_code' => $riskCode,
                'risk_pro_id' => $projectId,
                'risk_organization_id' => $organizationId,
                'risk_strategic_objective_id' => $objectiveId,
                'risk_business_process_id' => $processId,
                'risk_category_id' => $categoryId,
                'risk_description' => $description,
                'risk_user_id' => $userId,
                'risk_level' => $riskLevel,
                'risk_score' => $riskScore,
                'likelihood_level' => $likelihood,
                'impact_level' => $impact,
                'last_analysis_date' => $lastAnalysisDate,
                'last_monitoring_date' => $status == 'monitoring' ? $createdAt->copy()->addDays(rand(15, 30))->format('Y-m-d') : null,
                'last_evaluation_date' => $status == 'mitigated' ? $createdAt->copy()->addDays(rand(20, 40))->format('Y-m-d') : null,
                'risk_status' => $status,
                'identified_at' => $identifiedAt,
                'identified_by' => $userId,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];
            
            $riskCounter++;
        }

        DB::table('risk')->insert($risks);
        
        $totalRisks = DB::table('risk')->count();
        $highRisk = DB::table('risk')->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])->count();
        $mediumRisk = DB::table('risk')->where('risk_level', 'sedang')->count();
        $lowRisk = DB::table('risk')->whereIn('risk_level', ['rendah', 'sangat_rendah'])->count();
        
        $this->command->info("✓ {$totalRisks} data risiko berhasil dibuat berdasarkan penelitian");
        $this->command->info("  - Risiko Tinggi/Sangat Tinggi: {$highRisk}");
        $this->command->info("  - Risiko Sedang: {$mediumRisk}");
        $this->command->info("  - Risiko Rendah/Sangat Rendah: {$lowRisk}");
    }
    
    private function getRiskDescription($category, $workType)
    {
        $descriptions = [
            'Waktu' => [
                "Keterlambatan penyelesaian {$workType} akibat cuaca ekstrem",
                "Penundaan pengiriman material untuk {$workType} dari supplier",
                "Ketidaksesuaian jadwal pelaksanaan {$workType} dengan rencana",
                "Terlambatnya persetujuan izin kerja untuk {$workType}",
                "Keterlambatan proses pengadaan material {$workType}",
            ],
            'Lingkungan' => [
                "Dampak pencemaran air akibat aktivitas {$workType}",
                "Kebisingan melebihi ambang batas selama {$workType}",
                "Gangguan terhadap sistem drainase alami saat {$workType}",
                "Potensi erosi tanah akibat kegiatan {$workType}",
                "Dampak visual negatif dari lokasi {$workType}",
            ],
            'Manajemen' => [
                "Kurangnya koordinasi antar tim proyek selama {$workType}",
                "Kesalahan dalam perencanaan anggaran untuk {$workType}",
                "Komunikasi yang tidak efektif dengan kontraktor {$workType}",
                "Ketidakjelasan dalam pembagian tugas tim {$workType}",
                "Keterlambatan pengambilan keputusan terkait {$workType}",
            ],
            'Hukum' => [
                "Pelanggaran peraturan perizinan konstruksi untuk {$workType}",
                "Sengketa lahan dengan pemilik tanah di lokasi {$workType}",
                "Ketidaksesuaian dengan spesifikasi teknis dalam kontrak {$workType}",
                "Potensi tuntutan hukum dari masyarakat sekitar lokasi {$workType}",
                "Ketidaklengkapan dokumen legal untuk pelaksanaan {$workType}",
            ],
            'SDM' => [
                "Turnover tenaga ahli di tengah pelaksanaan {$workType}",
                "Keterbatasan kompetensi tenaga kerja untuk {$workType}",
                "Ketidakhadiran pekerja kunci saat {$workType}",
                "Konflik internal antar pekerja {$workType}",
                "Kurangnya pengalaman tim dalam {$workType}",
            ],
            'K3' => [
                "Kecelakaan kerja akibat tidak menggunakan APD selama {$workType}",
                "Keruntuhan struktur sementara (scaffolding) pada {$workType}",
                "Paparan bahan kimia berbahaya saat {$workType}",
                "Potensi tertimbun material saat {$workType}",
                "Kecelakaan lalu lintas di area {$workType}",
            ],
        ];
        
        $categoryDescriptions = $descriptions[$category] ?? ["Risiko pada pekerjaan {$workType}"];
        return $categoryDescriptions[array_rand($categoryDescriptions)];
    }
    
    private function calculateRiskLevel($score)
    {
        if ($score >= 20) return 'sangat_tinggi';
        if ($score >= 15) return 'tinggi';
        if ($score >= 10) return 'sedang';
        if ($score >= 5) return 'rendah';
        return 'sangat_rendah';
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