<?php

namespace App\Http\Controllers;

use App\Models\Risk;
use App\Models\RiskEvaluation;
use App\Models\RiskAnalysis;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RiskEvaluationController extends Controller
{
    public function index()
    {
        // Redirect ke halaman pilih risiko
        return view('risk-evaluations.select-risk');
    }

    public function all()
    {
        // Ambil semua evaluasi dengan pagination
        $evaluations = RiskEvaluation::with('risk')
            ->orderBy('evaluation_date', 'desc')
            ->paginate(15);
        
        return view('risk-evaluations.all', compact('evaluations'));
    }

    public function byRisk($riskId)
    {
        $risk = Risk::findOrFail($riskId);
        $evaluations = RiskEvaluation::where('risk_evaluation_risk_id', $riskId)
            ->orderBy('evaluation_date', 'desc')
            ->paginate(10);
        
        return view('risk-evaluations.by-risk', compact('risk', 'evaluations'));
    }

    public function create($riskId)
    {
        $risk = Risk::with(['analyses' => function($query) {
            $query->orderBy('analysis_date', 'desc');
        }])->findOrFail($riskId);
        
        $latestAnalysis = $risk->analyses->first();
        $latestEvaluation = $risk->evaluations()->latest()->first();
        
        return view('risk-evaluations.create', compact('risk', 'latestAnalysis', 'latestEvaluation'));
    }

    public function store(Request $request, $riskId)
    {
        $validated = $request->validate([
            'risk_evaluation_priority' => 'required|in:rendah,sedang,tinggi,sangat tinggi',
            'mitigation_decision' => 'required|string',
            'projected_risk_score' => 'nullable|numeric|min:1|max:25',
            'evaluation_date' => 'required|date',
            'evaluation_notes' => 'nullable|string',
        ]);

        $evaluation = RiskEvaluation::create([
            'risk_evaluation_risk_id' => $riskId,
            ...$validated
        ]);

        return redirect()->route('risk-evaluations.index', $riskId)
            ->with('success', 'Evaluasi risiko berhasil disimpan.');
    }

    public function show($riskId, $evaluationId)
    {
        $risk = Risk::findOrFail($riskId);
        $evaluation = RiskEvaluation::where('risk_evaluation_risk_id', $riskId)
            ->findOrFail($evaluationId);
        
        return view('risk-evaluations.show', compact('risk', 'evaluation'));
    }

    public function edit($riskId, $evaluationId)
    {
        $risk = Risk::findOrFail($riskId);
        $evaluation = RiskEvaluation::where('risk_evaluation_risk_id', $riskId)
            ->findOrFail($evaluationId);
        
        return view('risk-evaluations.edit', compact('risk', 'evaluation'));
    }

    public function update(Request $request, $riskId, $evaluationId)
    {
        $validated = $request->validate([
            'risk_evaluation_priority' => 'required|in:rendah,sedang,tinggi,sangat tinggi',
            'mitigation_decision' => 'required|string',
            'projected_risk_score' => 'nullable|numeric|min:1|max:25',
            'evaluation_date' => 'required|date',
            'evaluation_notes' => 'nullable|string',
        ]);

        $evaluation = RiskEvaluation::where('risk_evaluation_risk_id', $riskId)
            ->findOrFail($evaluationId);
        
        $evaluation->update($validated);

        return redirect()->route('risk-evaluations.index', $riskId)
            ->with('success', 'Evaluasi risiko berhasil diperbarui.');
    }

    public function destroy($riskId, $evaluationId)
    {
        $evaluation = RiskEvaluation::where('risk_evaluation_risk_id', $riskId)
            ->findOrFail($evaluationId);
        
        $evaluation->delete();

        return redirect()->route('risk-evaluations.index', $riskId)
            ->with('success', 'Evaluasi risiko berhasil dihapus.');
    }

    public function getEvaluationsByPriority($priority)
    {
        $evaluations = RiskEvaluation::where('risk_evaluation_priority', $priority)
            ->with('risk')
            ->orderBy('evaluation_date', 'desc')
            ->get();
        
        return response()->json($evaluations);
    }

    public function getEvaluationStatistics()
    {
        $total = RiskEvaluation::count();
        
        $byPriority = RiskEvaluation::select('risk_evaluation_priority')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('risk_evaluation_priority')
            ->get();
        
        $recentMonth = Carbon::now()->subMonth();
        $recentCount = RiskEvaluation::where('evaluation_date', '>=', $recentMonth)->count();
        
        return response()->json([
            'total_evaluations' => $total,
            'by_priority' => $byPriority,
            'recent_evaluations' => $recentCount
        ]);
    }
}