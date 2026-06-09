<?php

namespace App\Http\Controllers;

use App\Models\Risk;
use App\Models\RiskMonitoring;
use App\Models\RiskAnalysis;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RiskMonitoringController extends Controller
{
    /**
     * Menampilkan halaman pilih risiko
     */
    public function index()
    {
        // Redirect ke halaman pilih risiko
        return view('risk-monitorings.select-risk');
    }

    /**
     * Menampilkan semua monitoring
     */
    public function all()
    {
        // Ambil semua monitoring dengan pagination
        $monitorings = RiskMonitoring::with(['risk' => function($query) {
            $query->with(['organization', 'project']);
        }])
        ->orderBy('monitoring_date', 'desc')
        ->paginate(15);
        
        // Statistik
        $stats = [
            'total' => RiskMonitoring::count(),
            'high_risk' => RiskMonitoring::whereIn('current_risk_level', ['tinggi', 'sangat_tinggi'])->count(),
            'average_effectiveness' => RiskMonitoring::avg('effectiveness_rating'),
            'overdue' => RiskMonitoring::whereNotNull('next_monitoring_date')
                ->where('next_monitoring_date', '<', now())
                ->count(),
        ];
        
        return view('risk-monitorings.all', compact('monitorings', 'stats'));
    }

    /**
     * Menampilkan monitoring berdasarkan risiko tertentu
     */
    public function byRisk($riskId)
    {
        $risk = Risk::with(['organization', 'project'])->findOrFail($riskId);
        $monitorings = RiskMonitoring::where('risk_monitoring_risk_id', $riskId)
            ->orderBy('monitoring_date', 'desc')
            ->paginate(10);
        
        // Tambahkan is_latest untuk setiap monitoring
        $monitorings->transform(function ($monitoring) use ($monitorings) {
            $monitoring->is_latest = $monitorings->first()->risk_monitoring_id === $monitoring->risk_monitoring_id;
            return $monitoring;
        });
        
        return view('risk-monitorings.by-risk', compact('risk', 'monitorings'));
    }

    /**
     * Tampilkan form create monitoring
     */
    public function create($riskId)
    {
        $risk = Risk::with(['analyses' => function($query) {
            $query->orderBy('analysis_date', 'desc');
        }])->findOrFail($riskId);
        
        $latestAnalysis = $risk->analyses->first();
        
        return view('risk-monitorings.create', compact('risk', 'latestAnalysis'));
    }

    /**
     * Simpan monitoring baru
     */
    public function store(Request $request, $riskId)
    {
        $validated = $request->validate([
            'monitoring_date' => 'required|date',
            'current_risk_score' => 'required|numeric|min:1|max:25',
            'monitoring_result' => 'nullable|string',
            'monitoring_report' => 'nullable|string',
            'effectiveness_rating' => 'nullable|integer|between:1,5',
            'monitored_by' => 'required|string|max:255',
            'next_monitoring_date' => 'nullable|date|after:monitoring_date',
            'recommendations' => 'nullable|string',
        ]);

        // Hitung level risiko
        $riskLevel = $this->calculateRiskLevel($validated['current_risk_score']);
        
        // Buat monitoring
        $monitoring = RiskMonitoring::create([
            'risk_monitoring_risk_id' => $riskId,
            'monitoring_date' => $validated['monitoring_date'],
            'current_risk_score' => $validated['current_risk_score'],
            'current_risk_level' => $riskLevel,
            'monitoring_result' => $validated['monitoring_result'],
            'monitoring_report' => $validated['monitoring_report'],
            'effectiveness_rating' => $validated['effectiveness_rating'],
            'monitored_by' => $validated['monitored_by'],
            'next_monitoring_date' => $validated['next_monitoring_date'],
            'recommendations' => $validated['recommendations'],
        ]);

        // Update risiko dengan data monitoring terbaru
        $risk = Risk::findOrFail($riskId);
        $this->updateRiskFromLatestMonitoring($risk);

        return redirect()->route('risk-monitorings.by-risk', $riskId)
            ->with('success', 'Pemantauan risiko berhasil disimpan.');
    }

    /**
     * Tampilkan detail monitoring
     */
    public function show($riskId, $monitoringId)
    {
        $risk = Risk::findOrFail($riskId);
        $monitoring = RiskMonitoring::where('risk_monitoring_risk_id', $riskId)
            ->findOrFail($monitoringId);
        
        // Hitung perubahan skor
        $previousMonitoring = RiskMonitoring::where('risk_monitoring_risk_id', $riskId)
            ->where('monitoring_date', '<', $monitoring->monitoring_date)
            ->orderBy('monitoring_date', 'desc')
            ->first();
        
        $scoreChange = null;
        $percentageChange = null;
        
        if ($previousMonitoring) {
            $scoreChange = $monitoring->current_risk_score - $previousMonitoring->current_risk_score;
            $percentageChange = $previousMonitoring->current_risk_score > 0 
                ? ($scoreChange / $previousMonitoring->current_risk_score) * 100 
                : 0;
        }
        
        return view('risk-monitorings.show', compact('risk', 'monitoring', 'scoreChange', 'percentageChange'));
    }

    /**
     * Tampilkan form edit monitoring
     */
    public function edit($riskId, $monitoringId)
    {
        $risk = Risk::findOrFail($riskId);
        $monitoring = RiskMonitoring::where('risk_monitoring_risk_id', $riskId)
            ->findOrFail($monitoringId);
        
        return view('risk-monitorings.edit', compact('risk', 'monitoring'));
    }

    /**
     * Update monitoring
     */
    public function update(Request $request, $riskId, $monitoringId)
    {
        $validated = $request->validate([
            'monitoring_date' => 'required|date',
            'current_risk_score' => 'required|numeric|min:1|max:25',
            'monitoring_result' => 'nullable|string',
            'monitoring_report' => 'nullable|string',
            'effectiveness_rating' => 'nullable|integer|between:1,5',
            'monitored_by' => 'required|string|max:255',
            'next_monitoring_date' => 'nullable|date|after:monitoring_date',
            'recommendations' => 'nullable|string',
        ]);

        // Hitung level risiko
        $riskLevel = $this->calculateRiskLevel($validated['current_risk_score']);
        
        // Update monitoring
        $monitoring = RiskMonitoring::where('risk_monitoring_risk_id', $riskId)
            ->findOrFail($monitoringId);
        
        $monitoring->update([
            'monitoring_date' => $validated['monitoring_date'],
            'current_risk_score' => $validated['current_risk_score'],
            'current_risk_level' => $riskLevel,
            'monitoring_result' => $validated['monitoring_result'],
            'monitoring_report' => $validated['monitoring_report'],
            'effectiveness_rating' => $validated['effectiveness_rating'],
            'monitored_by' => $validated['monitored_by'],
            'next_monitoring_date' => $validated['next_monitoring_date'],
            'recommendations' => $validated['recommendations'],
        ]);

        // Update risiko dengan data monitoring terbaru
        $risk = Risk::findOrFail($riskId);
        $this->updateRiskFromLatestMonitoring($risk);

        return redirect()->route('risk-monitorings.by-risk', $riskId)
            ->with('success', 'Pemantauan risiko berhasil diperbarui.');
    }

    /**
     * Hapus monitoring
     */
    public function destroy($riskId, $monitoringId)
    {
        $monitoring = RiskMonitoring::where('risk_monitoring_risk_id', $riskId)
            ->findOrFail($monitoringId);
        
        $monitoring->delete();

        // Update risiko dengan data monitoring terbaru yang tersisa
        $risk = Risk::findOrFail($riskId);
        $this->updateRiskFromLatestMonitoring($risk);

        return redirect()->route('risk-monitorings.by-risk', $riskId)
            ->with('success', 'Pemantauan risiko berhasil dihapus.');
    }

    /**
     * API - History monitoring
     */
    public function getMonitoringHistory($riskId)
    {
        $monitorings = RiskMonitoring::where('risk_monitoring_risk_id', $riskId)
            ->orderBy('monitoring_date', 'desc')
            ->get(['monitoring_date', 'current_risk_score', 'current_risk_level', 'effectiveness_rating']);
        
        return response()->json([
            'success' => true,
            'data' => $monitorings
        ]);
    }

    /**
     * API - Trend monitoring
     */
    public function getMonitoringTrend($riskId)
    {
        $analyses = RiskAnalysis::where('risk_analysis_risk_id', $riskId)
            ->orderBy('analysis_date')
            ->get(['analysis_date', 'risk_score', 'risk_level'])
            ->map(function($item) {
                return [
                    'date' => $item->analysis_date,
                    'score' => $item->risk_score,
                    'type' => 'analysis',
                    'level' => $item->risk_level
                ];
            });
        
        $monitorings = RiskMonitoring::where('risk_monitoring_risk_id', $riskId)
            ->orderBy('monitoring_date')
            ->get(['monitoring_date', 'current_risk_score', 'current_risk_level'])
            ->map(function($item) {
                return [
                    'date' => $item->monitoring_date,
                    'score' => $item->current_risk_score,
                    'type' => 'monitoring',
                    'level' => $item->current_risk_level
                ];
            });
        
        $trend = $analyses->merge($monitorings)
            ->sortBy('date')
            ->values();
        
        return response()->json([
            'success' => true,
            'data' => $trend
        ]);
    }

    /**
     * API - Monitoring yang perlu dilakukan
     */
    public function getDueForMonitoring()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        // Ambil risiko yang belum dimonitor atau terakhir dimonitor lebih dari 30 hari lalu
        $risks = Risk::whereHas('monitorings', function($query) use ($thirtyDaysAgo) {
            $query->where('monitoring_date', '<', $thirtyDaysAgo)
                  ->orWhereNull('monitoring_date');
        }, '=', 0)
        ->orWhere(function($query) use ($thirtyDaysAgo) {
            $query->whereNull('last_monitoring_date')
                  ->orWhere('last_monitoring_date', '<', $thirtyDaysAgo);
        })
        ->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
        ->with(['organization', 'project'])
        ->orderBy('last_monitoring_date', 'asc')
        ->limit(20)
        ->get();
        
        return response()->json([
            'success' => true,
            'data' => $risks
        ]);
    }

    /**
     * API - Summary monitoring
     */
    public function getMonitoringSummary($riskId)
    {
        $monitorings = RiskMonitoring::where('risk_monitoring_risk_id', $riskId)
            ->orderBy('monitoring_date', 'desc')
            ->get();
        
        if ($monitorings->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data pemantauan'
            ], 404);
        }
        
        $latest = $monitorings->first();
        $first = $monitorings->last();
        
        $summary = [
            'total_monitorings' => $monitorings->count(),
            'latest_score' => $latest->current_risk_score,
            'latest_level' => $latest->current_risk_level,
            'latest_date' => $latest->monitoring_date,
            'first_score' => $first->current_risk_score,
            'first_date' => $first->monitoring_date,
            'score_change' => $latest->current_risk_score - $first->current_risk_score,
            'percentage_change' => $first->current_risk_score > 0 
                ? (($latest->current_risk_score - $first->current_risk_score) / $first->current_risk_score) * 100 
                : 0,
            'average_effectiveness' => $monitorings->avg('effectiveness_rating'),
            'high_risk_count' => $monitorings->whereIn('current_risk_level', ['tinggi', 'sangat_tinggi'])->count(),
            'overdue_count' => $monitorings->whereNotNull('next_monitoring_date')
                ->where('next_monitoring_date', '<', now())
                ->count(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Update risk data based on the latest monitoring
     */
    private function updateRiskFromLatestMonitoring(Risk $risk)
    {
        $latestMonitoring = RiskMonitoring::where('risk_monitoring_risk_id', $risk->risk_id)
            ->latest('monitoring_date')
            ->first();
        
        if ($latestMonitoring) {
            $risk->update([
                'risk_score' => $latestMonitoring->current_risk_score,
                'risk_level' => $latestMonitoring->current_risk_level,
                'last_monitoring_date' => $latestMonitoring->monitoring_date,
            ]);
        } else {
            // Jika tidak ada monitoring, reset nilai
            $risk->update([
                'risk_score' => null,
                'risk_level' => null,
                'last_monitoring_date' => null,
            ]);
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
}