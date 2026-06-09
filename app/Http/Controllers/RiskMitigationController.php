<?php

namespace App\Http\Controllers;

use App\Models\Risk;
use App\Models\RiskMitigation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RiskMitigationController extends Controller
{
    public function index()
    {
        // Redirect ke halaman pilih risiko
        return view('risk-mitigations.select-risk');
    }

    public function all()
    {
        // Ambil semua mitigasi dengan pagination
        $mitigations = RiskMitigation::with('risk')
            ->orderBy('deadline')
            ->paginate(15);
        
        return view('risk-mitigations.all', compact('mitigations'));
    }

    public function byRisk($riskId)
    {
        $risk = Risk::findOrFail($riskId);
        $mitigations = RiskMitigation::where('risk_mitigation_risk_id', $riskId)
            ->orderBy('deadline')
            ->paginate(10);
        
        return view('risk-mitigations.by-risk', compact('risk', 'mitigations'));
    }

    public function create($riskId)
    {
        $risk = Risk::findOrFail($riskId);
        $statusOptions = [
            'belum dimulai' => 'Belum Dimulai',
            'dalam proses' => 'Dalam Proses',
            'selesai' => 'Selesai',
            'ditunda' => 'Ditunda',
            'dibatalkan' => 'Dibatalkan'
        ];
        
        return view('risk-mitigations.create', compact('risk', 'statusOptions'));
    }

    public function store(Request $request, $riskId)
    {
        $validated = $request->validate([
            'mitigation_plan' => 'required|string',
            'responsible_party' => 'required|string|max:255',
            'deadline' => 'required|date',
            'budget' => 'nullable|numeric|min:0',
            'resources' => 'nullable|string',
            'success_criteria' => 'nullable|string',
            'status' => 'required|in:belum dimulai,dalam proses,selesai,ditunda,dibatalkan',
            'notes' => 'nullable|string',
        ]);

        $mitigation = RiskMitigation::create([
            'risk_mitigation_risk_id' => $riskId,
            ...$validated
        ]);

        return redirect()->route('risk-mitigations.index', $riskId)
            ->with('success', 'Rencana mitigasi berhasil ditambahkan.');
    }

    public function show($riskId, $mitigationId)
    {
        $risk = Risk::findOrFail($riskId);
        $mitigation = RiskMitigation::where('risk_mitigation_risk_id', $riskId)
            ->findOrFail($mitigationId);
        
        return view('risk-mitigations.show', compact('risk', 'mitigation'));
    }

    public function edit($riskId, $mitigationId)
    {
        $risk = Risk::findOrFail($riskId);
        $mitigation = RiskMitigation::where('risk_mitigation_risk_id', $riskId)
            ->findOrFail($mitigationId);
        
        $statusOptions = [
            'belum dimulai' => 'Belum Dimulai',
            'dalam proses' => 'Dalam Proses',
            'selesai' => 'Selesai',
            'ditunda' => 'Ditunda',
            'dibatalkan' => 'Dibatalkan'
        ];
        
        return view('risk-mitigations.edit', compact('risk', 'mitigation', 'statusOptions'));
    }

    public function update(Request $request, $riskId, $mitigationId)
    {
        $validated = $request->validate([
            'mitigation_plan' => 'required|string',
            'responsible_party' => 'required|string|max:255',
            'deadline' => 'required|date',
            'budget' => 'nullable|numeric|min:0',
            'resources' => 'nullable|string',
            'success_criteria' => 'nullable|string',
            'status' => 'required|in:belum dimulai,dalam proses,selesai,ditunda,dibatalkan',
            'notes' => 'nullable|string',
            'completion_date' => 'nullable|date|required_if:status,selesai',
            'actual_cost' => 'nullable|numeric|min:0',
        ]);

        $mitigation = RiskMitigation::where('risk_mitigation_risk_id', $riskId)
            ->findOrFail($mitigationId);
        
        $mitigation->update($validated);

        return redirect()->route('risk-mitigations.index', $riskId)
            ->with('success', 'Rencana mitigasi berhasil diperbarui.');
    }

    public function destroy($riskId, $mitigationId)
    {
        $mitigation = RiskMitigation::where('risk_mitigation_risk_id', $riskId)
            ->findOrFail($mitigationId);
        
        $mitigation->delete();

        return redirect()->route('risk-mitigations.index', $riskId)
            ->with('success', 'Rencana mitigasi berhasil dihapus.');
    }

    public function updateStatus(Request $request, $mitigationId)
    {
        $validated = $request->validate([
            'status' => 'required|in:belum dimulai,dalam proses,selesai,ditunda,dibatalkan',
            'completion_date' => 'nullable|date|required_if:status,selesai',
            'progress_notes' => 'nullable|string',
        ]);

        $mitigation = RiskMitigation::findOrFail($mitigationId);
        $mitigation->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Status mitigasi berhasil diperbarui.',
            'mitigation' => $mitigation
        ]);
    }

    public function getOverdueMitigations()
    {
        $today = Carbon::today();
        $mitigations = RiskMitigation::where('deadline', '<', $today)
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->with('risk')
            ->orderBy('deadline')
            ->get();
        
        return response()->json($mitigations);
    }

    public function getMitigationStatistics($riskId = null)
    {
        $query = RiskMitigation::query();
        
        if ($riskId) {
            $query->where('risk_mitigation_risk_id', $riskId);
        }
        
        $total = $query->count();
        $completed = $query->where('status', 'selesai')->count();
        $inProgress = $query->where('status', 'dalam proses')->count();
        $notStarted = $query->where('status', 'belum dimulai')->count();
        $delayed = $query->where('deadline', '<', Carbon::today())
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->count();
        
        return response()->json([
            'total' => $total,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'not_started' => $notStarted,
            'delayed' => $delayed,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0
        ]);
    }

    public function getMitigationProgress($mitigationId)
    {
        $mitigation = RiskMitigation::with('risk')->findOrFail($mitigationId);
        
        $progress = [
            'id' => $mitigation->risk_mitigation_id,
            'plan' => $mitigation->mitigation_plan,
            'status' => $mitigation->status,
            'deadline' => $mitigation->deadline,
            'responsible' => $mitigation->responsible_party,
            'risk_name' => $mitigation->risk->risk_description ?? 'N/A',
            'risk_level' => $mitigation->risk->risk_level ?? 'N/A',
            'days_remaining' => Carbon::parse($mitigation->deadline)->diffInDays(Carbon::today()),
            'is_overdue' => Carbon::parse($mitigation->deadline)->lt(Carbon::today()) && !in_array($mitigation->status, ['selesai', 'dibatalkan'])
        ];
        
        return response()->json($progress);
    }
}