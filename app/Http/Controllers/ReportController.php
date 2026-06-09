<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportSchedule;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Risk;
use App\Models\RiskCategory;
use App\Models\RiskMitigation;
use App\Models\RiskMonitoring;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with(['organization', 'project', 'generator', 'schedule']);
        
        // Filters
        if ($request->filled('report_type')) {
            $query->where('report_type', $request->report_type);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }
        
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('report_date', [
                $request->start_date,
                $request->end_date
            ]);
        }
        
        if ($request->filled('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        if ($request->filled('schedule_id')) {
            $query->where('schedule_id', $request->schedule_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }
        
        $reports = $query->orderBy('report_date', 'desc')
            ->paginate(20);
        
        $organizations = Organization::orderBy('organization_name')->get();
        $schedules = ReportSchedule::active()->orderBy('schedule_name')->get();
        $reportTypes = [
            'monitoring' => 'Laporan Pemantauan',
            'risk_profile' => 'Profil Risiko',
            'executive_summary' => 'Ringkasan Eksekutif',
            'mitigation_effectiveness' => 'Efektivitas Mitigasi',
            'custom' => 'Laporan Kustom'
        ];
        
        return view('reports.index', compact('reports', 'organizations', 'schedules', 'reportTypes'));
    }

    public function create(Request $request)
    {
        $schedules = ReportSchedule::active()->orderBy('schedule_name')->get();
        $organizations = Organization::orderBy('organization_name')->get();
        $projects = Project::where('pro_status', 'Aktif')->orderBy('pro_nama')->get();
        $risks = Risk::orderBy('risk_code')->get();
        
        $presetType = $request->get('type');
        $presetParams = $request->only(['organization_id', 'project_id', 'risk_id', 'period', 'schedule_id']);
        
        // If schedule_id is provided, load schedule data
        $schedule = null;
        if ($request->filled('schedule_id')) {
            $schedule = ReportSchedule::find($request->schedule_id);
            if ($schedule) {
                $presetType = $schedule->report_type;
                $presetParams = array_merge($presetParams, [
                    'report_type' => $schedule->report_type,
                    'schedule_id' => $schedule->schedule_id,
                    'parameters' => $schedule->parameters ?? [],
                ]);
            }
        }
        
        return view('reports.create', compact('schedules', 'organizations', 'projects', 'risks', 'presetType', 'presetParams', 'schedule'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:monitoring,risk_profile,executive_summary,mitigation_effectiveness,custom',
            'title' => 'required|string|max:255',
            'period' => 'nullable|string|in:bulanan,triwulan,tahunan,custom',
            'report_date' => 'required|date',
            'organization_id' => 'nullable|exists:organizations,organization_id',
            'project_id' => 'nullable|exists:project,pro_id',
            'risk_id' => 'nullable|exists:risk,risk_id',
            'schedule_id' => 'nullable|exists:report_schedules,schedule_id',
            'custom_data' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'sometimes|in:draft,generated,published,archived',
        ]);

        // Generate report data based on type
        $reportData = $this->generateReportData($validated);
        
        // Save report
        $report = Report::create([
            ...$validated,
            'data' => $reportData,
            'generated_by' => auth()->id(),
            'status' => $request->status ?? 'generated'
        ]);

        // Generate PDF if requested
        if ($request->has('generate_pdf')) {
            $this->generateReportPdf($report);
        }

        return redirect()->route('reports.show', $report->report_id)
            ->with('success', 'Laporan berhasil dibuat.');
    }

    public function show($id)
    {
        $report = Report::with(['organization', 'project', 'risk', 'generator', 'approver', 'schedule'])
            ->findOrFail($id);
        
        // Transform data for display
        $reportData = $this->formatReportData($report);
        
        return view('reports.show', compact('report', 'reportData'));
    }

    public function edit($id)
    {
        $report = Report::findOrFail($id);
        
        if ($report->isPublished()) {
            return redirect()->route('reports.show', $id)
                ->with('error', 'Laporan yang sudah dipublikasikan tidak dapat diedit.');
        }
        
        $schedules = ReportSchedule::active()->orderBy('schedule_name')->get();
        $organizations = Organization::orderBy('organization_name')->get();
        $projects = Project::where('pro_status', 'Aktif')->orderBy('pro_nama')->get();
        $risks = Risk::orderBy('risk_code')->get();
        
        return view('reports.edit', compact('report', 'schedules', 'organizations', 'projects', 'risks'));
    }

    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        if ($report->isPublished()) {
            return redirect()->route('reports.show', $id)
                ->with('error', 'Laporan yang sudah dipublikasikan tidak dapat diedit.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,generated,published,archived',
            'schedule_id' => 'nullable|exists:report_schedules,schedule_id',
            'approved_by' => 'nullable|exists:users,id',
            'approval_date' => 'nullable|date',
        ]);

        $report->update($validated);

        return redirect()->route('reports.show', $report->report_id)
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        
        // Check if report is published
        if ($report->isPublished()) {
            return redirect()->route('reports.index')
                ->with('error', 'Laporan yang sudah dipublikasikan tidak dapat dihapus. Silakan arsipkan terlebih dahulu.');
        }
        
        // Delete file if exists
        if ($report->hasFile()) {
            Storage::delete($report->file_path);
        }
        
        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }

    public function approve(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        $request->validate([
            'approval_notes' => 'nullable|string'
        ]);
        
        $report->update([
            'approved_by' => auth()->id(),
            'approval_date' => now(),
            'status' => 'published',
            'notes' => $report->notes . "\n\nCatatan Persetujuan: " . ($request->approval_notes ?? 'Disetujui')
        ]);

        // Send notification if report has schedule and auto_send_email
        if ($report->schedule_id && $report->schedule->auto_send_email) {
            $this->sendReportNotification($report);
        }

        return redirect()->route('reports.show', $report->report_id)
            ->with('success', 'Laporan berhasil disetujui dan dipublikasikan.');
    }

    public function generatePDF($id)
    {
        $report = Report::with(['organization', 'project', 'risk', 'generator', 'approver', 'schedule'])
            ->findOrFail($id);
        
        $reportData = $this->formatReportData($report);
        
        // Load PDF view based on report type
        $viewName = 'reports.pdf.' . str_replace('-', '_', $report->report_type);
        
        if (!view()->exists($viewName)) {
            $viewName = 'reports.pdf.default';
        }
        
        $pdf = PDF::loadView($viewName, compact('report', 'reportData'))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ]);
        
        // Generate filename
        $filename = 'Laporan-' . str_replace(' ', '-', $report->title) . '-' . date('Ymd-His') . '.pdf';
        $filePath = 'reports/pdf/' . $filename;
        
        // Save to storage
        Storage::put($filePath, $pdf->output());
        
        // Update report with file path
        $report->update(['file_path' => $filePath]);
        
        return $pdf->download($filename);
    }

    public function downloadFile($id)
    {
        $report = Report::findOrFail($id);
        
        if (!$report->hasFile()) {
            return redirect()->route('reports.show', $id)
                ->with('error', 'File laporan tidak ditemukan.');
        }
        
        return Storage::download($report->file_path);
    }

    public function generateFromSchedule(ReportSchedule $schedule, Request $request)
    {
        // Generate report based on schedule
        $params = array_merge(
            $schedule->parameters ?? [],
            $request->all()
        );

        $params['report_type'] = $schedule->report_type;
        $params['schedule_id'] = $schedule->schedule_id;

        // Generate report data based on schedule type
        $reportData = $this->generateReportData($params);
        
        // Create report title
        $title = $this->generateReportTitle($schedule);
        
        // Save report
        $report = Report::create([
            'report_type' => $schedule->report_type,
            'title' => $title,
            'period' => $this->getPeriodFromFrequency($schedule->frequency),
            'report_date' => now(),
            'schedule_id' => $schedule->schedule_id,
            'data' => $reportData,
            'generated_by' => auth()->id(),
            'status' => 'generated'
        ]);

        // Auto generate PDF if enabled
        if ($schedule->auto_generate) {
            $this->generateReportPdf($report);
            
            // Auto send email if enabled
            if ($schedule->auto_send_email && !empty($schedule->recipients)) {
                $this->sendScheduledReportEmail($report, $schedule);
            }
        }

        return redirect()->route('reports.show', $report->report_id)
            ->with('success', 'Laporan dari jadwal berhasil dibuat.');
    }

    public function bulkGenerateFromSchedule(Request $request)
    {
        $request->validate([
            'schedule_ids' => 'required|array',
            'schedule_ids.*' => 'exists:report_schedules,schedule_id',
            'report_date' => 'nullable|date',
        ]);

        $generatedReports = [];
        $failedReports = [];

        foreach ($request->schedule_ids as $scheduleId) {
            try {
                $schedule = ReportSchedule::find($scheduleId);
                
                if (!$schedule->is_active) {
                    $failedReports[] = ['schedule' => $schedule->schedule_name, 'reason' => 'Jadwal tidak aktif'];
                    continue;
                }

                // Check if report already exists for today (for daily/weekly/monthly schedules)
                $existingReport = $this->checkExistingScheduledReport($schedule);
                if ($existingReport) {
                    $failedReports[] = ['schedule' => $schedule->schedule_name, 'reason' => 'Laporan sudah ada untuk periode ini'];
                    continue;
                }

                $params = $schedule->parameters ?? [];
                $params['report_type'] = $schedule->report_type;
                $params['schedule_id'] = $schedule->schedule_id;

                $reportData = $this->generateReportData($params);
                
                $title = $this->generateReportTitle($schedule);
                
                $report = Report::create([
                    'report_type' => $schedule->report_type,
                    'title' => $title,
                    'period' => $this->getPeriodFromFrequency($schedule->frequency),
                    'report_date' => $request->report_date ?? now(),
                    'schedule_id' => $schedule->schedule_id,
                    'data' => $reportData,
                    'generated_by' => auth()->id(),
                    'status' => 'generated'
                ]);

                if ($schedule->auto_generate) {
                    $this->generateReportPdf($report);
                }

                $generatedReports[] = $report;

            } catch (\Exception $e) {
                $failedReports[] = [
                    'schedule' => $schedule->schedule_name ?? 'Unknown',
                    'reason' => $e->getMessage()
                ];
            }
        }

        $message = sprintf(
            'Berhasil membuat %d laporan. Gagal: %d laporan.',
            count($generatedReports),
            count($failedReports)
        );

        if (!empty($failedReports)) {
            $message .= ' Detail kegagalan: ' . json_encode($failedReports);
        }

        return redirect()->route('reports.index')
            ->with('success', $message)
            ->with('failed_reports', $failedReports);
    }

    // ==================== SPECIFIC REPORT GENERATORS ====================

    public function generateMonitoringReport(Request $request)
    {
        $validated = $request->validate([
            'period' => 'required|in:bulanan,triwulan,tahunan',
            'organization_id' => 'nullable|exists:organizations,organization_id',
            'project_id' => 'nullable|exists:project,pro_id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'schedule_id' => 'nullable|exists:report_schedules,schedule_id',
        ]);

        $data = $this->generateMonitoringData($validated);
        
        $title = 'Laporan Pemantauan Risiko ' . ucfirst($validated['period']) . ' ' . date('F Y');
        if ($validated['organization_id']) {
            $org = Organization::find($validated['organization_id']);
            $title .= ' - ' . $org->organization_name;
        }
        
        $report = Report::create([
            'report_type' => 'monitoring',
            'title' => $title,
            'period' => $validated['period'],
            'report_date' => now(),
            'organization_id' => $validated['organization_id'],
            'project_id' => $validated['project_id'],
            'schedule_id' => $validated['schedule_id'] ?? null,
            'data' => $data,
            'generated_by' => auth()->id(),
            'status' => 'generated'
        ]);

        return redirect()->route('reports.show', $report->report_id)
            ->with('success', 'Laporan pemantauan berhasil dibuat.');
    }

    public function generateRiskProfileReport(Request $request)
    {
        $validated = $request->validate([
            'organization_id' => 'nullable|exists:organizations,organization_id',
            'project_id' => 'nullable|exists:project,pro_id',
            'category_id' => 'nullable|exists:risk_categories,risk_category_id',
            'risk_level' => 'nullable|in:sangat_rendah,rendah,sedang,tinggi,sangat_tinggi',
            'schedule_id' => 'nullable|exists:report_schedules,schedule_id',
        ]);

        $data = $this->generateRiskProfileData($validated);
        
        $title = 'Profil Risiko ' . date('F Y');
        if ($validated['organization_id']) {
            $org = Organization::find($validated['organization_id']);
            $title .= ' - ' . $org->organization_name;
        }
        
        $report = Report::create([
            'report_type' => 'risk_profile',
            'title' => $title,
            'period' => 'bulanan',
            'report_date' => now(),
            'organization_id' => $validated['organization_id'],
            'project_id' => $validated['project_id'],
            'schedule_id' => $validated['schedule_id'] ?? null,
            'data' => $data,
            'generated_by' => auth()->id(),
            'status' => 'generated'
        ]);

        return redirect()->route('reports.show', $report->report_id)
            ->with('success', 'Laporan profil risiko berhasil dibuat.');
    }

    public function generateExecutiveSummary(Request $request)
    {
        $validated = $request->validate([
            'period' => 'required|in:bulanan,triwulan,tahunan',
            'organization_id' => 'nullable|exists:organizations,organization_id',
            'include_comparison' => 'boolean',
            'include_recommendations' => 'boolean',
            'schedule_id' => 'nullable|exists:report_schedules,schedule_id',
        ]);

        $data = $this->generateExecutiveSummaryData($validated);
        
        $title = 'Ringkasan Eksekutif Manajemen Risiko ' . ucfirst($validated['period']) . ' ' . date('Y');
        
        $report = Report::create([
            'report_type' => 'executive_summary',
            'title' => $title,
            'period' => $validated['period'],
            'report_date' => now(),
            'organization_id' => $validated['organization_id'],
            'schedule_id' => $validated['schedule_id'] ?? null,
            'data' => $data,
            'generated_by' => auth()->id(),
            'status' => 'generated'
        ]);

        return redirect()->route('reports.show', $report->report_id)
            ->with('success', 'Ringkasan eksekutif berhasil dibuat.');
    }

    public function generateMitigationEffectivenessReport(Request $request)
    {
        $validated = $request->validate([
            'period' => 'required|in:bulanan,triwulan,tahunan',
            'organization_id' => 'nullable|exists:organizations,organization_id',
            'project_id' => 'nullable|exists:project,pro_id',
            'schedule_id' => 'nullable|exists:report_schedules,schedule_id',
        ]);

        $data = $this->generateMitigationEffectivenessData($validated);
        
        $title = 'Laporan Efektivitas Mitigasi ' . ucfirst($validated['period']) . ' ' . date('F Y');
        
        $report = Report::create([
            'report_type' => 'mitigation_effectiveness',
            'title' => $title,
            'period' => $validated['period'],
            'report_date' => now(),
            'organization_id' => $validated['organization_id'],
            'project_id' => $validated['project_id'],
            'schedule_id' => $validated['schedule_id'] ?? null,
            'data' => $data,
            'generated_by' => auth()->id(),
            'status' => 'generated'
        ]);

        return redirect()->route('reports.show', $report->report_id)
            ->with('success', 'Laporan efektivitas mitigasi berhasil dibuat.');
    }

    // ==================== DATA GENERATION METHODS ====================

    private function generateReportData(array $params)
    {
        $data = [];
        
        switch ($params['report_type']) {
            case 'monitoring':
                $data = $this->generateMonitoringData($params);
                break;
            case 'risk_profile':
                $data = $this->generateRiskProfileData($params);
                break;
            case 'executive_summary':
                $data = $this->generateExecutiveSummaryData($params);
                break;
            case 'mitigation_effectiveness':
                $data = $this->generateMitigationEffectivenessData($params);
                break;
            case 'custom':
                $data = json_decode($params['custom_data'] ?? '{}', true) ?? [];
                break;
        }
        
        return $data;
    }

    private function generateMonitoringData(array $params)
    {
        $query = Risk::with(['category', 'organization', 'project', 'mitigations', 'monitorings']);
        
        // Apply filters
        if (!empty($params['organization_id'])) {
            $query->where('risk_organization_id', $params['organization_id']);
        }
        
        if (!empty($params['project_id'])) {
            $query->where('risk_pro_id', $params['project_id']);
        }
        
        if (!empty($params['start_date']) && !empty($params['end_date'])) {
            $query->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
        }
        
        $risks = $query->get();
        $period = $params['period'] ?? 'bulanan';
        
        $data = [
        'metadata' => [
            'generated_at' => now()->toDateTimeString(),
            'period' => $period,
            'total_risks' => $risks->count(),
            'filters_applied' => $params,
        ],
        'summary' => [
            'total_risks' => $risks->count(),
            'risk_distribution' => $risks->groupBy('risk_level')->map->count(),
            'category_distribution' => $risks->groupBy('category.risk_category_name')->map->count(),
            'average_risk_score' => round($risks->avg('risk_score') ?? 0, 2),
        ],
        'mitigation_effectiveness' => [
            'total_mitigations' => $risks->sum(function($risk) {
                return $risk->mitigations->count();
            }),
            'mitigations_by_status' => $this->getMitigationsByStatus($risks),
            'completion_rate' => $this->calculateCompletionRate($risks),
        ],
        'monitoring_activities' => [
            'total_monitorings' => $risks->sum(function($risk) {
                return $risk->monitorings->count();
            }),
            'recent_monitorings' => $this->getRecentMonitorings($risks, 10),
            'effectiveness_rating' => $this->calculateMonitoringEffectiveness($risks),
        ],
        'top_risks' => $risks->sortByDesc('risk_score')->take(10)->map(function($risk) {
            return [
                'risk_code' => $risk->risk_code,
                'risk_description' => $risk->risk_description,
                'risk_level' => $risk->risk_level,
                'risk_score' => $risk->risk_score,
                'organization' => [
                    'organization_name' => $risk->organization->organization_name ?? null,
                ],
                'project' => [
                    'pro_nama' => $risk->project->pro_nama ?? null,
                ],
            ];
        })->values()->toArray(),
        'trend_analysis' => $this->analyzeRiskTrend($risks, $period),
        ];
    
        return $data;
    }

    private function generateRiskProfileData(array $params)
    {
        $query = Risk::with(['category', 'organization', 'project', 'analyses']);
        
        // Apply filters
        if (!empty($params['organization_id'])) {
            $query->where('risk_organization_id', $params['organization_id']);
        }
        
        if (!empty($params['project_id'])) {
            $query->where('risk_pro_id', $params['project_id']);
        }
        
        if (!empty($params['category_id'])) {
            $query->where('risk_category_id', $params['category_id']);
        }
        
        if (!empty($params['risk_level'])) {
            $query->where('risk_level', $params['risk_level']);
        }
        
        $risks = $query->get();
        
        $data = [
            'metadata' => [
                'generated_at' => now()->toDateTimeString(),
                'total_risks' => $risks->count(),
                'filters_applied' => $params,
            ],
            'risk_matrix' => $this->generateRiskMatrix($risks),
            'distribution' => [
                'by_level' => $risks->groupBy('risk_level')->map->count(),
                'by_category' => $risks->groupBy('category.risk_category_name')->map->count(),
                'by_organization' => $risks->groupBy('organization.organization_name')->map->count(),
                'by_project' => $risks->groupBy('project.pro_nama')->map->count(),
            ],
            'statistics' => [
                'total' => $risks->count(),
                'high_risk' => $risks->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])->count(),
                'medium_risk' => $risks->where('risk_level', 'sedang')->count(),
                'low_risk' => $risks->whereIn('risk_level', ['rendah', 'sangat_rendah'])->count(),
                'average_score' => round($risks->avg('risk_score') ?? 0, 2),
                'highest_score' => $risks->max('risk_score') ?? 0,
                'lowest_score' => $risks->min('risk_score') ?? 0,
            ],
            'top_10_risks' => $risks->sortByDesc('risk_score')->take(10)->values()->map(function($risk) {
                return [
                    'risk_code' => $risk->risk_code,
                    'description' => $risk->risk_description,
                    'category' => $risk->category->risk_category_name ?? '-',
                    'score' => $risk->risk_score,
                    'level' => $risk->risk_level,
                    'organization' => $risk->organization->organization_name ?? '-',
                    'project' => $risk->project->pro_nama ?? '-',
                ];
            }),
            'trend_analysis' => $this->analyzeRiskProfileTrend($risks),
            'recommendations' => $this->generateRiskProfileRecommendations($risks),
        ];
        
        return $data;
    }

    private function generateExecutiveSummaryData(array $params)
    {
        // Get all risks for the period
        $query = Risk::with(['category', 'organization', 'project', 'mitigations', 'monitorings']);
        
        if (!empty($params['organization_id'])) {
            $query->where('risk_organization_id', $params['organization_id']);
        }
        
        // Apply date range based on period
        $dateRange = $this->getDateRangeByPeriod($params['period']);
        if ($dateRange) {
            $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        }
        
        $risks = $query->get();
        
        // Get previous period for comparison
        $previousPeriod = $this->getPreviousPeriod($params['period']);
        $previousRisks = [];
        if ($previousPeriod) {
            $previousRisks = Risk::whereBetween('created_at', [$previousPeriod['start'], $previousPeriod['end']])->get();
        }
        
        $data = [
            'metadata' => [
                'generated_at' => now()->toDateTimeString(),
                'period' => $params['period'],
                'date_range' => $dateRange,
            ],
            'executive_overview' => [
                'total_risks' => $risks->count(),
                'high_risk_count' => $risks->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])->count(),
                'risk_trend' => $this->calculateRiskTrend($risks, $previousRisks),
                'mitigation_progress' => $this->calculateOverallMitigationProgress($risks),
                'key_achievements' => $this->getKeyAchievements($risks),
            ],
            'key_metrics' => [
                'risk_exposure' => round($risks->avg('risk_score') ?? 0, 2),
                'mitigation_completion_rate' => $this->calculateCompletionRate($risks),
                'monitoring_coverage' => $this->calculateMonitoringCoverage($risks),
                'risk_reduction' => $this->calculateRiskReduction($risks),
            ],
            'critical_risks' => $risks->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
                ->sortByDesc('risk_score')
                ->take(5)
                ->values()
                ->map(function($risk) {
                    return [
                        'risk_code' => $risk->risk_code,
                        'description' => substr($risk->risk_description, 0, 100) . '...',
                        'score' => $risk->risk_score,
                        'mitigation_status' => $risk->mitigations->where('status', 'selesai')->count() . '/' . $risk->mitigations->count(),
                        'owner' => $risk->organization->organization_name ?? '-',
                    ];
                }),
            'department_performance' => $this->getOrganizationPerformance($risks),
            'financial_impact' => $this->estimateFinancialImpact($risks),
            'strategic_recommendations' => $this->generateStrategicRecommendations($risks),
            'next_period_focus' => $this->getNextPeriodFocus($risks, $params['period']),
        ];
        
        return $data;
    }

    private function generateMitigationEffectivenessData(array $params)
    {
        $query = RiskMitigation::with(['risk', 'risk.organization', 'risk.project']);
        
        if (!empty($params['organization_id'])) {
            $query->whereHas('risk', function($q) use ($params) {
                $q->where('risk_organization_id', $params['organization_id']);
            });
        }
        
        if (!empty($params['project_id'])) {
            $query->whereHas('risk', function($q) use ($params) {
                $q->where('risk_pro_id', $params['project_id']);
            });
        }
        
        // Apply date range based on period
        $dateRange = $this->getDateRangeByPeriod($params['period']);
        if ($dateRange) {
            $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        }
        
        $mitigations = $query->get();
        $completedMitigations = $mitigations->where('status', 'selesai');
        
        $data = [
            'metadata' => [
                'generated_at' => now()->toDateTimeString(),
                'period' => $params['period'],
                'total_mitigations' => $mitigations->count(),
                'completed_mitigations' => $completedMitigations->count(),
            ],
            'effectiveness_metrics' => [
                'completion_rate' => $mitigations->count() > 0 ? 
                    round(($completedMitigations->count() / $mitigations->count()) * 100, 2) : 0,
                'on_time_completion' => $this->calculateOnTimeCompletion($completedMitigations),
                'budget_variance' => $this->calculateBudgetVariance($completedMitigations),
                'risk_reduction_effectiveness' => $this->calculateRiskReductionEffectiveness($completedMitigations),
            ],
            'mitigation_status' => [
                'by_status' => $mitigations->groupBy('status')->map->count(),
                'by_risk_level' => $mitigations->groupBy('risk.risk_level')->map->count(),
                'by_organization' => $mitigations->groupBy('risk.organization.organization_name')->map->count(),
            ],
            'performance_analysis' => [
                'top_performers' => $this->getTopPerformingMitigations($completedMitigations),
                'delayed_mitigations' => $this->getDelayedMitigations($mitigations),
                'resource_utilization' => $this->analyzeResourceUtilization($completedMitigations),
            ],
            'cost_analysis' => [
                'total_budget' => $mitigations->sum('budget'),
                'total_actual_cost' => $completedMitigations->sum('actual_cost'),
                'cost_variance' => $completedMitigations->sum('budget') - $completedMitigations->sum('actual_cost'),
                'cost_effectiveness' => $this->calculateCostEffectiveness($completedMitigations),
            ],
            'recommendations' => $this->generateMitigationRecommendations($mitigations),
        ];
        
        return $data;
    }

    // ==================== SCHEDULE-RELATED METHODS ====================

    private function generateReportTitle(ReportSchedule $schedule): string
    {
        $frequencyLabels = [
            'daily' => 'Harian',
            'weekly' => 'Mingguan',
            'monthly' => 'Bulanan',
            'quarterly' => 'Triwulan',
            'yearly' => 'Tahunan',
        ];

        $typeLabels = [
            'monitoring' => 'Pemantauan Risiko',
            'risk_profile' => 'Profil Risiko',
            'executive_summary' => 'Ringkasan Eksekutif',
            'mitigation_effectiveness' => 'Efektivitas Mitigasi',
        ];

        $frequency = $frequencyLabels[$schedule->frequency] ?? $schedule->frequency;
        $type = $typeLabels[$schedule->report_type] ?? $schedule->report_type;
        
        $dateFormat = 'd F Y';
        if ($schedule->frequency === 'monthly') {
            $dateFormat = 'F Y';
        } elseif ($schedule->frequency === 'yearly') {
            $dateFormat = 'Y';
        }

        return sprintf(
            'Laporan %s %s - %s',
            $type,
            $frequency,
            now()->format($dateFormat)
        );
    }

    private function getPeriodFromFrequency(string $frequency): string
    {
        return match($frequency) {
            'daily', 'weekly' => 'bulanan',
            'monthly' => 'bulanan',
            'quarterly' => 'triwulan',
            'yearly' => 'tahunan',
            default => 'custom',
        };
    }

    private function checkExistingScheduledReport(ReportSchedule $schedule): ?Report
    {
        $startDate = null;
        $endDate = null;

        switch ($schedule->frequency) {
            case 'daily':
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'weekly':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
            case 'monthly':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'quarterly':
                $quarter = ceil(now()->month / 3);
                $startMonth = (($quarter - 1) * 3) + 1;
                $startDate = Carbon::create(now()->year, $startMonth, 1)->startOfDay();
                $endDate = $startDate->copy()->addMonths(3)->subDay()->endOfDay();
                break;
            case 'yearly':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;
        }

        if ($startDate && $endDate) {
            return Report::where('schedule_id', $schedule->schedule_id)
                ->whereBetween('report_date', [$startDate, $endDate])
                ->first();
        }

        return null;
    }

    private function sendScheduledReportEmail(Report $report, ReportSchedule $schedule)
    {
        try {
            $recipients = $schedule->recipients ?? [];
            
            if (empty($recipients)) {
                return;
            }

            $data = [
                'report' => $report,
                'schedule' => $schedule,
                'downloadUrl' => route('reports.download', $report->report_id),
                'viewUrl' => route('reports.show', $report->report_id),
            ];

            Mail::send('emails.report-scheduled', $data, function($message) use ($recipients, $report) {
                $message->to($recipients)
                    ->subject('Laporan Otomatis: ' . $report->title)
                    ->attach(storage_path('app/' . $report->file_path));
            });

            // Log email sent
            \Log::info('Scheduled report email sent', [
                'report_id' => $report->report_id,
                'schedule_id' => $schedule->schedule_id,
                'recipients' => $recipients,
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to send scheduled report email', [
                'report_id' => $report->report_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendReportNotification(Report $report)
    {
        // Send notification when report is approved
        try {
            $recipients = $report->schedule->recipients ?? [];
            
            if (empty($recipients)) {
                return;
            }

            $data = [
                'report' => $report,
                'approver' => auth()->user(),
                'downloadUrl' => route('reports.download', $report->report_id),
            ];

            Mail::send('emails.report-approved', $data, function($message) use ($recipients, $report) {
                $message->to($recipients)
                    ->subject('Laporan Telah Disetujui: ' . $report->title);
                
                if ($report->hasFile()) {
                    $message->attach(storage_path('app/' . $report->file_path));
                }
            });

        } catch (\Exception $e) {
            \Log::error('Failed to send report notification email', [
                'report_id' => $report->report_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    // ==================== HELPER METHODS ====================

    private function formatReportData(Report $report)
    {
        $data = $report->data ?? [];
        
        // Add formatted data for display
        $data['formatted'] = [
            'generated_by_name' => $report->generator->name ?? 'System',
            'approved_by_name' => $report->approver->name ?? '-',
            'organization_name' => $report->organization->organization_name ?? 'Semua Organisasi',
            'project_name' => $report->project->pro_nama ?? 'Semua Proyek',
            'risk_description' => $report->risk->risk_description ?? '-',
            'schedule_name' => $report->schedule->schedule_name ?? '-',
            'report_date_formatted' => $report->report_date->format('d F Y'),
            'approval_date_formatted' => $report->approval_date ? $report->approval_date->format('d F Y') : '-',
            'has_file' => $report->hasFile(),
            'file_size' => $report->getFileSize(),
        ];
        
        return $data;
    }

    private function generateReportPdf(Report $report)
    {
        $reportData = $this->formatReportData($report);
        
        $viewName = 'reports.pdf.' . str_replace('-', '_', $report->report_type);
        if (!view()->exists($viewName)) {
            $viewName = 'reports.pdf.default';
        }
        
        $pdf = PDF::loadView($viewName, compact('report', 'reportData'));
        
        $filename = 'Report-' . $report->report_id . '-' . date('Ymd-His') . '.pdf';
        $filePath = 'reports/pdf/' . $filename;
        
        Storage::put($filePath, $pdf->output());
        
        $report->update(['file_path' => $filePath]);
    }

    private function getMitigationsByStatus($risks)
    {
        $riskIds = $risks->pluck('risk_id')->toArray();
        
        if (empty($riskIds)) {
            return [];
        }
        
        $statuses = RiskMitigation::whereIn('risk_mitigation_risk_id', $riskIds)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        return $statuses;
    }

    private function calculateCompletionRate($risks)
    {
        $totalMitigations = 0;
        $completedMitigations = 0;
        
        foreach ($risks as $risk) {
            $mitigations = RiskMitigation::where('risk_mitigation_risk_id', $risk->risk_id)->get();
            $totalMitigations += $mitigations->count();
            $completedMitigations += $mitigations->where('status', 'selesai')->count();
        }
        
        return $totalMitigations > 0 ? round(($completedMitigations / $totalMitigations) * 100, 2) : 0;
    }

    private function getRecentMonitorings($risks, $limit = 10)
    {
        $allMonitorings = [];
        foreach ($risks as $risk) {
            foreach ($risk->monitorings as $monitoring) {
                $allMonitorings[] = [
                    'risk_code' => $risk->risk_code,
                    'date' => $monitoring->monitoring_date,
                    'score' => $monitoring->current_risk_score,
                    'result' => substr($monitoring->monitoring_result ?? '', 0, 50) . '...',
                ];
            }
        }
        
        usort($allMonitorings, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return array_slice($allMonitorings, 0, $limit);
    }

    private function calculateMonitoringEffectiveness($risks)
    {
        $totalMonitorings = 0;
        $effectiveMonitorings = 0;
        
        foreach ($risks as $risk) {
            foreach ($risk->monitorings as $monitoring) {
                $totalMonitorings++;
                
                // Define effectiveness criteria
                $isEffective = false;
                
                // Criteria 1: Has results and recommendations
                if (!empty($monitoring->monitoring_result) && 
                    !empty($monitoring->recommendations)) {
                    $isEffective = true;
                }
                
                // Criteria 2: Follow-up action defined
                if (!empty($monitoring->follow_up_action)) {
                    $isEffective = true;
                }
                
                // Criteria 3: Risk score decreased or maintained
                if ($monitoring->current_risk_score <= $monitoring->previous_risk_score) {
                    $isEffective = true;
                }
                
                if ($isEffective) {
                    $effectiveMonitorings++;
                }
            }
        }
        
        return $totalMonitorings > 0 ? 
            round(($effectiveMonitorings / $totalMonitorings) * 100, 2) : 
            0;
    }

    private function generateRiskMatrix($risks)
    {
        $matrix = [];
        for ($impact = 1; $impact <= 5; $impact++) {
            for ($likelihood = 1; $likelihood <= 5; $likelihood++) {
                $count = $risks->where('impact_level', $impact)
                    ->where('likelihood_level', $likelihood)
                    ->count();
                
                $score = $impact * $likelihood;
                $level = $this->calculateRiskLevel($score);
                
                $matrix[$impact][$likelihood] = [
                    'count' => $count,
                    'score' => $score,
                    'level' => $level,
                ];
            }
        }
        
        return $matrix;
    }

    private function getDateRangeByPeriod($period)
    {
        $now = Carbon::now();
        
        switch ($period) {
            case 'bulanan':
                return [
                    'start' => $now->startOfMonth()->toDateString(),
                    'end' => $now->endOfMonth()->toDateString()
                ];
            case 'triwulan':
                $quarter = ceil($now->month / 3);
                $startMonth = (($quarter - 1) * 3) + 1;
                $start = Carbon::create($now->year, $startMonth, 1);
                $end = $start->copy()->addMonths(3)->subDay();
                return [
                    'start' => $start->toDateString(),
                    'end' => $end->toDateString()
                ];
            case 'tahunan':
                return [
                    'start' => $now->startOfYear()->toDateString(),
                    'end' => $now->endOfYear()->toDateString()
                ];
            default:
                return null;
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

    private function analyzeRiskTrend($risks, $period)
{
    // Group risks by time period
    $trendData = [];
    
    foreach ($risks as $risk) {
        $createdAt = $risk->created_at;
        $periodKey = '';
        
        switch ($period) {
            case 'bulanan':
                $periodKey = $createdAt->format('Y-m');
                break;
            case 'triwulan':
                $quarter = ceil($createdAt->month / 3);
                $periodKey = $createdAt->year . '-Q' . $quarter;
                break;
            case 'tahunan':
                $periodKey = $createdAt->year;
                break;
            default:
                $periodKey = $createdAt->format('Y-m-d');
        }
        
        if (!isset($trendData[$periodKey])) {
            $trendData[$periodKey] = [
                'period' => $periodKey,
                'count' => 0,
                'total_score' => 0,
                'high_risks' => 0,
            ];
        }
        
        $trendData[$periodKey]['count']++;
        $trendData[$periodKey]['total_score'] += $risk->risk_score;
        
        if (in_array($risk->risk_level, ['tinggi', 'sangat_tinggi'])) {
            $trendData[$periodKey]['high_risks']++;
        }
    }
    
    // Calculate averages
    foreach ($trendData as &$data) {
        if ($data['count'] > 0) {
            $data['avg_score'] = round($data['total_score'] / $data['count'], 2);
        } else {
            $data['avg_score'] = 0;
        }
    }
    
    // Sort by period
    ksort($trendData);
    
    return array_values($trendData);
}

private function analyzeRiskProfileTrend($risks)
{
    // Get last 12 months data
    $trendData = [];
    $now = Carbon::now();
    
    for ($i = 11; $i >= 0; $i--) {
        $date = $now->copy()->subMonths($i);
        $monthKey = $date->format('Y-m');
        
        $monthRisks = $risks->filter(function($risk) use ($date) {
            return $risk->created_at->format('Y-m') === $date->format('Y-m');
        });
        
        $trendData[$monthKey] = [
            'period' => $date->format('M Y'),
            'count' => $monthRisks->count(),
            'avg_score' => $monthRisks->count() > 0 ? 
                round($monthRisks->avg('risk_score'), 2) : 0,
            'high_risks' => $monthRisks->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])->count(),
        ];
    }
    
    return array_values($trendData);
}

private function generateRiskProfileRecommendations($risks)
{
    $recommendations = [];
    
    $highRisksCount = $risks->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])->count();
    $totalRisks = $risks->count();
    
    if ($highRisksCount > 0 && $totalRisks > 0) {
        $percentage = round(($highRisksCount / $totalRisks) * 100, 2);
        
        if ($percentage > 20) {
            $recommendations[] = [
                'type' => 'urgent',
                'title' => 'Prioritaskan Mitigasi Risiko Tinggi',
                'description' => "$percentage% dari total risiko termasuk dalam kategori tinggi/sangat tinggi. Perlu fokus pada mitigasi segera.",
                'action' => 'Review semua risiko tinggi dan percepat implementasi mitigasi.'
            ];
        }
    }
    
    // Check for risks without mitigation
    $risksWithoutMitigation = $risks->filter(function($risk) {
        return $risk->mitigations->isEmpty();
    });
    
    if ($risksWithoutMitigation->count() > 0) {
        $recommendations[] = [
            'type' => 'important',
            'title' => 'Kembangkan Rencana Mitigasi',
            'description' => "{$risksWithoutMitigation->count()} risiko belum memiliki rencana mitigasi.",
            'action' => 'Buat rencana mitigasi untuk semua risiko yang belum memiliki mitigasi.'
        ];
    }
    
    // Check for monitoring gaps
    $risksWithoutRecentMonitoring = $risks->filter(function($risk) {
        $latestMonitoring = $risk->monitorings->sortByDesc('monitoring_date')->first();
        if (!$latestMonitoring) {
            return true;
        }
        
        $daysSinceMonitoring = Carbon::parse($latestMonitoring->monitoring_date)->diffInDays(now());
        return $daysSinceMonitoring > 30;
    });
    
    if ($risksWithoutRecentMonitoring->count() > 0) {
        $recommendations[] = [
            'type' => 'monitoring',
            'title' => 'Perkuat Pemantauan',
            'description' => "{$risksWithoutRecentMonitoring->count()} risiko belum dipantau dalam 30 hari terakhir.",
            'action' => 'Jadwalkan pemantauan reguler untuk semua risiko.'
        ];
    }
    
    return $recommendations;
}

    private function calculateRiskTrend($currentRisks, $previousRisks)
    {
        if ($previousRisks->isEmpty()) {
            return 'new';
        }
        
        $currentAvgScore = $currentRisks->avg('risk_score') ?? 0;
        $previousAvgScore = $previousRisks->avg('risk_score') ?? 0;
        
        if ($currentAvgScore > $previousAvgScore * 1.1) {
            return 'increasing';
        } elseif ($currentAvgScore < $previousAvgScore * 0.9) {
            return 'decreasing';
        } else {
            return 'stable';
        }
    }

    private function calculateOverallMitigationProgress($risks)
    {
        $totalMitigations = 0;
        $completedMitigations = 0;
        $inProgressMitigations = 0;
        
        foreach ($risks as $risk) {
            $totalMitigations += $risk->mitigations->count();
            $completedMitigations += $risk->mitigations->where('status', 'selesai')->count();
            $inProgressMitigations += $risk->mitigations->where('status', 'dalam_proses')->count();
        }
        
        return [
            'total' => $totalMitigations,
            'completed' => $completedMitigations,
            'in_progress' => $inProgressMitigations,
            'completion_rate' => $totalMitigations > 0 ? 
                round(($completedMitigations / $totalMitigations) * 100, 2) : 0,
            'progress_rate' => $totalMitigations > 0 ? 
                round((($completedMitigations + $inProgressMitigations) / $totalMitigations) * 100, 2) : 0,
        ];
    }

    private function calculateMonitoringCoverage($risks)
    {
        $risksWithMonitoring = $risks->filter(function($risk) {
            return $risk->monitorings->count() > 0;
        });
        
        return $risks->count() > 0 ? 
            round(($risksWithMonitoring->count() / $risks->count()) * 100, 2) : 
            0;
    }

    private function calculateRiskReduction($risks)
    {
        $totalReduction = 0;
        $risksWithReduction = 0;
        
        foreach ($risks as $risk) {
            if ($risk->analyses->count() >= 2) {
                $firstScore = $risk->analyses->first()->risk_score;
                $latestScore = $risk->analyses->last()->risk_score;
                
                if ($latestScore < $firstScore) {
                    $totalReduction += ($firstScore - $latestScore);
                    $risksWithReduction++;
                }
            }
        }
        
        return [
            'total_reduction' => $totalReduction,
            'risks_with_reduction' => $risksWithReduction,
            'average_reduction' => $risksWithReduction > 0 ? 
                round($totalReduction / $risksWithReduction, 2) : 0,
            'reduction_rate' => $risks->count() > 0 ? 
                round(($risksWithReduction / $risks->count()) * 100, 2) : 0,
        ];
    }

    private function getKeyAchievements($risks)
    {
        $achievements = [];
        
        // Count completed mitigations
        $completedMitigations = $risks->sum(function($risk) {
            return $risk->mitigations->where('status', 'selesai')->count();
        });
        
        if ($completedMitigations > 0) {
            $achievements[] = [
                'title' => 'Mitigasi Diselesaikan',
                'description' => "$completedMitigations tindakan mitigasi berhasil diselesaikan.",
                'icon' => 'check-circle'
            ];
        }
        
        // Count risks reduced
        $risksReduced = 0;
        foreach ($risks as $risk) {
            if ($risk->analyses->count() >= 2) {
                $firstScore = $risk->analyses->first()->risk_score;
                $latestScore = $risk->analyses->last()->risk_score;
                
                if ($latestScore < $firstScore) {
                    $risksReduced++;
                }
            }
        }
        
        if ($risksReduced > 0) {
            $achievements[] = [
                'title' => 'Risiko Berkurang',
                'description' => "$risksReduced risiko berhasil direduksi skornya.",
                'icon' => 'trending-down'
            ];
        }
        
        // Check for early warning
        $earlyWarnings = $risks->filter(function($risk) {
            return in_array($risk->risk_level, ['tinggi', 'sangat_tinggi']) &&
                $risk->mitigations->where('status', 'dalam_proses')->count() > 0;
        })->count();
        
        if ($earlyWarnings > 0) {
            $achievements[] = [
                'title' => 'Early Warning',
                'description' => "Identifikasi $earlyWarnings risiko tinggi yang sedang dalam mitigasi.",
                'icon' => 'alert-triangle'
            ];
        }
        
        return $achievements;
    }

    private function getOrganizationPerformance($risks)
    {
        $orgPerformance = [];
        
        $risksByOrg = $risks->groupBy('risk_organization_id');
        
        foreach ($risksByOrg as $orgId => $orgRisks) {
            $org = $orgRisks->first()->organization ?? null;
            if (!$org) continue;
            
            $highRisks = $orgRisks->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])->count();
            $mitigationCompletion = 0;
            $totalMitigations = 0;
            
            foreach ($orgRisks as $risk) {
                $totalMitigations += $risk->mitigations->count();
                $mitigationCompletion += $risk->mitigations->where('status', 'selesai')->count();
            }
            
            $orgPerformance[] = [
                'organization_name' => $org->organization_name,
                'total_risks' => $orgRisks->count(),
                'high_risks' => $highRisks,
                'avg_risk_score' => round($orgRisks->avg('risk_score') ?? 0, 2),
                'mitigation_completion_rate' => $totalMitigations > 0 ? 
                    round(($mitigationCompletion / $totalMitigations) * 100, 2) : 0,
                'performance_score' => $this->calculatePerformanceScore($orgRisks),
            ];
        }
        
        // Sort by performance score
        usort($orgPerformance, function($a, $b) {
            return $b['performance_score'] <=> $a['performance_score'];
        });
        
        return $orgPerformance;
    }

    private function calculatePerformanceScore($risks)
    {
        $score = 0;
        
        // Risk reduction (40%)
        $riskReduction = $this->calculateRiskReduction($risks);
        $score += ($riskReduction['reduction_rate'] * 0.4);
        
        // Mitigation completion (30%)
        $completionRate = $this->calculateCompletionRate($risks);
        $score += ($completionRate * 0.3);
        
        // Monitoring coverage (20%)
        $monitoringCoverage = $this->calculateMonitoringCoverage($risks);
        $score += ($monitoringCoverage * 0.2);
        
        // Low high-risk ratio (10%)
        $highRisks = $risks->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])->count();
        $totalRisks = $risks->count();
        $highRiskRatio = $totalRisks > 0 ? ($highRisks / $totalRisks) * 100 : 0;
        $score += (100 - $highRiskRatio) * 0.1;
        
        return round($score, 2);
    }

    private function calculateOnTimeCompletion($mitigations)
{
    $onTimeCount = 0;
    
    foreach ($mitigations as $mitigation) {
        if ($mitigation->actual_completion_date && 
            $mitigation->target_completion_date) {
            if ($mitigation->actual_completion_date <= $mitigation->target_completion_date) {
                $onTimeCount++;
            }
        }
    }
    
    return $mitigations->count() > 0 ? 
        round(($onTimeCount / $mitigations->count()) * 100, 2) : 
        0;
}

private function calculateBudgetVariance($mitigations)
{
    $totalBudget = $mitigations->sum('budget');
    $totalActualCost = $mitigations->sum('actual_cost');
    
    return $totalBudget > 0 ? 
        round((($totalBudget - $totalActualCost) / $totalBudget) * 100, 2) : 
        0;
}

private function calculateRiskReductionEffectiveness($mitigations)
{
    $totalReduction = 0;
    $mitigationsWithReduction = 0;
    
    foreach ($mitigations as $mitigation) {
        $risk = $mitigation->risk;
        if ($risk && $risk->analyses->count() >= 2) {
            $firstScore = $risk->analyses->first()->risk_score;
            $latestScore = $risk->analyses->last()->risk_score;
            
            if ($latestScore < $firstScore) {
                $totalReduction += ($firstScore - $latestScore);
                $mitigationsWithReduction++;
            }
        }
    }
    
    return [
        'total_reduction' => $totalReduction,
        'mitigations_with_reduction' => $mitigationsWithReduction,
        'average_reduction' => $mitigationsWithReduction > 0 ? 
            round($totalReduction / $mitigationsWithReduction, 2) : 0,
        'effectiveness_rate' => $mitigations->count() > 0 ? 
            round(($mitigationsWithReduction / $mitigations->count()) * 100, 2) : 0,
    ];
}

private function getTopPerformingMitigations($mitigations, $limit = 5)
{
    $performances = [];
    
    foreach ($mitigations as $mitigation) {
        $performanceScore = $this->calculateMitigationPerformanceScore($mitigation);
        $performances[] = [
            'mitigation' => $mitigation,
            'score' => $performanceScore,
        ];
    }
    
    // Sort by performance score
    usort($performances, function($a, $b) {
        return $b['score'] <=> $a['score'];
    });
    
    return array_slice($performances, 0, $limit);
}

private function calculateMitigationPerformanceScore($mitigation)
{
    $score = 0;
    
    // Completion on time (30%)
    if ($mitigation->actual_completion_date && $mitigation->target_completion_date) {
        if ($mitigation->actual_completion_date <= $mitigation->target_completion_date) {
            $score += 30;
        } else {
            $daysLate = $mitigation->target_completion_date->diffInDays($mitigation->actual_completion_date);
            if ($daysLate <= 7) $score += 25;
            elseif ($daysLate <= 14) $score += 20;
            elseif ($daysLate <= 30) $score += 15;
            else $score += 5;
        }
    }
    
    // Budget adherence (25%)
    if ($mitigation->budget > 0) {
        $variance = abs(($mitigation->actual_cost - $mitigation->budget) / $mitigation->budget) * 100;
        if ($variance <= 5) $score += 25;
        elseif ($variance <= 10) $score += 20;
        elseif ($variance <= 15) $score += 15;
        elseif ($variance <= 20) $score += 10;
        else $score += 5;
    }
    
    // Risk reduction (25%)
    $risk = $mitigation->risk;
    if ($risk && $risk->analyses->count() >= 2) {
        $firstScore = $risk->analyses->first()->risk_score;
        $latestScore = $risk->analyses->last()->risk_score;
        
        if ($latestScore < $firstScore) {
            $reduction = (($firstScore - $latestScore) / $firstScore) * 100;
            if ($reduction >= 50) $score += 25;
            elseif ($reduction >= 30) $score += 20;
            elseif ($reduction >= 20) $score += 15;
            elseif ($reduction >= 10) $score += 10;
            else $score += 5;
        }
    }
    
    // Quality (20%)
    if (!empty($mitigation->result_description) && 
        !empty($mitigation->lesson_learned)) {
        $score += 20;
    } elseif (!empty($mitigation->result_description) || 
               !empty($mitigation->lesson_learned)) {
        $score += 10;
    }
    
    return round($score, 2);
}

private function getDelayedMitigations($mitigations)
{
    $delayed = [];
    
    foreach ($mitigations as $mitigation) {
        if ($mitigation->status !== 'selesai' && 
            $mitigation->target_completion_date) {
            
            $daysRemaining = now()->diffInDays($mitigation->target_completion_date, false);
            
            if ($daysRemaining < 0) {
                $delayed[] = [
                    'mitigation' => $mitigation,
                    'days_overdue' => abs($daysRemaining),
                    'risk_level' => $mitigation->risk->risk_level ?? 'medium',
                ];
            }
        }
    }
    
    // Sort by days overdue
    usort($delayed, function($a, $b) {
        return $b['days_overdue'] <=> $a['days_overdue'];
    });
    
    return array_slice($delayed, 0, 10);
}

private function analyzeResourceUtilization($mitigations)
{
    $totalBudget = $mitigations->sum('budget');
    $totalActualCost = $mitigations->sum('actual_cost');
    
    $byType = $mitigations->groupBy('mitigation_type')->map(function($group) {
        return [
            'count' => $group->count(),
            'total_budget' => $group->sum('budget'),
            'total_actual' => $group->sum('actual_cost'),
            'efficiency' => $group->sum('budget') > 0 ? 
                round(($group->sum('budget') - $group->sum('actual_cost')) / $group->sum('budget') * 100, 2) : 0,
        ];
    });
    
    return [
        'budget_utilization' => $totalBudget > 0 ? 
            round(($totalActualCost / $totalBudget) * 100, 2) : 0,
        'cost_variance' => $totalBudget - $totalActualCost,
        'by_type' => $byType,
        'average_cost_per_mitigation' => $mitigations->count() > 0 ? 
            round($totalActualCost / $mitigations->count(), 2) : 0,
    ];
}

private function calculateCostEffectiveness($mitigations)
{
    $totalRiskReduction = 0;
    $totalCost = $mitigations->sum('actual_cost');
    
    foreach ($mitigations as $mitigation) {
        $risk = $mitigation->risk;
        if ($risk && $risk->analyses->count() >= 2) {
            $firstScore = $risk->analyses->first()->risk_score;
            $latestScore = $risk->analyses->last()->risk_score;
            
            if ($latestScore < $firstScore) {
                $totalRiskReduction += ($firstScore - $latestScore);
            }
        }
    }
    
    return [
        'total_risk_reduction' => $totalRiskReduction,
        'total_cost' => $totalCost,
        'cost_per_risk_point' => $totalRiskReduction > 0 ? 
            round($totalCost / $totalRiskReduction, 2) : 0,
        'effectiveness_ratio' => $totalCost > 0 ? 
            round($totalRiskReduction / $totalCost, 4) : 0,
    ];
}

    private function generateMitigationRecommendations($mitigations)
    {
        $recommendations = [];
        
        // Delayed mitigations
        $delayedCount = $this->getDelayedMitigations($mitigations);
        if (count($delayedCount) > 0) {
            $recommendations[] = [
                'type' => 'urgent',
                'title' => 'Percepat Mitigasi Tertunda',
                'description' => count($delayedCount) . ' mitigasi melewati tanggal target.',
                'action' => 'Review dan alokasi ulang resource untuk mitigasi tertunda.'
            ];
        }
        
        // Budget overruns
        $overrunCount = $mitigations->filter(function($mitigation) {
            return $mitigation->actual_cost > $mitigation->budget * 1.2; // 20% over budget
        })->count();
        
        if ($overrunCount > 0) {
            $recommendations[] = [
                'type' => 'financial',
                'title' => 'Kendalikan Biaya Mitigasi',
                'description' => "$overrunCount mitigasi melebihi budget lebih dari 20%.",
                'action' => 'Implementasi kontrol biaya dan review budget lebih ketat.'
            ];
        }
        
        // Low effectiveness
        $lowEffectiveness = $mitigations->filter(function($mitigation) {
            $score = $this->calculateMitigationPerformanceScore($mitigation);
            return $score < 60;
        })->count();
        
        if ($lowEffectiveness > 0) {
            $recommendations[] = [
                'type' => 'improvement',
                'title' => 'Tingkatkan Efektivitas',
                'description' => "$lowEffectiveness mitigasi memiliki skor efektivitas di bawah 60%.",
                'action' => 'Training dan implementasi best practices untuk mitigasi.'
            ];
        }
        
        return $recommendations;
    }

    private function getPreviousPeriod($period)
    {
        $now = Carbon::now();
        
        switch ($period) {
            case 'bulanan':
                $start = $now->copy()->subMonth()->startOfMonth();
                $end = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'triwulan':
                $quarter = ceil($now->month / 3);
                $previousQuarter = $quarter - 1;
                if ($previousQuarter < 1) {
                    $previousQuarter = 4;
                    $year = $now->year - 1;
                } else {
                    $year = $now->year;
                }
                $startMonth = (($previousQuarter - 1) * 3) + 1;
                $start = Carbon::create($year, $startMonth, 1);
                $end = $start->copy()->addMonths(3)->subDay();
                break;
            case 'tahunan':
                $start = $now->copy()->subYear()->startOfYear();
                $end = $now->copy()->subYear()->endOfYear();
                break;
            default:
                return null;
        }
        
        return [
            'start' => $start->toDateString(),
            'end' => $end->toDateString()
        ];
    }

    private function estimateFinancialImpact($risks)
    {
        $totalEstimatedImpact = 0;
        $totalActualImpact = 0;
        $risksWithImpact = 0;
        
        foreach ($risks as $risk) {
            if ($risk->financial_impact_estimate) {
                $totalEstimatedImpact += $risk->financial_impact_estimate;
                $risksWithImpact++;
            }
            
            if ($risk->actual_financial_impact) {
                $totalActualImpact += $risk->actual_financial_impact;
            }
        }
        
        return [
            'total_estimated' => $totalEstimatedImpact,
            'total_actual' => $totalActualImpact,
            'risks_with_estimate' => $risksWithImpact,
            'average_estimate' => $risksWithImpact > 0 ? 
                round($totalEstimatedImpact / $risksWithImpact, 2) : 0,
            'savings_from_mitigation' => max(0, $totalEstimatedImpact - $totalActualImpact),
        ];
    }

    private function generateStrategicRecommendations($risks)
    {
        $recommendations = [];
        
        // High concentration in one category
        $categoryDistribution = $risks->groupBy('category.risk_category_name')->map->count();
        if ($categoryDistribution->count() > 0) {
            $maxCategory = $categoryDistribution->keys()->first();
            $maxCount = $categoryDistribution->first();
            $percentage = round(($maxCount / $risks->count()) * 100, 2);
            
            if ($percentage > 40) {
                $recommendations[] = [
                    'type' => 'strategic',
                    'title' => 'Diversifikasi Portfolio Risiko',
                    'description' => "$percentage% risiko terkonsentrasi pada kategori '$maxCategory'.",
                    'action' => 'Kembangkan strategi untuk diversifikasi dan mitigasi risiko pada kategori ini.'
                ];
            }
        }
        
        // Recurring risks
        $recurringRisks = $risks->filter(function($risk) {
            return $risk->is_recurring == true;
        });
        
        if ($recurringRisks->count() > 3) {
            $recommendations[] = [
                'type' => 'process',
                'title' => 'Tangani Risiko Berulang',
                'description' => $recurringRisks->count() . ' risiko teridentifikasi sebagai risiko berulang.',
                'action' => 'Implementasi sistem permanen untuk menangani risiko berulang.'
            ];
        }
        
        // Opportunities
        $opportunityRisks = $risks->where('risk_type', 'opportunity');
        if ($opportunityRisks->count() > 0) {
            $recommendations[] = [
                'type' => 'opportunity',
                'title' => 'Eksplorasi Peluang',
                'description' => $opportunityRisks->count() . ' peluang teridentifikasi.',
                'action' => 'Kembangkan rencana untuk mengeksplorasi dan memanfaatkan peluang.'
            ];
        }
        
        return $recommendations;
    }

    private function getNextPeriodFocus($risks, $period)
    {
        $focusAreas = [];
        
        // High risks without mitigation
        $highRisksWithoutMitigation = $risks
            ->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
            ->filter(function($risk) {
                return $risk->mitigations->isEmpty();
            });
        
        if ($highRisksWithoutMitigation->count() > 0) {
            $focusAreas[] = [
                'priority' => 'high',
                'area' => 'Mitigasi Risiko Tinggi',
                'description' => "{$highRisksWithoutMitigation->count()} risiko tinggi tanpa mitigasi.",
                'target' => 'Kembangkan rencana mitigasi untuk semua risiko tinggi.'
            ];
        }
        
        // Old risks without recent monitoring
        $oldRisks = $risks->filter(function($risk) {
            if ($risk->monitorings->isEmpty()) {
                return $risk->created_at->diffInDays(now()) > 60;
            }
            
            $latestMonitoring = $risk->monitorings->sortByDesc('monitoring_date')->first();
            return $latestMonitoring && 
                Carbon::parse($latestMonitoring->monitoring_date)->diffInDays(now()) > 90;
        });
        
        if ($oldRisks->count() > 0) {
            $focusAreas[] = [
                'priority' => 'medium',
                'area' => 'Pemantauan Risiko',
                'description' => "{$oldRisks->count()} risiko belum dipantau dalam 3 bulan terakhir.",
                'target' => 'Jadwalkan pemantauan untuk semua risiko yang belum dipantau.'
            ];
        }
        
        // Ineffective mitigations
        $ineffectiveMitigations = 0;
        foreach ($risks as $risk) {
            foreach ($risk->mitigations as $mitigation) {
                if ($mitigation->status === 'selesai') {
                    $score = $this->calculateMitigationPerformanceScore($mitigation);
                    if ($score < 60) {
                        $ineffectiveMitigations++;
                    }
                }
            }
        }
        
        if ($ineffectiveMitigations > 0) {
            $focusAreas[] = [
                'priority' => 'medium',
                'area' => 'Efektivitas Mitigasi',
                'description' => "$ineffectiveMitigations mitigasi dengan efektivitas rendah.",
                'target' => 'Review dan perbaiki mitigasi dengan efektivitas rendah.'
            ];
        }
        
        return $focusAreas;
    }

    // ==================== API METHODS ====================

    public function getReportStatistics()
    {
        $total = Report::count();
        $byType = Report::select('report_type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('report_type')
            ->get();
        
        $byStatus = Report::select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $bySchedule = Report::select('schedule_id')
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull('schedule_id')
            ->groupBy('schedule_id')
            ->with('schedule')
            ->get();
        
        $recent = Report::where('report_date', '>=', Carbon::now()->subMonth())->count();
        
        return response()->json([
            'total_reports' => $total,
            'reports_by_type' => $byType,
            'reports_by_status' => $byStatus,
            'reports_by_schedule' => $bySchedule,
            'recent_reports' => $recent
        ]);
    }

    public function getTrendData(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $type = $request->get('type');
        
        $query = Report::query();
        
        if ($type) {
            $query->where('report_type', $type);
        }
        
        $data = [];
        $now = Carbon::now();
        
        if ($period === 'monthly') {
            for ($i = 11; $i >= 0; $i--) {
                $month = $now->copy()->subMonths($i);
                $monthKey = $month->format('Y-m');
                $monthName = $month->format('M Y');
                
                $count = $query->clone()
                    ->whereYear('report_date', $month->year)
                    ->whereMonth('report_date', $month->month)
                    ->count();
                
                $data[$monthKey] = [
                    'period' => $monthName,
                    'count' => $count
                ];
            }
        }
        
        return response()->json($data);
    }

    public function getScheduledReportsStats()
    {
        $scheduledReports = Report::whereNotNull('schedule_id')
            ->selectRaw('schedule_id, COUNT(*) as total, 
                         SUM(CASE WHEN status = "published" THEN 1 ELSE 0 END) as published,
                         SUM(CASE WHEN file_path IS NOT NULL THEN 1 ELSE 0 END) as with_files')
            ->groupBy('schedule_id')
            ->with('schedule')
            ->get();

        return response()->json([
            'scheduled_reports' => $scheduledReports,
            'total_scheduled' => $scheduledReports->sum('total'),
            'total_published' => $scheduledReports->sum('published'),
        ]);
    }
}