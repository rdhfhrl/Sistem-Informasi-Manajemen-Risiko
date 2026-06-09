<?php

namespace App\Http\Controllers;

use App\Models\Risk;
use App\Models\RiskCategory;
use App\Models\Organization;
use App\Models\StrategicObjective;
use App\Models\BusinessProcess;
use App\Models\Project;
use App\Models\User;
use App\Models\RiskIdentification;
use App\Models\RiskAnalysis;
use App\Models\RiskEvaluation;
use App\Models\RiskIndicator;
use App\Models\RiskMitigation;
use App\Models\RiskMonitoring;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RiskController extends Controller
{
    public function index(Request $request)
    {
        $query = Risk::with(['category', 'organization', 'project', 'user', 'strategicObjective', 'businessProcess']);
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('risk_code', 'like', "%{$search}%")
                ->orWhere('risk_description', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan request
        if ($request->filled('category')) {
            $query->where('risk_category_id', $request->category);
        }
        
        if ($request->filled('organization')) {
            $query->where('risk_organization_id', $request->organization);
        }
        
        if ($request->filled('project')) {
            $query->where('risk_pro_id', $request->project);
        }
        
        if ($request->filled('status')) {
            $query->where('risk_level', $request->status);
        }
        
        // Sorting
        $sort = $request->get('sort', 'score_desc');
        switch ($sort) {
            case 'score_asc':
                $query->orderBy('risk_score', 'asc');
                break;
            case 'date_desc':
                $query->orderBy('created_at', 'desc');
                break;
            case 'date_asc':
                $query->orderBy('created_at', 'asc');
                break;
            default: // score_desc
                $query->orderBy('risk_score', 'desc');
        }
        
        $risks = $query->orderBy('risk_score', 'desc')
            ->paginate(20);
        
        $categories = RiskCategory::all();
        $organizations = Organization::all();
        $projects = Project::where('pro_status', 'Aktif')->get();
        
        return view('risks.index', compact('risks', 'categories', 'organizations', 'projects'));
    }

    public function create()
    {
        $categories = RiskCategory::orderBy('risk_category_name')->get();
        $organizations = Organization::orderBy('organization_name')->get();
        $projects = Project::where('pro_status', 'Aktif')->orderBy('pro_nama')->get();
        $objectives = StrategicObjective::orderBy('strategic_objective_name')->get();
        $processes = BusinessProcess::orderBy('business_process_name')->get();
        $users = User::where('role', 'unit_pemilik_risiko')->orderBy('name')->get();
        
        return view('risks.create', compact('categories', 'organizations', 'projects', 'objectives', 'processes', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'risk_pro_id' => 'required|exists:project,pro_id',
            'risk_organization_id' => 'required|exists:organizations,organization_id',
            'risk_strategic_objective_id' => 'required|exists:strategic_objectives,strategic_objective_id',
            'risk_business_process_id' => 'required|exists:business_processes,business_process_id',
            'risk_category_id' => 'required|exists:risk_categories,risk_category_id',
            'risk_description' => 'required|string',
            'risk_user_id' => 'required|exists:users,id',
        ]);

        // Generate risk code
        $lastRisk = Risk::orderBy('risk_id', 'desc')->first();
        $nextNumber = $lastRisk ? (int) substr($lastRisk->risk_code, 5) + 1 : 1;
        $riskCode = 'RISK-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $risk = Risk::create([
            'risk_code' => $riskCode,
            ...$validated
        ]);

        // PERBAIKAN: Gunakan array syntax untuk route parameter
        return redirect()->route('risks.show', ['risk' => $risk->risk_id])
            ->with('success', 'Risiko berhasil ditambahkan dengan kode: ' . $riskCode);
    }

    public function show(Risk $risk)
    {
        $risk->load([
            'category',
            'organization',
            'project',
            'strategicObjective',
            'businessProcess',
            'user',
            'identification',
            'analyses' => function($query) {
                $query->orderBy('analysis_date', 'desc');
            },
            'evaluations' => function($query) {
                $query->orderBy('evaluation_date', 'desc');
            },
            'indicators',
            'mitigations' => function($query) {
                $query->orderBy('deadline');
            },
            'monitorings' => function($query) {
                $query->orderBy('monitoring_date', 'desc');
            }
        ]);
        
        return view('risks.show', compact('risk'));
    }

    public function edit(Risk $risk)
    {
        $categories = RiskCategory::orderBy('risk_category_name')->get();
        $organizations = Organization::orderBy('organization_name')->get();
        $projects = Project::where('pro_status', 'Aktif')->orderBy('pro_nama')->get();
        $objectives = StrategicObjective::where('strategic_objective_organization_id', $risk->risk_organization_id)
            ->orderBy('strategic_objective_name')->get();
        $processes = BusinessProcess::where('business_process_organization_id', $risk->risk_organization_id)
            ->orderBy('business_process_name')->get();
        $users = User::where('role', 'unit_pemilik_risiko')->orderBy('name')->get();
        
        return view('risks.edit', compact('risk', 'categories', 'organizations', 'projects', 'objectives', 'processes', 'users'));
    }

    public function update(Request $request, Risk $risk)
    {
        $validated = $request->validate([
            'risk_pro_id' => 'required|exists:project,pro_id',
            'risk_organization_id' => 'required|exists:organizations,organization_id',
            'risk_strategic_objective_id' => 'required|exists:strategic_objectives,strategic_objective_id',
            'risk_business_process_id' => 'required|exists:business_processes,business_process_id',
            'risk_category_id' => 'required|exists:risk_categories,risk_category_id',
            'risk_description' => 'required|string',
            'risk_user_id' => 'required|exists:users,id',
        ]);

        $risk->update($validated);

        return redirect()->route('risks.show', ['risk' => $risk->risk_id])
            ->with('success', 'Risiko berhasil diperbarui.');
    }

    public function destroy(Risk $risk)
    {
        $risk->delete();

        return redirect()->route('risks.index')
            ->with('success', 'Risiko berhasil dihapus.');
    }

    // ==================== RISK IDENTIFICATION METHODS ====================
    
    public function storeIdentification(Request $request, $riskId)
    {
        $risk = Risk::findOrFail($riskId);
        
        $validated = $request->validate([
            'loss_type' => 'nullable|in:Reputasi,Operasional,Kepatuhan,Lainnya',
            'violation_type' => 'nullable|in:Hukum,SOP,Kontrak,Lainnya',
            'failure_type' => 'nullable|in:Manusia,Proses,Sistem,Lainnya',
            'error_type' => 'nullable|in:Human Error,Technical Error,Lainnya',
        ]);

        $identification = RiskIdentification::updateOrCreate(
            ['risk_identification_risk_id' => $riskId],
            $validated
        );

        return redirect()->route('risks.show', ['risk' => $riskId])
            ->with('success', 'Identifikasi risiko berhasil disimpan.');
    }

    // ==================== RISK ANALYSIS METHODS ====================
    
    public function storeAnalysis(Request $request, $riskId)
    {
        $risk = Risk::findOrFail($riskId);
        
        $validated = $request->validate([
            'likelihood_level' => 'required|integer|between:1,5',
            'impact_level' => 'required|integer|between:1,5',
            'analysis_date' => 'required|date',
        ]);

        $riskScore = $validated['likelihood_level'] * $validated['impact_level'];
        $riskLevel = $this->calculateRiskLevel($riskScore);
        
        $analysis = RiskAnalysis::create([
            'risk_analysis_risk_id' => $riskId,
            'likelihood_level' => $validated['likelihood_level'],
            'impact_level' => $validated['impact_level'],
            'risk_score' => $riskScore,
            'risk_level' => $riskLevel,
            'analysis_date' => $validated['analysis_date'],
        ]);

        // Update data risiko utama
        $risk->update([
            'likelihood_level' => $validated['likelihood_level'],
            'impact_level' => $validated['impact_level'],
            'risk_score' => $riskScore,
            'risk_level' => $riskLevel,
            'last_analysis_date' => $validated['analysis_date'],
        ]);

        return redirect()->route('risks.show', ['risk' => $riskId])
            ->with('success', 'Analisis risiko berhasil disimpan.');
    }

    // ==================== RISK EVALUATION METHODS ====================
    
     public function storeEvaluation(Request $request, $riskId)
    {
        $validated = $request->validate([
            'risk_evaluation_priority' => 'required|in:rendah,sedang,tinggi,sangat tinggi',
            'mitigation_decision' => 'required|string',
            'projected_risk_score' => 'nullable|numeric|min:1|max:25',
            'evaluation_date' => 'required|date',
        ]);

        $evaluation = RiskEvaluation::create([
            'risk_evaluation_risk_id' => $riskId,
            ...$validated
        ]);

        // PERBAIKAN: Gunakan array syntax
        return redirect()->route('risks.show', ['risk' => $riskId])
            ->with('success', 'Evaluasi risiko berhasil disimpan.');
    }

    // ==================== RISK INDICATOR METHODS ====================
    
    public function storeIndicator(Request $request, $riskId)
    {
        $validated = $request->validate([
            'indicator_type' => 'required|in:akar_masalah,penyebab,dampak,lainnya',
            'indicator_name' => 'required|string|max:255',
            'indicator_description' => 'nullable|string',
            'threshold' => 'required|numeric',
            'unit' => 'nullable|string|max:50',
        ]);

        $indicator = RiskIndicator::create([
            'risk_indicator_risk_id' => $riskId,
            ...$validated
        ]);

        // PERBAIKAN: Gunakan array syntax
        return redirect()->route('risks.show', ['risk' => $riskId])
            ->with('success', 'Indikator risiko berhasil ditambahkan.');
    }

    // ==================== RISK MITIGATION METHODS ====================
    
    public function storeMitigation(Request $request, $riskId)
    {
        $validated = $request->validate([
            'mitigation_plan' => 'required|string',
            'responsible_party' => 'required|string|max:255',
            'deadline' => 'required|date',
            'status' => 'required|in:belum dimulai,dalam proses,selesai,ditunda,dibatalkan',
        ]);

        $mitigation = RiskMitigation::create([
            'risk_mitigation_risk_id' => $riskId,
            ...$validated
        ]);

        // PERBAIKAN: Gunakan array syntax
        return redirect()->route('risks.show', ['risk' => $riskId])
            ->with('success', 'Rencana mitigasi berhasil ditambahkan.');
    }

    public function updateMitigationStatus(Request $request, $mitigationId)
    {
        $validated = $request->validate([
            'status' => 'required|in:belum dimulai,dalam proses,selesai,ditunda,dibatalkan',
        ]);

        $mitigation = RiskMitigation::findOrFail($mitigationId);
        $mitigation->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Status mitigasi berhasil diperbarui.'
        ]);
    }

    // ==================== RISK MONITORING METHODS ====================
    
    public function storeMonitoring(Request $request, $riskId)
    {
        $validated = $request->validate([
            'current_risk_score' => 'required|numeric|min:1|max:25',
            'monitoring_result' => 'nullable|string',
            'monitoring_report' => 'nullable|string',
            'monitoring_date' => 'required|date',
        ]);

        $monitoring = RiskMonitoring::create([
            'risk_monitoring_risk_id' => $riskId,
            ...$validated
        ]);

        // Update risk score based on monitoring
        $risk = Risk::findOrFail($riskId);
        $risk->update([
            'risk_score' => $validated['current_risk_score'],
            'risk_level' => $this->calculateRiskLevel($validated['current_risk_score']),
        ]);

        // PERBAIKAN: Gunakan array syntax
        return redirect()->route('risks.show', ['risk' => $riskId])
            ->with('success', 'Pemantauan risiko berhasil disimpan.');
    }

    // ==================== HELPER METHODS ====================
    
    private function calculateRiskLevel($score)
    {
        if ($score >= 20) return 'sangat_tinggi';
        if ($score >= 15) return 'tinggi';
        if ($score >= 10) return 'sedang';
        if ($score >= 5) return 'rendah';
        return 'sangat_rendah';
    }

    // ==================== API METHODS ====================
    
    public function getRiskMatrix()
    {
        $matrix = [];
        for ($likelihood = 1; $likelihood <= 5; $likelihood++) {
            for ($impact = 1; $impact <= 5; $impact++) {
                $score = $likelihood * $impact;
                $level = $this->calculateRiskLevel($score);
                
                $count = Risk::where('likelihood_level', $likelihood)
                    ->where('impact_level', $impact)
                    ->count();
                
                $matrix[$likelihood][$impact] = [
                    'score' => $score,
                    'level' => $level,
                    'count' => $count,
                    'color' => $this->getRiskColor($level)
                ];
            }
        }
        
        return response()->json($matrix);
    }

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

    public function getHighRisks()
    {
        return Risk::whereHas('analyses', function($query) {
            $query->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
                ->whereRaw('risk_analyses.analysis_date = (
                    SELECT MAX(analysis_date) 
                    FROM risk_analyses 
                    WHERE risk_analysis_risk_id = risk.risk_id
                )');
        })
        ->with(['category', 'organization', 'project', 'analyses' => function($query) {
            $query->latest('analysis_date')->limit(1);
        }])
        ->get();
    }
}