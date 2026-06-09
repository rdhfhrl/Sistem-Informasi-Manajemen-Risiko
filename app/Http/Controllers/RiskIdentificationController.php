<?php

namespace App\Http\Controllers;

use App\Models\Risk;
use App\Models\RiskIdentification;
use Illuminate\Http\Request;

class RiskIdentificationController extends Controller
{
    public function index(Request $request)
    {
        // Debug: Cek jumlah data di database
        \Log::info('RiskIdentification count: ' . RiskIdentification::count());
        
        $query = RiskIdentification::with(['risk' => function($query) {
            $query->with(['project', 'organization', 'category']);
        }]);
        
        // Filter berdasarkan search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('risk', function($q) use ($search) {
                $q->where('risk_code', 'like', "%{$search}%")
                ->orWhere('risk_description', 'like', "%{$search}%");
            });
        }
        
        // Filter berdasarkan loss_type
        if ($request->filled('loss_type')) {
            $query->where('loss_type', $request->loss_type);
        }
        
        // Filter berdasarkan violation_type
        if ($request->filled('violation_type')) {
            $query->where('violation_type', $request->violation_type);
        }
        
        // Filter berdasarkan failure_type
        if ($request->filled('failure_type')) {
            $query->where('failure_type', $request->failure_type);
        }
        
        // Filter berdasarkan error_type
        if ($request->filled('error_type')) {
            $query->where('error_type', $request->error_type);
        }
        
        $identifications = $query->orderBy('created_at', 'desc')->paginate(20);
        
        \Log::info('Identifications loaded: ' . $identifications->count());
        
        return view('risk-identifications.index', compact('identifications'));
    }

    public function create(Request $request)
    {
        $riskId = $request->input('riskId') ?? $request->route('riskId');
        
        if (!$riskId) {
            return redirect()->route('risks.index')
                ->with('error', 'ID Risiko tidak ditemukan');
        }
        
        $risk = Risk::with(['project', 'organization', 'category'])->findOrFail($riskId);
        $identification = $risk->identification;
        
        \Log::info('Creating identification for risk: ' . $riskId);
        
        return view('risk-identifications.create', compact('risk', 'identification'));
    }

    public function store(Request $request)
    {
        // Ambil risk_id dari input form atau query parameter
        $riskId = $request->input('risk_id') ?? $request->get('riskId');
        
        if (!$riskId) {
            return redirect()->route('risks.index')
                ->with('error', 'ID Risiko diperlukan');
        }
        
        $risk = Risk::findOrFail($riskId);
        
        $validated = $request->validate([
            'loss_type' => 'nullable|in:Reputasi,Operasional,Kepatuhan,Lainnya',
            'violation_type' => 'nullable|in:Hukum,SOP,Kontrak,Lainnya',
            'failure_type' => 'nullable|in:Manusia,Proses,Sistem,Lainnya',
            'error_type' => 'nullable|in:Human Error,Technical Error,Lainnya',
        ]);

        \Log::info('Storing identification for risk: ' . $riskId, $validated);

        // Tambahkan risk_id ke data yang divalidasi
        $validated['risk_identification_risk_id'] = $riskId;
        
        // Update atau create data identification
        $identification = RiskIdentification::updateOrCreate(
            ['risk_identification_risk_id' => $riskId],
            $validated
        );

        return redirect()->route('risks.show', $riskId)
            ->with('success', 'Identifikasi risiko berhasil disimpan.');
    }

    public function edit(Request $request)
    {
        $riskId = $request->input('riskId') ?? $request->route('riskId');
        
        if (!$riskId) {
            return redirect()->route('risks.index')
                ->with('error', 'ID Risiko tidak ditemukan');
        }
        
        $risk = Risk::with(['project', 'organization', 'category'])->findOrFail($riskId);
        $identification = $risk->identification;
        
        if (!$identification) {
            return redirect()->route('risk-identifications.create', ['riskId' => $riskId])
                ->with('info', 'Silakan buat identifikasi risiko terlebih dahulu.');
        }
        
        return view('risk-identifications.create', compact('risk', 'identification'));
    }

    public function update(Request $request)
    {
        return $this->store($request); // Gunakan store() yang sudah diperbaiki
    }

    public function destroy(Request $request)
    {
        $riskId = $request->input('risk_id') ?? $request->get('riskId');
        
        if (!$riskId) {
            return redirect()->route('risks.index')
                ->with('error', 'ID Risiko diperlukan');
        }
        
        $risk = Risk::findOrFail($riskId);
        
        if ($risk->identification) {
            $risk->identification->delete();
            return redirect()->route('risks.show', $riskId)
                ->with('success', 'Identifikasi risiko berhasil dihapus.');
        }
        
        return redirect()->route('risks.show', $riskId)
            ->with('error', 'Identifikasi risiko tidak ditemukan.');
    }

    public function getStatistics()
    {
        $lossTypes = RiskIdentification::select('loss_type')
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull('loss_type')
            ->groupBy('loss_type')
            ->get();
        
        $violationTypes = RiskIdentification::select('violation_type')
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull('violation_type')
            ->groupBy('violation_type')
            ->get();
        
        $failureTypes = RiskIdentification::select('failure_type')
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull('failure_type')
            ->groupBy('failure_type')
            ->get();
        
        $errorTypes = RiskIdentification::select('error_type')
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull('error_type')
            ->groupBy('error_type')
            ->get();

        return response()->json([
            'loss_types' => $lossTypes,
            'violation_types' => $violationTypes,
            'failure_types' => $failureTypes,
            'error_types' => $errorTypes
        ]);
    }
}