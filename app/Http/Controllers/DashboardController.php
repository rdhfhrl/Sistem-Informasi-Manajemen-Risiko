<?php

namespace App\Http\Controllers;

use App\Models\Risk;
use App\Models\RiskCategory;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Audit;
use App\Models\Report;
use App\Models\RiskMitigation;
use App\Models\RiskMonitoring;
use App\Models\User;
use App\Models\StrategicObjective;
use App\Models\BusinessProcess;
use App\Models\RiskAnalysis;
use App\Models\RiskEvaluation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Redirect ke dashboard berdasarkan role
        if ($user->role === 'admin') {
            return $this->adminDashboard($user);
        } elseif ($user->role === 'unit_pemilik_risiko') {
            return $this->uprDashboard($user);
        } elseif ($user->role === 'auditor') {
            return $this->auditorDashboard($user);
        }
        
        // Default dashboard jika role tidak dikenali
        return $this->defaultDashboard($user);
    }

    /**
     * Dashboard untuk Admin
     */
    private function adminDashboard($user)
    {
        // ==================== STATISTIK ADMIN ====================
        $stats = [
            'total_users' => User::count(),
            'active_users' => $this->getActiveUsersCount(),            
            'total_organizations' => Organization::count(),
            'uptd_count' => Organization::where('organization_type', 'UPTD')->count(),
            'active_projects' => Project::where('pro_status', 'Aktif')->count(),
            'completed_projects' => Project::where('pro_status', 'Selesai')->count(),
            'total_risks' => Risk::count(),
            'high_risks' => Risk::whereIn('risk_level', ['tinggi', 'sangat_tinggi'])->count(),
            'average_risk_score' => round(Risk::avg('risk_score') ?? 0, 2),
            'recent_audits' => Audit::where('audit_date', '>=', Carbon::now()->subMonth())->count(),
        ];
        
        // ==================== MATRIKS RISIKO ====================
        $riskMatrix = $this->getRiskMatrixData();
        
        // ==================== AKTIVITAS TERBARU ====================
        $recentActivities = $this->getRecentActivities(5);
        
        // ==================== DATA UNTUK CHART ====================
        // Distribusi risiko per kategori
        $categoryRiskDistribution = Risk::select('risk_categories.risk_category_name', DB::raw('COUNT(*) as total'))
            ->leftJoin('risk_categories', 'risk.risk_category_id', '=', 'risk_categories.risk_category_id')
            ->groupBy('risk_categories.risk_category_id', 'risk_categories.risk_category_name')
            ->orderBy('total', 'desc')
            ->get();
            
        // Distribusi risiko per organisasi
        $orgRiskDistribution = Risk::select('organizations.organization_name', DB::raw('COUNT(*) as total'))
            ->leftJoin('organizations', 'risk.risk_organization_id', '=', 'organizations.organization_id')
            ->groupBy('organizations.organization_id', 'organizations.organization_name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
            
        // ==================== NOTIFIKASI SISTEM ====================
        $notifications = $this->getSystemNotifications();
        
        return view('dashboard.admin.index', compact(
            'stats',
            'riskMatrix',
            'recentActivities',
            'categoryRiskDistribution',
            'orgRiskDistribution',
            'notifications'
        ));
    }

    private function getActiveUsersCount()
    {
        try {
            // Coba hitung dengan kolom is_active
            return User::where('is_active', true)->count();
        } catch (\Exception $e) {
            // Jika kolom belum ada, asumsikan semua user aktif
            return User::count();
        }
    }

    private function getUserStats()
    {
        try {
            return [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'inactive' => User::where('is_active', false)->count(),
            ];
        } catch (\Exception $e) {
            // Jika error, return data default
            return [
                'total' => User::count(),
                'active' => User::count(), // Asumsi semua aktif
                'inactive' => 0,
            ];
        }
    }

    /**
     * Dashboard untuk Unit Pemilik Risiko (UPR)
     */
    private function uprDashboard($user)
    {
        try {
            $userId = $user->id;
            $userName = $user->name; // Untuk mencocokkan dengan string di responsible_party
            
            Log::info('Loading UPR dashboard for user: ' . $userName . ' (ID: ' . $userId . ')');
            
            // ==================== STATISTIK RISIKO ====================
            $myRisksCount = Risk::where('risk_user_id', $userId)->count();
            $highRisksCount = Risk::where('risk_user_id', $userId)
                ->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
                ->count();
            
            Log::info('User risks - Total: ' . $myRisksCount . ', High: ' . $highRisksCount);
            
            // ==================== STATISTIK PROYEK ====================
            $projectIds = Risk::where('risk_user_id', $userId)
                ->distinct('risk_pro_id')
                ->pluck('risk_pro_id')
                ->toArray();
            
            Log::info('Project IDs from user risks: ' . json_encode($projectIds));
            
            $myProjectsCount = 0;
            $ongoingProjectsCount = 0;
            
            if (!empty($projectIds)) {
                $myProjectsCount = Project::whereIn('pro_id', $projectIds)->count();
                $ongoingProjectsCount = Project::whereIn('pro_id', $projectIds)
                    ->where('pro_status', 'Aktif')
                    ->count();
            }
            
            Log::info('Projects - Total: ' . $myProjectsCount . ', Ongoing: ' . $ongoingProjectsCount);
            
            // ==================== STATISTIK MITIGASI ====================
            // CATATAN: responsible_party di risk_mitigations adalah string (nama), bukan ID
            // Jadi kita perlu mencocokkan berdasarkan nama user
            
            $pendingMitigationsCount = RiskMitigation::where('responsible_party', $userName)
                ->whereNotIn('status', ['selesai', 'dibatalkan'])
                ->count();
                
            $overdueMitigationsCount = RiskMitigation::where('responsible_party', $userName)
                ->where('deadline', '<', Carbon::today())
                ->whereNotIn('status', ['selesai', 'dibatalkan'])
                ->count();
            
            Log::info('Mitigations for ' . $userName . ' - Pending: ' . $pendingMitigationsCount . ', Overdue: ' . $overdueMitigationsCount);
            
            // ==================== STATISTIK MONITORING ====================
            $upcomingMonitoringCount = 0;
            $todayMonitoringCount = 0;
            
            // Jika tabel risk_monitoring ada dan memiliki kolom monitored_by
            if (Schema::hasTable('risk_monitoring')) {
                // Coba berdasarkan ID jika kolomnya integer
                if (Schema::hasColumn('risk_monitoring', 'monitored_by')) {
                    $upcomingMonitoringCount = RiskMonitoring::where('monitored_by', $userId)
                        ->where('monitoring_date', '>=', Carbon::today())
                        ->count();
                        
                    $todayMonitoringCount = RiskMonitoring::where('monitored_by', $userId)
                        ->whereDate('monitoring_date', Carbon::today())
                        ->count();
                }
            }
            
            // ==================== COMPILE STATS ====================
            $stats = [
                'my_projects' => $myProjectsCount,
                'ongoing_projects' => $ongoingProjectsCount,
                'my_risks' => $myRisksCount,
                'high_risks' => $highRisksCount,
                'pending_mitigations' => $pendingMitigationsCount,
                'overdue_mitigations' => $overdueMitigationsCount,
                'upcoming_monitoring' => $upcomingMonitoringCount,
                'today_monitoring' => $todayMonitoringCount,
            ];
            
            // ==================== DATA PROYEK SAYA ====================
            $myProjects = collect([]);
            if (!empty($projectIds)) {
                $myProjects = Project::withCount(['risks' => function($query) use ($userId) {
                        $query->where('risk_user_id', $userId);
                    }])
                    ->with(['risks' => function($query) use ($userId) {
                        $query->where('risk_user_id', $userId)
                            ->orderBy('created_at', 'desc')
                            ->take(3);
                    }])
                    ->whereIn('pro_id', $projectIds)
                    ->orderBy('pro_tanggal_mulai', 'desc')
                    ->limit(6)
                    ->get();
                
                Log::info('Loaded ' . $myProjects->count() . ' projects for user');
            }
            
            // ==================== RISIKO TERBARU SAYA ====================
            $myRecentRisks = Risk::with(['organization', 'project'])
                ->where('risk_user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($risk) {
                    // Tambahkan warna berdasarkan level risiko
                    $risk->level_color = $this->getRiskColor($risk->risk_level);
                    return $risk;
                });
                
            Log::info('Loaded ' . $myRecentRisks->count() . ' recent risks for user');
                
            // ==================== MITIGASI KRITIS ====================
            $criticalMitigations = RiskMitigation::with('risk')
                ->where('responsible_party', $userName)
                ->where('deadline', '<=', Carbon::now()->addDays(7))
                ->whereNotIn('status', ['selesai', 'dibatalkan'])
                ->orderBy('deadline')
                ->limit(5)
                ->get();
                
            Log::info('Loaded ' . $criticalMitigations->count() . ' critical mitigations for user');
                
            // ==================== PEMBERITAHUAN ====================
            $updates = $this->getUprUpdates($user);
            
            return view('dashboard.upr.index', compact(
                'stats',
                'myProjects',
                'myRecentRisks',
                'criticalMitigations',
                'updates'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error in uprDashboard: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return fallback view
            return view('dashboard.upr.index', [
                'stats' => [
                    'my_projects' => 0,
                    'ongoing_projects' => 0,
                    'my_risks' => 0,
                    'high_risks' => 0,
                    'pending_mitigations' => 0,
                    'overdue_mitigations' => 0,
                    'upcoming_monitoring' => 0,
                    'today_monitoring' => 0,
                ],
                'myProjects' => collect([]),
                'myRecentRisks' => collect([]),
                'criticalMitigations' => collect([]),
                'updates' => []
            ]);
        }
    }

    /**
     * Dashboard untuk Auditor - DIPERBAIKI
     */
    private function auditorDashboard($user)
    {
        try {
            Log::info('Loading auditor dashboard for user: ' . $user->name . ' (ID: ' . $user->id . ')');

            // ==================== STATISTIK AUDITOR ====================
            // Total evaluasi yang perlu direview (prioritas tinggi/sangat tinggi)
            $pendingEvaluationsCount = DB::table('risk_evaluations')
                ->whereIn('risk_evaluation_priority', ['tinggi', 'sangat tinggi'])
                ->where(function($query) {
                    $query->where('mitigation_decision', '=', '')
                        ->orWhereNull('mitigation_decision');
                })
                ->where('evaluation_date', '>=', Carbon::now()->subDays(30))
                ->count();

            // Audit yang dilakukan oleh auditor ini
            $completedAuditsCount = Audit::where('auditor', $user->name)->count();

            // Proyek dengan risiko tinggi/sangat tinggi
            $highRiskProjectsCount = Risk::whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
                ->distinct('risk_pro_id')
                ->count();

            // Hitung compliance rate berdasarkan audit findings
            $totalAudits = Audit::count();
            $compliantAudits = Audit::where(function($query) {
                    $query->where('audit_findings', 'Tidak ada temuan signifikan')
                        ->orWhereNull('audit_findings')
                        ->orWhere('audit_findings', '=', '');
                })
                ->count();
            $complianceRate = $totalAudits > 0 ? round(($compliantAudits / $totalAudits) * 100, 2) : 0;

            // Evaluasi dalam 30 hari terakhir
            $recentEvaluationsCount = DB::table('risk_evaluations')
                ->where('evaluation_date', '>=', Carbon::now()->subDays(30))
                ->count();

            // Evaluasi yang memerlukan review (lebih dari 3 hari)
            $evaluationsNeedingReview = DB::table('risk_evaluations')
                ->whereIn('risk_evaluation_priority', ['tinggi', 'sangat tinggi'])
                ->where(function($query) {
                    $query->where('mitigation_decision', '=', '')
                        ->orWhereNull('mitigation_decision');
                })
                ->where('evaluation_date', '<=', Carbon::now()->subDays(3))
                ->count();

            $stats = [
                'pending_evaluations' => $pendingEvaluationsCount,
                'completed_audits' => $completedAuditsCount,
                'audit_completion_rate' => $this->calculateAuditCompletionRate($user),
                'high_risk_projects' => $highRiskProjectsCount,
                'compliance_rate' => $complianceRate,
                'recent_evaluations' => $recentEvaluationsCount,
                'evaluations_needing_review' => $evaluationsNeedingReview,
            ];

            // ==================== EVALUASI YANG PERLU DIPROSES ====================
            $pendingEvaluations = DB::table('risk_evaluations as re')
                ->select('re.*', 'r.risk_code', 'r.risk_description', 'r.risk_level')
                ->leftJoin('risk as r', 're.risk_evaluation_risk_id', '=', 'r.risk_id')
                ->whereIn('re.risk_evaluation_priority', ['tinggi', 'sangat tinggi'])
                ->where(function($query) {
                    $query->where('re.mitigation_decision', '=', '')
                        ->orWhereNull('re.mitigation_decision');
                })
                ->orderBy('re.evaluation_date')
                ->limit(5)
                ->get()
                ->map(function($evaluation) {
                    // Tambahkan informasi tambahan
                    $evaluation->risk_level_color = $this->getRiskColor($evaluation->risk_level);
                    return $evaluation;
                });

            // ==================== AUDIT YANG BARU DIBUAT ====================
            $recentAudits = Audit::with('risk')
                ->where('auditor', $user->name)
                ->orderBy('audit_date', 'desc')
                ->limit(5)
                ->get()
                ->map(function($audit) {
                    // Tambahkan status color
                    $audit->status_color = $audit->audit_findings ? 'warning' : 'success';
                    return $audit;
                });

            // ==================== RISIKO YANG PERLU DIEVALUASI ====================
            // Risiko tinggi/sangat tinggi yang belum ada evaluasi dalam 30 hari
            $risksNeedingEvaluation = Risk::with(['organization', 'project'])
                ->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
                ->whereDoesntHave('evaluations', function($query) {
                    $query->where('evaluation_date', '>=', Carbon::now()->subDays(30));
                })
                ->orWhere(function($query) {
                    $query->whereNull('last_evaluation_date')
                        ->orWhere('last_evaluation_date', '<', Carbon::now()->subDays(30));
                })
                ->orderBy('risk_score', 'desc')
                ->limit(5)
                ->get()
                ->map(function($risk) {
                    // Tambahkan warna level
                    $risk->level_color = $this->getRiskColor($risk->risk_level);
                    return $risk;
                });

            // ==================== TEMUAN AUDIT TERBARU ====================
            $recentAuditFindings = Audit::with('risk')
                ->whereNotNull('audit_findings')
                ->where('audit_findings', '!=', 'Tidak ada temuan signifikan')
                ->orderBy('audit_date', 'desc')
                ->limit(5)
                ->get();

            // ==================== RISIKO UNTUK COMPLIANCE CHECK ====================
            // Ambil beberapa risiko untuk compliance check (bukan semua)
            $complianceRisks = Risk::with(['evaluations', 'audits'])
                ->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
                ->limit(5) // Batasi jumlah untuk performa
                ->get()
                ->map(function($risk) {
                    // Hitung compliance metrics
                    $risk->has_evaluation = $risk->evaluations->count() > 0;
                    $risk->has_audit = $risk->audits->count() > 0;
                    $risk->has_mitigation_decision = false;
                    
                    if ($risk->has_evaluation) {
                        $risk->has_mitigation_decision = $risk->evaluations
                            ->whereNotNull('mitigation_decision')
                            ->where('mitigation_decision', '!=', '')
                            ->count() > 0;
                    }
                    
                    $risk->is_compliant = $risk->has_evaluation && $risk->has_audit && $risk->has_mitigation_decision;
                    return $risk;
                });

            // ==================== NOTIFIKASI AUDITOR ====================
            $auditorNotifications = $this->getAuditorNotifications($user);

            Log::info('Auditor dashboard data loaded successfully');

            return view('dashboard.auditor.index', compact(
                'stats',
                'pendingEvaluations',
                'recentAudits',
                'risksNeedingEvaluation',  // Variabel yang benar untuk risiko perlu evaluasi
                'recentAuditFindings',
                'complianceRisks',         // Variabel baru untuk compliance check
                'auditorNotifications'     // Variabel untuk notifikasi
            ));

        } catch (\Exception $e) {
            Log::error('Error in auditorDashboard: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // Return fallback data yang aman
            return view('dashboard.auditor.index', [
                'stats' => [
                    'pending_evaluations' => 0,
                    'completed_audits' => 0,
                    'audit_completion_rate' => 0,
                    'high_risk_projects' => 0,
                    'compliance_rate' => 0,
                    'recent_evaluations' => 0,
                    'evaluations_needing_review' => 0,
                ],
                'pendingEvaluations' => collect([]),
                'recentAudits' => collect([]),
                'risksNeedingEvaluation' => collect([]),  // Pastikan variabel ini ada
                'recentAuditFindings' => collect([]),
                'complianceRisks' => collect([]),         // Tambahkan ini
                'auditorNotifications' => []              // Tambahkan ini
            ]);
        }
    }

    /**
     * Get auditor notifications - METHOD BARU
     */
    private function getAuditorNotifications($user)
    {
        $notifications = [];

        try {
            // 1. Evaluasi yang perlu direview
            $urgentEvaluations = DB::table('risk_evaluations')
                ->whereIn('risk_evaluation_priority', ['tinggi', 'sangat tinggi'])
                ->where(function($query) {
                    $query->where('mitigation_decision', '=', '')
                        ->orWhereNull('mitigation_decision');
                })
                ->where('evaluation_date', '<=', Carbon::now()->subDays(5))
                ->count();

            if ($urgentEvaluations > 0) {
                $notifications[] = [
                    'type' => 'danger',
                    'icon' => 'alert-octagon',
                    'title' => 'Evaluasi Urgent',
                    'message' => "Ada {$urgentEvaluations} evaluasi prioritas tinggi yang belum diproses",
                ];
            }

            // 2. Audit yang belum ada temuan
            $auditsWithoutFindings = Audit::where('auditor', $user->name)
                ->where(function($query) {
                    $query->whereNull('audit_findings')
                        ->orWhere('audit_findings', '=', '');
                })
                ->where('audit_date', '<', Carbon::today())
                ->count();

            if ($auditsWithoutFindings > 0) {
                $notifications[] = [
                    'type' => 'warning',
                    'icon' => 'file-text',
                    'title' => 'Laporan Audit Belum Lengkap',
                    'message' => "Ada {$auditsWithoutFindings} audit yang belum memiliki temuan",
                ];
            }

            // 3. Risiko tinggi tanpa audit dalam 90 hari
            $risksNeedingAudit = Risk::whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
                ->whereDoesntHave('audits', function($query) {
                    $query->where('audit_date', '>=', Carbon::now()->subDays(90));
                })
                ->count();

            if ($risksNeedingAudit > 0) {
                $notifications[] = [
                    'type' => 'info',
                    'icon' => 'eye',
                    'title' => 'Risiko Perlu Audit',
                    'message' => "Ada {$risksNeedingAudit} risiko tinggi yang belum diaudit dalam 90 hari",
                ];
            }

            // 4. Rekomendasi audit yang belum ditindaklanjuti
            $unaddressedRecommendations = Audit::whereNotNull('audit_recommendations')
                ->where('audit_date', '<', Carbon::now()->subDays(60))
                ->count();

            if ($unaddressedRecommendations > 0) {
                $notifications[] = [
                    'type' => 'warning',
                    'icon' => 'message-square',
                    'title' => 'Rekomendasi Belum Ditindaklanjuti',
                    'message' => "Ada {$unaddressedRecommendations} rekomendasi audit yang belum ditindaklanjuti",
                ];
            }

        } catch (\Exception $e) {
            Log::error('Error in getAuditorNotifications: ' . $e->getMessage());
        }

        return $notifications;
    }

    /**
     * Default dashboard (fallback)
     */
    private function defaultDashboard($user)
    {
        $stats = [
            'total_risks' => Risk::count(),
            'high_risks' => Risk::whereIn('risk_level', ['tinggi', 'sangat_tinggi'])->count(),
            'active_projects' => Project::where('pro_status', 'Aktif')->count(),
            'total_organizations' => Organization::count(),
            'average_risk_score' => round(Risk::avg('risk_score') ?? 0, 2),
        ];
        
        $riskMatrix = $this->getRiskMatrixData();
        $recentActivities = $this->getRecentActivities(5);
        
        return view('dashboard.index', compact('stats', 'riskMatrix', 'recentActivities'));
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get risk matrix data
     */
    private function getRiskMatrixData()
    {
        try {
            $matrix = [];
            
            for ($likelihood = 1; $likelihood <= 5; $likelihood++) {
                for ($impact = 1; $impact <= 5; $impact++) {
                    $count = Risk::where('likelihood_level', $likelihood)
                        ->where('impact_level', $impact)
                        ->count();
                    
                    $score = $likelihood * $impact;
                    $level = $this->calculateRiskLevel($score);
                    
                    $matrix[$likelihood][$impact] = [
                        'count' => $count,
                        'score' => $score,
                        'level' => $level,
                        'color' => $this->getRiskColor($level)
                    ];
                }
            }
            
            return $matrix;
        } catch (\Exception $e) {
            Log::error('Error in getRiskMatrixData: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recent activities for dashboard
     */
    private function getRecentActivities($limit = 5)
    {
        try {
            $activities = collect();
            
            // Recent risks
            Risk::with('category')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->each(function($risk) use ($activities) {
                    $activities->push([
                        'type' => 'risk',
                        'title' => 'Risiko Baru: ' . $risk->risk_code,
                        'description' => mb_strlen($risk->risk_description) > 100 
                            ? substr($risk->risk_description, 0, 100) . '...' 
                            : $risk->risk_description,
                        'time' => $risk->created_at->diffForHumans(),
                        'color' => '#3b82f6',
                        'icon' => 'alert-triangle',
                    ]);
                });
            
            // Recent mitigations
            RiskMitigation::with('risk')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->each(function($mitigation) use ($activities) {
                    $activities->push([
                        'type' => 'mitigation',
                        'title' => 'Mitigasi Baru: ' . ($mitigation->risk->risk_code ?? '-'),
                        'description' => mb_strlen($mitigation->mitigation_plan) > 100 
                            ? substr($mitigation->mitigation_plan, 0, 100) . '...' 
                            : $mitigation->mitigation_plan,
                        'time' => $mitigation->created_at->diffForHumans(),
                        'color' => '#10b981',
                        'icon' => 'shield',
                    ]);
                });
            
            return $activities->sortByDesc(function($activity) {
                    return $activity['time'] ?? Carbon::now();
                })
                ->take($limit)
                ->values();
                
        } catch (\Exception $e) {
            Log::error('Error in getRecentActivities: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get system notifications for admin
     */
    private function getSystemNotifications()
    {
        $notifications = [];
        
        try {
            // Check for overdue mitigations
            $overdueCount = RiskMitigation::where('deadline', '<', Carbon::today())
                ->whereNotIn('status', ['selesai', 'dibatalkan'])
                ->count();
            
            if ($overdueCount > 0) {
                $notifications[] = [
                    'type' => 'danger',
                    'icon' => 'alert-octagon',
                    'title' => 'Mitigasi Terlambat',
                    'message' => "Ada {$overdueCount} mitigasi yang melewati deadline",
                ];
            }
            
            // Check for high risks without mitigations
            $highRisksNoMitigation = Risk::whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
                ->doesntHave('mitigations')
                ->count();
            
            if ($highRisksNoMitigation > 0) {
                $notifications[] = [
                    'type' => 'warning',
                    'icon' => 'alert-circle',
                    'title' => 'Risiko Tinggi Tanpa Mitigasi',
                    'message' => "Ada {$highRisksNoMitigation} risiko tinggi tanpa rencana mitigasi",
                ];
            }
            
            // Check for system health
            $totalUsers = User::count();
            $activeUsers = User::where('is_active', true)->count();
            
            if ($totalUsers > 0 && ($activeUsers / $totalUsers) < 0.8) {
                $notifications[] = [
                    'type' => 'info',
                    'icon' => 'users',
                    'title' => 'Pengguna Tidak Aktif',
                    'message' => 'Beberapa pengguna belum aktif dalam sistem',
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error in getSystemNotifications: ' . $e->getMessage());
        }
        
        return $notifications;
    }

    /**
     * Get updates for UPR - SUDAH BENAR
     */
    private function getUprUpdates($user)
    {
        $updates = [];
        $userId = $user->id;
        $userName = $user->name;
        
        try {
            // 1. Deadline mitigasi mendatang
            $upcomingDeadlines = RiskMitigation::where('responsible_party', $userName)
                ->where('deadline', '>=', Carbon::today())
                ->where('deadline', '<=', Carbon::now()->addDays(7))
                ->whereNotIn('status', ['selesai', 'dibatalkan'])
                ->count();
            
            if ($upcomingDeadlines > 0) {
                $updates[] = [
                    'type' => 'info',
                    'icon' => 'calendar',
                    'title' => 'Deadline Mendatang',
                    'message' => "Ada {$upcomingDeadlines} mitigasi dengan deadline dalam 7 hari",
                ];
            }
            
            // 2. Risiko yang perlu dimonitor (belum ada monitoring dalam 30 hari)
            $risksNeedMonitoring = Risk::where('risk_user_id', $userId)
                ->where(function($query) {
                    $query->whereNull('last_monitoring_date')
                        ->orWhere('last_monitoring_date', '<', Carbon::now()->subDays(30));
                })
                ->count();
            
            if ($risksNeedMonitoring > 0) {
                $updates[] = [
                    'type' => 'warning',
                    'icon' => 'eye',
                    'title' => 'Perlu Pemantauan',
                    'message' => "Ada {$risksNeedMonitoring} risiko yang perlu dipantau",
                ];
            }
            
            // 3. Risiko dengan level tinggi
            $highRisks = Risk::where('risk_user_id', $userId)
                ->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
                ->count();
                
            if ($highRisks > 0) {
                $updates[] = [
                    'type' => 'danger',
                    'icon' => 'alert-triangle',
                    'title' => 'Risiko Tinggi',
                    'message' => "Ada {$highRisks} risiko dengan level tinggi",
                ];
            }
            
            // 4. Mitigasi yang terlambat
            $overdueMitigations = RiskMitigation::where('responsible_party', $userName)
                ->where('deadline', '<', Carbon::today())
                ->whereNotIn('status', ['selesai', 'dibatalkan'])
                ->count();
                
            if ($overdueMitigations > 0) {
                $updates[] = [
                    'type' => 'danger',
                    'icon' => 'alert-octagon',
                    'title' => 'Mitigasi Terlambat',
                    'message' => "Ada {$overdueMitigations} mitigasi yang melewati deadline",
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Error in getUprUpdates: ' . $e->getMessage());
        }
        
        return $updates;
    }

    /**
     * Calculate audit completion rate for auditor
     */
    private function calculateAuditCompletionRate($user)
    {
        try {
            $totalAssigned = Audit::where('auditor', $user->name)->count();
            $completed = Audit::where('auditor', $user->name)
                ->whereNotNull('audit_report')
                ->count();
            
            return $totalAssigned > 0 ? round(($completed / $totalAssigned) * 100, 2) : 0;
        } catch (\Exception $e) {
            Log::error('Error in calculateAuditCompletionRate: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculate compliance rate for auditor
     */
    private function calculateComplianceRate()
    {
        try {
            $totalAudits = Audit::count();
            $compliantAudits = Audit::whereNull('audit_findings')
                ->orWhere('audit_findings', '=', '')
                ->count();
            
            return $totalAudits > 0 ? round(($compliantAudits / $totalAudits) * 100, 2) : 0;
        } catch (\Exception $e) {
            Log::error('Error in calculateComplianceRate: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculate risk level based on score
     */
    private function calculateRiskLevel($score)
    {
        if ($score >= 20) return 'sangat_tinggi';
        if ($score >= 15) return 'tinggi';
        if ($score >= 10) return 'sedang';
        if ($score >= 5) return 'rendah';
        return 'sangat_rendah';
    }

    /**
     * Get risk color based on level
     */
    private function getRiskColor($level)
    {
        $colors = [
            'sangat_rendah' => '#28a745',
            'rendah' => '#ffc107',
            'sedang' => '#fd7e14',
            'tinggi' => '#dc3545',
            'sangat_tinggi' => '#721c24'
        ];
        
        return $colors[$level] ?? '#6c757d';
    }

    /**
     * API endpoint untuk mendapatkan data dashboard berdasarkan role
     */
    public function getDashboardData(Request $request)
    {
        $user = auth()->user();
        $type = $request->get('type', 'stats');
        
        try {
            if ($user->role === 'admin') {
                $data = $this->getAdminData($type);
            } elseif ($user->role === 'unit_pemilik_risiko') {
                $data = $this->getUprData($type, $user);
            } elseif ($user->role === 'auditor') {
                $data = $this->getAuditorData($type, $user);
            } else {
                $data = $this->getDefaultData($type);
            }
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getDashboardData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get admin-specific data
     */
    private function getAdminData($type)
    {
        switch ($type) {
            case 'user_stats':
                return [
                    'total' => User::count(),
                    'active' => User::where('is_active', true)->count(),
                    'admins' => User::where('role', 'admin')->count(),
                    'upr' => User::where('role', 'unit_pemilik_risiko')->count(),
                    'auditors' => User::where('role', 'auditor')->count(),
                ];
                
            case 'project_stats':
                return [
                    'total' => Project::count(),
                    'active' => Project::where('pro_status', 'Aktif')->count(),
                    'completed' => Project::where('pro_status', 'Selesai')->count(),
                    'delayed' => Project::where('pro_tanggal_selesai', '<', Carbon::today())
                        ->where('pro_status', 'Aktif')
                        ->count(),
                ];
                
            default:
                return $this->getDefaultData($type);
        }
    }

    /**
     * Get UPR-specific data
     */
    private function getUprData($type, $user)
    {
        switch ($type) {
            case 'my_risks':
                return Risk::where('risk_user_id', $user->id)
                    ->with(['category', 'project'])
                    ->orderBy('risk_score', 'desc')
                    ->limit(10)
                    ->get();
                    
            case 'my_mitigations':
                return RiskMitigation::where('responsible_party', $user->id)
                    ->with('risk')
                    ->orderBy('deadline')
                    ->limit(10)
                    ->get();
                    
            case 'monitoring_schedule':
                return Risk::where('risk_user_id', $user->id)
                    ->where(function($query) {
                        $query->whereNull('last_monitoring_date')
                            ->orWhere('last_monitoring_date', '<', Carbon::now()->subDays(30));
                    })
                    ->with(['project', 'organization'])
                    ->get();
                    
            default:
                return $this->getDefaultData($type);
        }
    }

    /**
     * Get auditor-specific data - SUDAH BENAR
     */
    private function getAuditorData($type, $user)
    {
        try {
        switch ($type) {
            case 'pending_evaluations':
                // Cek apakah tabel risk_evaluations ada
                if (!Schema::hasTable('risk_evaluations')) {
                    return collect([]);
                }
                
                // Evaluasi prioritas tinggi yang perlu perhatian
                return RiskEvaluation::with('risk')
                    ->where(function($query) {
                        $query->whereIn('risk_evaluation_priority', ['tinggi', 'sangat tinggi'])
                              ->orWhere(function($q) {
                                  $q->where('mitigation_decision', '=', '')
                                    ->orWhereNull('mitigation_decision');
                              });
                    })
                    ->orderBy('evaluation_date')
                    ->get();
                    
            case 'my_audits':
                return Audit::where('auditor', $user->name)
                    ->with('risk')
                    ->orderBy('audit_date', 'desc')
                    ->limit(10)
                    ->get();
                    
            case 'risk_compliance':
                $risks = Risk::with(['evaluations', 'audits'])
                    ->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
                    ->get()
                    ->map(function($risk) {
                        $risk->has_evaluation = $risk->evaluations->count() > 0;
                        $risk->has_audit = $risk->audits->count() > 0;
                        
                        // Cek apakah evaluasi mencakup keputusan mitigasi
                        $risk->has_mitigation_decision = $risk->evaluations
                            ->whereNotNull('mitigation_decision')
                            ->where('mitigation_decision', '!=', '')
                            ->count() > 0;
                            
                        $risk->is_compliant = $risk->has_evaluation && $risk->has_audit && $risk->has_mitigation_decision;
                        return $risk;
                    });
                
                \Log::debug('Risks data:', ['risks' => $risks->toArray()]);

                return $risks;
                    
            default:
                return $this->getDefaultData($type);
        }
        } catch (\Exception $e) {
            Log::error('Error in getAuditorData: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get default data
     */
    private function getDefaultData($type)
    {
        switch ($type) {
            case 'stats':
                return [
                    'total_risks' => Risk::count(),
                    'high_risks' => Risk::whereIn('risk_level', ['tinggi', 'sangat_tinggi'])->count(),
                    'active_projects' => Project::where('pro_status', 'Aktif')->count(),
                ];
                
            case 'risk_matrix':
                return $this->getRiskMatrixData();
                
            default:
                return [];
        }
    }
}