<?php

namespace App\Http\Controllers;

use App\Models\Risk;
use App\Models\RiskIndicator;
use Illuminate\Http\Request;

class RiskIndicatorController extends Controller
{
    /**
     * Halaman pilih risiko
     */
    public function index()
    {
        return view('risk-indicators.select-risk');
    }

    /**
     * Semua indikator
     */
    public function all(Request $request)
    {
        $query = RiskIndicator::with(['risk' => function($query) {
            $query->with(['project', 'organization']);
        }, 'measurements']);
        
        // Filter by indicator type
        if ($request->has('indicator_type') && $request->indicator_type) {
            $query->where('indicator_type', $request->indicator_type);
        }
        
        // Filter by risk level
        if ($request->has('risk_level') && $request->risk_level) {
            $query->whereHas('risk', function($q) use ($request) {
                $q->where('risk_level', $request->risk_level);
            });
        }
        
        // Filter by status berdasarkan measurements
        if ($request->has('status') && $request->status) {
            if ($request->status === 'exceeded') {
                $query->whereHas('measurements', function($q) use ($indicator) {
                    $q->latest()
                    ->where('measured_value', '>', $indicator->threshold);
                });
            } elseif ($request->status === 'normal') {
                $query->whereHas('measurements', function($q) use ($indicator) {
                    $q->latest()
                    ->where('measured_value', '<=', $indicator->threshold);
                });
            } elseif ($request->status === 'not_measured') {
                $query->doesntHave('measurements');
            }
        }
        
        $indicators = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Statistik
        $stats = [
            'total' => RiskIndicator::count(),
            'exceeded' => $this->getExceededCount(),
            'akar_masalah' => RiskIndicator::where('indicator_type', 'akar_masalah')->count(),
            'penyebab' => RiskIndicator::where('indicator_type', 'penyebab')->count(),
            'dampak' => RiskIndicator::where('indicator_type', 'dampak')->count(),
            'normal' => $this->getNormalCount(),
            'not_measured' => $this->getNotMeasuredCount(),
        ];
        
        return view('risk-indicators.all', compact('indicators', 'stats'));
    }

    /**
     * Indikator berdasarkan risiko
     */
    public function byRisk($riskId)
    {
        $risk = Risk::findOrFail($riskId);
        $indicators = RiskIndicator::with('measurements')
            ->where('risk_indicator_risk_id', $riskId)
            ->orderBy('indicator_type')
            ->orderBy('indicator_name')
            ->paginate(10);
        
        return view('risk-indicators.index', compact('risk', 'indicators'));
    }

    /**
     * Halaman create
     */
    public function create($riskId)
    {
        $risk = Risk::findOrFail($riskId);
        $indicatorTypes = [
            'akar_masalah' => 'Akar Masalah',
            'penyebab' => 'Penyebab',
            'dampak' => 'Dampak',
            'lainnya' => 'Lainnya'
        ];
        
        return view('risk-indicators.create', compact('risk', 'indicatorTypes'));
    }

    /**
     * Store indikator baru
     */
    public function store(Request $request, $riskId)
    {
        $validated = $request->validate([
            'indicator_type' => 'required|in:akar_masalah,penyebab,dampak,lainnya',
            'indicator_name' => 'required|string|max:255',
            'indicator_description' => 'nullable|string',
            'threshold' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
        ]);

        RiskIndicator::create([
            'risk_indicator_risk_id' => $riskId,
            ...$validated
        ]);

        return redirect()->route('risk-indicators.by-risk', $riskId)
            ->with('success', 'Indikator risiko berhasil ditambahkan.');
    }

    /**
     * Show detail indikator
     */
    public function show($riskId, $indicatorId)
    {
        $risk = Risk::findOrFail($riskId);
        $indicator = RiskIndicator::with('measurements.measuredBy')
            ->where('risk_indicator_risk_id', $riskId)
            ->findOrFail($indicatorId);
        
        return view('risk-indicators.show', compact('risk', 'indicator'));
    }

    /**
     * Edit indikator
     */
    public function edit($riskId, $indicatorId)
    {
        $risk = Risk::findOrFail($riskId);
        $indicator = RiskIndicator::where('risk_indicator_risk_id', $riskId)
            ->findOrFail($indicatorId);
        
        $indicatorTypes = [
            'akar_masalah' => 'Akar Masalah',
            'penyebab' => 'Penyebab',
            'dampak' => 'Dampak',
            'lainnya' => 'Lainnya'
        ];
        
        return view('risk-indicators.edit', compact('risk', 'indicator', 'indicatorTypes'));
    }

    /**
     * Update indikator
     */
    public function update(Request $request, $riskId, $indicatorId)
    {
        $validated = $request->validate([
            'indicator_type' => 'required|in:akar_masalah,penyebab,dampak,lainnya',
            'indicator_name' => 'required|string|max:255',
            'indicator_description' => 'nullable|string',
            'threshold' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
        ]);

        $indicator = RiskIndicator::where('risk_indicator_risk_id', $riskId)
            ->findOrFail($indicatorId);
        
        $indicator->update($validated);

        return redirect()->route('risk-indicators.by-risk', $riskId)
            ->with('success', 'Indikator risiko berhasil diperbarui.');
    }

    /**
     * Delete indikator
     */
    public function destroy($riskId, $indicatorId)
    {
        $indicator = RiskIndicator::where('risk_indicator_risk_id', $riskId)
            ->findOrFail($indicatorId);
        
        $indicator->delete();

        return redirect()->route('risk-indicators.by-risk', $riskId)
            ->with('success', 'Indikator risiko berhasil dihapus.');
    }

    /**
     * API: Update measurement value
     */
    public function updateValue(Request $request, $indicatorId)
    {
        $validated = $request->validate([
            'current_value' => 'required|numeric',
            'measurement_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $indicator = RiskIndicator::findOrFail($indicatorId);
        
        // Create measurement record
        $measurement = $indicator->updateMeasurement(
            $validated['current_value'],
            $validated['measurement_date'],
            $validated['notes'],
            auth()->id()
        );

        return response()->json([
            'success' => true,
            'message' => 'Pengukuran indikator berhasil disimpan.',
            'indicator' => $indicator->fresh(['measurements']),
            'measurement' => $measurement
        ]);
    }

    /**
     * API: Get measurement history
     */
    public function getIndicatorMeasurements($indicatorId)
    {
        $indicator = RiskIndicator::findOrFail($indicatorId);
        $measurements = $indicator->getMeasurementHistory(20);
        
        return response()->json($measurements);
    }

    /**
     * API: Get indicators that exceeded thresholds
     */
    public function getExceededThresholds()
    {
        // Ambil semua indikator dengan measurements terakhir
        $indicators = RiskIndicator::with(['risk' => function($query) {
            $query->with(['project', 'organization']);
        }, 'measurements' => function($query) {
            $query->latest()->limit(1);
        }])
        ->has('measurements')
        ->get()
        ->filter(function($indicator) {
            $latestMeasurement = $indicator->measurements->first();
            return $latestMeasurement && $latestMeasurement->measured_value > $indicator->threshold;
        })
        ->values();
        
        return response()->json([
            'success' => true,
            'data' => $indicators,
            'count' => $indicators->count()
        ]);
    }

    /**
     * Helper methods untuk statistics
     */
    private function getExceededCount()
    {
        return RiskIndicator::has('measurements')
            ->get()
            ->filter(function($indicator) {
                return $indicator->isExceeded();
            })
            ->count();
    }

    private function getNormalCount()
    {
        return RiskIndicator::has('measurements')
            ->get()
            ->filter(function($indicator) {
                return !$indicator->isExceeded() && $indicator->current_value !== null;
            })
            ->count();
    }

    private function getNotMeasuredCount()
    {
        return RiskIndicator::doesntHave('measurements')->count();
    }
}