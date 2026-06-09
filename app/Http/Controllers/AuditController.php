<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Risk;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::with('risk')
            ->orderBy('audit_date', 'desc');

        // Apply filters
        if ($request->filled('auditor')) {
            $query->where('auditor', 'like', '%' . $request->auditor . '%');
        }

        if ($request->filled('audit_date')) {
            $query->whereDate('audit_date', $request->audit_date);
        }

        if ($request->filled('has_findings')) {
            if ($request->has_findings == 'yes') {
                $query->whereNotNull('audit_findings');
            } elseif ($request->has_findings == 'no') {
                $query->whereNull('audit_findings');
            }
        }

        if ($request->filled('has_recommendations')) {
            if ($request->has_recommendations == 'yes') {
                $query->whereNotNull('audit_recommendations');
            } elseif ($request->has_recommendations == 'no') {
                $query->whereNull('audit_recommendations');
            }
        }

        $audits = $query->paginate(20);

        // Calculate statistics
        $totalAudits = Audit::count();
        $auditsWithFindings = Audit::whereNotNull('audit_findings')->count();
        
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $recentAudits = Audit::whereBetween('audit_date', [$startOfMonth, $endOfMonth])->count();
        
        $auditorsCount = Audit::distinct('auditor')->count('auditor');
        $uniqueAuditorsThisMonth = Audit::whereBetween('audit_date', [$startOfMonth, $endOfMonth])
            ->distinct('auditor')
            ->count('auditor');

        return view('audits.index', compact(
            'audits',
            'totalAudits',
            'auditsWithFindings',
            'recentAudits',
            'auditorsCount',
            'uniqueAuditorsThisMonth'
        ));
    }

    public function create()
    {
        $risks = Risk::orderBy('risk_code')->get();
        return view('audits.create', compact('risks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'risk_id' => 'nullable|exists:risk,risk_id',
            'auditor' => 'required|string|max:255',
            'audit_date' => 'required|date',
            'audit_findings' => 'nullable|string',
            'audit_recommendations' => 'nullable|string',
            'audit_report' => 'nullable|string',
        ]);

        Audit::create($validated);

        return redirect()->route('audits.index')
            ->with('success', 'Audit berhasil ditambahkan.');
    }

    public function show($id)
    {
        $audit = Audit::with('risk')->findOrFail($id);
        return view('audits.show', compact('audit'));
    }

    public function edit($id)
    {
        $audit = Audit::findOrFail($id);
        $risks = Risk::orderBy('risk_code')->get();
        
        return view('audits.edit', compact('audit', 'risks'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'risk_id' => 'nullable|exists:risk,risk_id',
            'auditor' => 'required|string|max:255',
            'audit_date' => 'required|date',
            'audit_findings' => 'nullable|string',
            'audit_recommendations' => 'nullable|string',
            'audit_report' => 'nullable|string',
        ]);

        $audit = Audit::findOrFail($id);
        $audit->update($validated);

        return redirect()->route('audits.index')
            ->with('success', 'Audit berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $audit = Audit::findOrFail($id);
        $audit->delete();

        return redirect()->route('audits.index')
            ->with('success', 'Audit berhasil dihapus.');
    }

    public function getRiskAudits($riskId)
    {
        $audits = Audit::where('risk_id', $riskId)
            ->orderBy('audit_date', 'desc')
            ->get();
        
        return response()->json($audits);
    }

    public function getStatistics()
    {
        $total = Audit::count();
        $withFindings = Audit::whereNotNull('audit_findings')->count();
        $recent = Audit::where('audit_date', '>=', Carbon::now()->subMonth())->count();
        
        return response()->json([
            'total_audits' => $total,
            'audits_with_findings' => $withFindings,
            'recent_audits' => $recent
        ]);
    }
}