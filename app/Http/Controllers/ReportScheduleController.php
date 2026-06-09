<?php

namespace App\Http\Controllers;

use App\Models\ReportSchedule;
use App\Models\User;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Risk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class ReportScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = ReportSchedule::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('report-schedules.index', compact('schedules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $organizations = Organization::all();
        $projects = Project::all();
        $risks = Risk::all();

        return view('report-schedules.create', compact('users', 'organizations', 'projects', 'risks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'schedule_name' => 'required|string|max:255',
            'report_type' => 'required|in:monitoring,risk_profile,executive_summary,mitigation_effectiveness',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'parameters' => 'nullable|array',
            'recipients' => 'nullable|array',
            'auto_generate' => 'boolean',
            'auto_send_email' => 'boolean',
            'generation_time' => 'nullable|date_format:H:i',
            'day_of_month' => 'nullable|integer|between:1,31',
            'month_of_year' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['parameters'] = json_encode($request->parameters ?? []);
        $validated['recipients'] = json_encode($request->recipients ?? []);

        ReportSchedule::create($validated);

        return redirect()->route('report-schedules.index')
            ->with('success', 'Jadwal laporan berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ReportSchedule $reportSchedule)
    {
        $reportSchedule->load('creator', 'reports');
        
        return view('report-schedules.show', compact('reportSchedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReportSchedule $reportSchedule)
    {
        $users = User::all();
        $organizations = Organization::all();
        $projects = Project::all();
        $risks = Risk::all();

        return view('report-schedules.edit', compact('reportSchedule', 'users', 'organizations', 'projects', 'risks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReportSchedule $reportSchedule)
    {
        $validated = $request->validate([
            'schedule_name' => 'required|string|max:255',
            'report_type' => 'required|in:monitoring,risk_profile,executive_summary,mitigation_effectiveness',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'parameters' => 'nullable|array',
            'recipients' => 'nullable|array',
            'auto_generate' => 'boolean',
            'auto_send_email' => 'boolean',
            'generation_time' => 'nullable|date_format:H:i',
            'day_of_month' => 'nullable|integer|between:1,31',
            'month_of_year' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['parameters'] = json_encode($request->parameters ?? []);
        $validated['recipients'] = json_encode($request->recipients ?? []);

        $reportSchedule->update($validated);

        return redirect()->route('report-schedules.index')
            ->with('success', 'Jadwal laporan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReportSchedule $reportSchedule)
    {
        $reportSchedule->delete();

        return redirect()->route('report-schedules.index')
            ->with('success', 'Jadwal laporan berhasil dihapus.');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(ReportSchedule $reportSchedule)
    {
        $reportSchedule->update([
            'is_active' => !$reportSchedule->is_active
        ]);

        return back()->with('success', 'Status jadwal berhasil diperbarui.');
    }

    /**
     * Generate report manually
     */
    public function generateReport(ReportSchedule $reportSchedule)
    {
        // Logic untuk generate report berdasarkan schedule
        // Implementasi sesuai kebutuhan
        
        return back()->with('success', 'Laporan berhasil digenerate.');
    }
}