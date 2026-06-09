<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Membuat data laporan...');
        
        // Dapatkan data referensi
        $uptd = DB::table('organizations')
            ->where('organization_type', 'UPTD')
            ->where('organization_name', 'LIKE', '%Medan%')
            ->first();
            
        $admin = DB::table('users')
            ->where('role', 'admin')
            ->first();
            
        $projects = DB::table('project')
            ->where('pro_status', 'Aktif')
            ->limit(3)
            ->get();
            
        $schedules = DB::table('report_schedules')->get();
        
        if (!$uptd || !$admin || $projects->isEmpty() || $schedules->isEmpty()) {
            $this->command->error('❌ Data referensi tidak lengkap.');
            return;
        }

        $reports = [];
        
        // Data laporan untuk 6 bulan terakhir
        $now = Carbon::now();
        $reportTypes = ['monitoring', 'risk_profile', 'executive_summary', 'mitigation_effectiveness'];
        $periods = ['bulanan', 'triwulan', 'tahunan'];
        $statuses = ['draft', 'generated', 'published', 'archived'];
        $statusWeights = [10, 30, 50, 10]; // 50% published, 30% generated, 10% draft, 10% archived
        
        for ($i = 0; $i < 6; $i++) {
            $reportDate = $now->copy()->subMonths($i);
            
            foreach ($reportTypes as $reportType) {
                // Tentukan status dengan bobot
                $status = $this->weightedRandom($statuses, $statusWeights);
                
                // Pilih periode yang sesuai
                $period = $periods[array_rand($periods)];
                
                // Pilih project acak (kadang null untuk laporan organisasi)
                $project = rand(0, 10) > 3 ? $projects->random() : null;
                
                // Pilih schedule acak
                $schedule = $schedules->where('report_type', $reportType)->first();
                
                // Buat title
                $title = $this->generateReportTitle($reportType, $period, $reportDate);
                
                // Generate data laporan
                $reportData = $this->generateReportData($reportType, $period, $uptd->organization_id, $project?->pro_id);
                
                // Approved by untuk laporan yang published
                $approvedBy = $status === 'published' ? $admin->id : null;
                $approvalDate = $status === 'published' ? $reportDate->copy()->addDays(rand(1, 5)) : null;
                
                $reports[] = [
                    'report_type' => $reportType,
                    'title' => $title,
                    'period' => $period,
                    'report_date' => $reportDate->format('Y-m-d'),
                    'organization_id' => $uptd->organization_id,
                    'project_id' => $project?->pro_id,
                    'risk_id' => null,
                    'schedule_id' => $schedule?->schedule_id,
                    'data' => json_encode($reportData),
                    'file_path' => $status !== 'draft' ? 'reports/pdf/' . $this->generateFileName($title) : null,
                    'status' => $status,
                    'generated_by' => $admin->id,
                    'approved_by' => $approvedBy,
                    'approval_date' => $approvalDate?->format('Y-m-d'),
                    'notes' => $this->generateNotes($status, $reportType),
                    'created_at' => $reportDate,
                    'updated_at' => $status === 'published' ? $approvalDate : $reportDate,
                ];
            }
        }

        // Tambahkan beberapa laporan khusus proyek
        foreach ($projects as $project) {
            $reportDate = $now->copy()->subMonths(rand(1, 3));
            
            $reports[] = [
                'report_type' => 'monitoring',
                'title' => 'Laporan Pemantauan Risiko Proyek ' . $project->pro_nama . ' - ' . $reportDate->format('F Y'),
                'period' => 'bulanan',
                'report_date' => $reportDate->format('Y-m-d'),
                'organization_id' => $uptd->organization_id,
                'project_id' => $project->pro_id,
                'risk_id' => null,
                'schedule_id' => null,
                'data' => json_encode($this->generateProjectReportData($project->pro_id)),
                'file_path' => 'reports/pdf/' . $this->generateFileName('Laporan Proyek ' . $project->pro_nama),
                'status' => 'published',
                'generated_by' => $admin->id,
                'approved_by' => $admin->id,
                'approval_date' => $reportDate->copy()->addDays(3)->format('Y-m-d'),
                'notes' => 'Laporan pemantauan risiko khusus proyek ' . $project->pro_nama,
                'created_at' => $reportDate,
                'updated_at' => $reportDate->copy()->addDays(3),
            ];
        }

        DB::table('reports')->insert($reports);
        
        $totalReports = DB::table('reports')->count();
        $publishedReports = DB::table('reports')->where('status', 'published')->count();
        $scheduledReports = DB::table('reports')->whereNotNull('schedule_id')->count();
        
        $this->command->info("✓ {$totalReports} data laporan berhasil dibuat");
        $this->command->info("  - Dipublikasikan: {$publishedReports}");
        $this->command->info("  - Dari jadwal: {$scheduledReports}");
        
        // Statistik berdasarkan tipe
        $typeStats = DB::table('reports')
            ->select('report_type', DB::raw('COUNT(*) as count'))
            ->groupBy('report_type')
            ->get();
            
        foreach ($typeStats as $stat) {
            $typeLabel = match($stat->report_type) {
                'monitoring' => 'Pemantauan',
                'risk_profile' => 'Profil Risiko',
                'executive_summary' => 'Ringkasan Eksekutif',
                'mitigation_effectiveness' => 'Efektivitas Mitigasi',
                default => $stat->report_type
            };
            $this->command->info("  - {$typeLabel}: {$stat->count}");
        }
    }
    
    private function generateReportTitle($reportType, $period, $date)
    {
        $typeLabels = [
            'monitoring' => 'Laporan Pemantauan Risiko',
            'risk_profile' => 'Profil Risiko',
            'executive_summary' => 'Ringkasan Eksekutif',
            'mitigation_effectiveness' => 'Laporan Efektivitas Mitigasi',
        ];
        
        $periodLabels = [
            'bulanan' => 'Bulanan',
            'triwulan' => 'Triwulan',
            'tahunan' => 'Tahunan',
        ];
        
        $type = $typeLabels[$reportType] ?? $reportType;
        $periodLabel = $periodLabels[$period] ?? $period;
        
        return "{$type} {$periodLabel} - " . $date->format('F Y');
    }
    
    private function generateFileName($title)
    {
        $cleanTitle = preg_replace('/[^A-Za-z0-9\-]/', '-', $title);
        $cleanTitle = preg_replace('/-+/', '-', $cleanTitle);
        
        return 'report-' . strtolower($cleanTitle) . '-' . date('Ymd-His') . '.pdf';
    }
    
    private function generateNotes($status, $reportType)
    {
        $notes = [];
        
        if ($status === 'published') {
            $notes[] = 'Laporan telah disetujui dan dipublikasikan.';
        } elseif ($status === 'generated') {
            $notes[] = 'Laporan telah digenerate, menunggu persetujuan.';
        } else {
            $notes[] = 'Laporan dalam status draft.';
        }
        
        $typeNotes = [
            'monitoring' => 'Berisi hasil pemantauan risiko selama periode tertentu.',
            'risk_profile' => 'Menampilkan profil dan distribusi risiko.',
            'executive_summary' => 'Ringkasan untuk manajemen puncak.',
            'mitigation_effectiveness' => 'Analisis efektivitas tindakan mitigasi.',
        ];
        
        $notes[] = $typeNotes[$reportType] ?? 'Laporan manajemen risiko.';
        
        return implode(' ', $notes);
    }
    
    private function generateReportData($reportType, $period, $organizationId, $projectId = null)
    {
        // Hitung statistik dari database yang sebenarnya
        $totalRisks = DB::table('risk')
            ->where('risk_organization_id', $organizationId)
            ->when($projectId, function($query, $projectId) {
                return $query->where('risk_pro_id', $projectId);
            })
            ->count();
            
        $highRisks = DB::table('risk')
            ->where('risk_organization_id', $organizationId)
            ->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
            ->when($projectId, function($query, $projectId) {
                return $query->where('risk_pro_id', $projectId);
            })
            ->count();
            
        $completedMitigations = DB::table('risk_mitigations')
            ->where('status', 'selesai')
            ->whereExists(function ($query) use ($organizationId, $projectId) {
                $query->select(DB::raw(1))
                    ->from('risk')
                    ->whereColumn('risk.risk_id', 'risk_mitigations.risk_mitigation_risk_id')
                    ->where('risk.risk_organization_id', $organizationId);
                if ($projectId) {
                    $query->where('risk.risk_pro_id', $projectId);
                }
            })
            ->count();
            
        $totalMitigations = DB::table('risk_mitigations')
            ->whereExists(function ($query) use ($organizationId, $projectId) {
                $query->select(DB::raw(1))
                    ->from('risk')
                    ->whereColumn('risk.risk_id', 'risk_mitigations.risk_mitigation_risk_id')
                    ->where('risk.risk_organization_id', $organizationId);
                if ($projectId) {
                    $query->where('risk.risk_pro_id', $projectId);
                }
            })
            ->count();
            
        $completionRate = $totalMitigations > 0 ? round(($completedMitigations / $totalMitigations) * 100, 2) : 0;
        
        // Data dasar untuk semua jenis laporan
        $baseData = [
            'metadata' => [
                'generated_at' => now()->toDateTimeString(),
                'period' => $period,
                'total_risks' => $totalRisks,
                'high_risk_count' => $highRisks,
                'mitigation_completion_rate' => $completionRate,
            ],
            'summary' => [
                'total_risks' => $totalRisks,
                'high_risk_count' => $highRisks,
                'average_risk_score' => rand(8, 15) + (rand(0, 99) / 100),
            ],
        ];
        
        // Tambahkan data khusus berdasarkan jenis laporan
        switch ($reportType) {
            case 'monitoring':
                $baseData['monitoring_activities'] = [
                    'total_monitorings' => rand(15, 40),
                    'recent_monitorings' => rand(5, 12),
                    'effectiveness_rating' => rand(70, 95) + (rand(0, 99) / 100),
                ];
                break;
                
            case 'risk_profile':
                $baseData['distribution'] = [
                    'by_level' => [
                        'sangat_rendah' => rand(5, 10),
                        'rendah' => rand(8, 15),
                        'sedang' => rand(10, 20),
                        'tinggi' => rand(3, 8),
                        'sangat_tinggi' => rand(1, 4),
                    ],
                    'by_category' => [
                        'Waktu' => rand(5, 12),
                        'Lingkungan' => rand(3, 8),
                        'Manajemen' => rand(6, 10),
                        'Hukum' => rand(2, 6),
                        'SDM' => rand(4, 9),
                        'K3' => rand(7, 14),
                    ],
                ];
                break;
                
            case 'executive_summary':
                $baseData['key_metrics'] = [
                    'risk_exposure' => rand(10, 18) + (rand(0, 99) / 100),
                    'mitigation_completion_rate' => $completionRate,
                    'monitoring_coverage' => rand(75, 95) + (rand(0, 99) / 100),
                    'risk_reduction' => rand(15, 30) + (rand(0, 99) / 100),
                ];
                $baseData['critical_risks'] = $this->generateCriticalRisksData(3);
                break;
                
            case 'mitigation_effectiveness':
                $baseData['effectiveness_metrics'] = [
                    'completion_rate' => $completionRate,
                    'on_time_completion' => rand(65, 90) + (rand(0, 99) / 100),
                    'budget_variance' => rand(-5, 5) + (rand(0, 99) / 100),
                    'risk_reduction_effectiveness' => rand(60, 85) + (rand(0, 99) / 100),
                ];
                break;
        }
        
        return $baseData;
    }
    
    private function generateProjectReportData($projectId)
    {
        // Ambil data proyek
        $project = DB::table('project')->where('pro_id', $projectId)->first();
        
        // Hitung statistik untuk proyek
        $projectRisks = DB::table('risk')->where('risk_pro_id', $projectId)->count();
        $projectHighRisks = DB::table('risk')
            ->where('risk_pro_id', $projectId)
            ->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
            ->count();
            
        $projectMitigations = DB::table('risk_mitigations')
            ->whereExists(function ($query) use ($projectId) {
                $query->select(DB::raw(1))
                    ->from('risk')
                    ->whereColumn('risk.risk_id', 'risk_mitigations.risk_mitigation_risk_id')
                    ->where('risk.risk_pro_id', $projectId);
            })
            ->count();
            
        $completedProjectMitigations = DB::table('risk_mitigations')
            ->where('status', 'selesai')
            ->whereExists(function ($query) use ($projectId) {
                $query->select(DB::raw(1))
                    ->from('risk')
                    ->whereColumn('risk.risk_id', 'risk_mitigations.risk_mitigation_risk_id')
                    ->where('risk.risk_pro_id', $projectId);
            })
            ->count();
            
        $completionRate = $projectMitigations > 0 ? round(($completedProjectMitigations / $projectMitigations) * 100, 2) : 0;
        
        return [
            'metadata' => [
                'project_name' => $project->pro_nama ?? 'Proyek',
                'project_location' => $project->pro_lokasi ?? '-',
                'project_status' => $project->pro_status ?? '-',
                'generated_at' => now()->toDateTimeString(),
                'period' => 'bulanan',
            ],
            'project_summary' => [
                'total_risks' => $projectRisks,
                'high_risks' => $projectHighRisks,
                'total_mitigations' => $projectMitigations,
                'completed_mitigations' => $completedProjectMitigations,
                'completion_rate' => $completionRate,
                'average_risk_score' => rand(8, 18) + (rand(0, 99) / 100),
            ],
            'risk_distribution' => [
                'by_category' => [
                    'Waktu' => rand(1, 5),
                    'Lingkungan' => rand(1, 4),
                    'Manajemen' => rand(2, 6),
                    'Hukum' => rand(0, 3),
                    'SDM' => rand(1, 4),
                    'K3' => rand(2, 7),
                ],
            ],
            'top_risks' => $this->generateCriticalRisksData(5),
            'recommendations' => [
                'Tingkatkan monitoring untuk risiko tinggi',
                'Percepat penyelesaian mitigasi',
                'Koordinasi dengan kontraktor perlu ditingkatkan',
            ],
        ];
    }
    
    private function generateCriticalRisksData($count)
    {
        $risks = [];
        $riskDescriptions = [
            'Keterlambatan pengiriman material konstruksi',
            'Potensi kecelakaan kerja di area galian',
            'Ketidaksesuaian spesifikasi teknis',
            'Keterlambatan persetujuan izin kerja',
            'Kondisi cuaca ekstrem mengganggu jadwal',
            'Turnover tenaga ahli di tengah proyek',
            'Konflik dengan masyarakat sekitar lokasi',
            'Ketidakcukupan anggaran untuk mitigasi',
        ];
        
        for ($i = 0; $i < $count; $i++) {
            $risks[] = [
                'risk_code' => 'RISK-' . str_pad(rand(100, 999), 4, '0', STR_PAD_LEFT),
                'description' => $riskDescriptions[array_rand($riskDescriptions)],
                'level' => ['sedang', 'tinggi', 'sangat_tinggi'][array_rand(['sedang', 'tinggi', 'sangat_tinggi'])],
                'score' => rand(10, 25),
                'mitigation_status' => rand(0, 100) . '%',
            ];
        }
        
        return $risks;
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