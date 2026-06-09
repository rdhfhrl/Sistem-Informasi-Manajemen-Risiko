<?php

namespace App\Http\Controllers;

use App\Models\Risk;
use App\Models\RiskAnalysis;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RiskAnalysisController extends Controller
{
    public function index()
    {
        // Redirect ke halaman pilih risiko
        return view('risk-analyses.select-risk');
    }

    public function allAnalyses()
    {
        // Ambil semua analisis dengan pagination
        $analyses = RiskAnalysis::with('risk')
            ->orderBy('analysis_date', 'desc')
            ->paginate(15);
        
        // Hitung statistik
        $totalAnalyses = RiskAnalysis::count();
        $highRiskCount = RiskAnalysis::whereIn('risk_level', ['tinggi', 'sangat_tinggi'])->count();
        $mediumRiskCount = RiskAnalysis::where('risk_level', 'sedang')->count();
        $lowRiskCount = RiskAnalysis::whereIn('risk_level', ['rendah', 'sangat_rendah'])->count();
        
        return view('risk-analyses.all', compact(
            'analyses', 
            'totalAnalyses', 
            'highRiskCount', 
            'mediumRiskCount', 
            'lowRiskCount'
        ));
    }

    public function byRisk($riskId)
    {
        // Analisis berdasarkan risiko tertentu (method existing)
        $risk = Risk::findOrFail($riskId);
        $analyses = RiskAnalysis::where('risk_analysis_risk_id', $riskId)
            ->orderBy('analysis_date', 'desc')
            ->paginate(10);
        
        return view('risk-analyses.by-risk', compact('risk', 'analyses'));
    }

    public function create($riskId)
    {
        $risk = Risk::findOrFail($riskId);
        $latestAnalysis = $risk->analyses()->latest()->first();
        
        return view('risk-analyses.create', compact('risk', 'latestAnalysis'));
    }

    public function store(Request $request, $riskId)
    {
        $risk = Risk::findOrFail($riskId);
        
        $validated = $request->validate([
            'likelihood_level' => 'required|integer|between:1,5',
            'impact_level' => 'required|integer|between:1,5',
            'analysis_date' => 'required|date',
            'analysis_notes' => 'nullable|string',
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

        // Update risk with latest analysis
        $risk = Risk::find($riskId);
        $risk->update([
            'risk_level' => $riskLevel,
            'risk_score' => $riskScore,
            'likelihood_level' => $validated['likelihood_level'],
            'impact_level' => $validated['impact_level'],
            'last_analysis_date' => $validated['analysis_date'],
        ]);

        return redirect()->route('risk-analyses.index', $riskId)
            ->with('success', 'Analisis risiko berhasil disimpan.');
    }

    public function show($riskId, $analysisId)
    {
        $risk = Risk::findOrFail($riskId);
        $analysis = RiskAnalysis::where('risk_analysis_risk_id', $riskId)
            ->findOrFail($analysisId);
        
        return view('risk-analyses.show', compact('risk', 'analysis'));
    }

    public function edit($riskId, $analysisId)
    {
        $risk = Risk::findOrFail($riskId);
        $analysis = RiskAnalysis::where('risk_analysis_risk_id', $riskId)
            ->findOrFail($analysisId);
        
        return view('risk-analyses.edit', compact('risk', 'analysis'));
    }

    public function update(Request $request, $riskId, $analysisId)
    {
        $validated = $request->validate([
            'likelihood_level' => 'required|integer|between:1,5',
            'impact_level' => 'required|integer|between:1,5',
            'analysis_date' => 'required|date',
            'analysis_notes' => 'nullable|string',
        ]);

        $riskScore = $validated['likelihood_level'] * $validated['impact_level'];
        $riskLevel = $this->calculateRiskLevel($riskScore);
        
        $analysis = RiskAnalysis::where('risk_analysis_risk_id', $riskId)
            ->findOrFail($analysisId);
        
        $analysis->update([
            'likelihood_level' => $validated['likelihood_level'],
            'impact_level' => $validated['impact_level'],
            'risk_score' => $riskScore,
            'risk_level' => $riskLevel,
            'analysis_date' => $validated['analysis_date'],
        ]);

        // Update risk if this is the latest analysis
        $latestAnalysis = RiskAnalysis::where('risk_analysis_risk_id', $riskId)
            ->latest('analysis_date')
            ->first();
        
        if ($latestAnalysis && $latestAnalysis->risk_analysis_id == $analysisId) {
            $risk = Risk::findOrFail($riskId);
            $risk->update([
                'likelihood_level' => $validated['likelihood_level'],
                'impact_level' => $validated['impact_level'],
                'risk_score' => $riskScore,
                'risk_level' => $riskLevel,
                'last_analysis_date' => $validated['analysis_date'],
            ]);
        }

        return redirect()->route('risk-analyses.index', $riskId)
            ->with('success', 'Analisis risiko berhasil diperbarui.');
    }

    public function destroy($riskId, $analysisId)
    {
        $analysis = RiskAnalysis::where('risk_analysis_risk_id', $riskId)
            ->findOrFail($analysisId);
        
        $analysis->delete();

        // Update risk with the latest remaining analysis
        $latestAnalysis = RiskAnalysis::where('risk_analysis_risk_id', $riskId)
            ->latest('analysis_date')
            ->first();
        
        if ($latestAnalysis) {
            $risk = Risk::findOrFail($riskId);
            $risk->update([
                'likelihood_level' => $latestAnalysis->likelihood_level,
                'impact_level' => $latestAnalysis->impact_level,
                'risk_score' => $latestAnalysis->risk_score,
                'risk_level' => $latestAnalysis->risk_level,
                'last_analysis_date' => $latestAnalysis->analysis_date,
            ]);
        }

        return redirect()->route('risk-analyses.index', $riskId)
            ->with('success', 'Analisis risiko berhasil dihapus.');
    }

    public function getAnalysisHistory($riskId)
    {
        $analyses = RiskAnalysis::where('risk_analysis_risk_id', $riskId)
            ->orderBy('analysis_date', 'desc')
            ->get();
        
        return response()->json($analyses);
    }

    public function getAnalysisTrend($riskId)
    {
        $analyses = RiskAnalysis::where('risk_analysis_risk_id', $riskId)
            ->orderBy('analysis_date')
            ->get(['analysis_date', 'risk_score', 'risk_level']);
        
        return response()->json($analyses);
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