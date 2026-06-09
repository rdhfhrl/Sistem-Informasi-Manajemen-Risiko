<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        // Query dengan filter
        $query = Project::withCount('risks');
        
        // Filter pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('pro_nama', 'like', '%'.$search.'%')
                ->orWhere('pro_lokasi', 'like', '%'.$search.'%');
            });
        }
        
        // Filter status
        if ($request->has('status') && $request->status != '') {
            $query->where('pro_status', $request->status);
        }
        
        // Filter tahun
        if ($request->has('year') && $request->year != '') {
            $query->whereYear('pro_tanggal_mulai', $request->year);
        }
        
        $projects = $query->orderBy('pro_status')
            ->orderBy('pro_tanggal_mulai', 'desc')
            ->paginate(20);
        
        // Hitung statistik untuk semua proyek (tidak difilter)
        $totalProjects = Project::count();
        $activeProjects = Project::where('pro_status', 'Aktif')->count();
        $completedProjects = Project::where('pro_status', 'Selesai')->count();
        
        return view('projects.index', compact(
            'projects', 
            'totalProjects', 
            'activeProjects',
            'completedProjects'
        ));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pro_nama' => 'required|string|max:255',
            'pro_lokasi' => 'required|string',
            'pro_deskripsi' => 'nullable|string',
            'pro_tanggal_mulai' => 'required|date',
            'pro_tanggal_selesai' => 'required|date|after_or_equal:pro_tanggal_mulai',
            'pro_status' => 'required|in:Aktif,Selesai,Ditunda,Dibatalkan'
        ]);

        Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil ditambahkan.');
    }

    public function show($id)
    {
        $project = Project::with([
            'risks' => function($query) {
                $query->orderBy('risk_score', 'desc');
            },
            'risks.category',
            'risks.organization'
        ])->findOrFail($id);
        
        $riskStats = [
            'total' => $project->risks->count(),
            'sangat_tinggi' => $project->risks->where('risk_level', 'sangat_tinggi')->count(),
            'tinggi' => $project->risks->where('risk_level', 'tinggi')->count(),
            'sedang' => $project->risks->where('risk_level', 'sedang')->count(),
            'rendah' => $project->risks->where('risk_level', 'rendah')->count(),
            'sangat_rendah' => $project->risks->where('risk_level', 'sangat_rendah')->count(),
        ];
        
        return view('projects.show', compact('project', 'riskStats'));
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'pro_nama' => 'required|string|max:255',
            'pro_lokasi' => 'required|string',
            'pro_deskripsi' => 'nullable|string',
            'pro_tanggal_mulai' => 'required|date',
            'pro_tanggal_selesai' => 'required|date|after_or_equal:pro_tanggal_mulai',
            'pro_status' => 'required|in:Aktif,Selesai,Ditunda,Dibatalkan'
        ]);

        $project = Project::findOrFail($id);
        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        
        if ($project->risks()->count() > 0) {
            return redirect()->route('projects.index')
                ->with('error', 'Tidak dapat menghapus proyek yang memiliki data risiko.');
        }
        
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil dihapus.');
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'pro_status' => 'required|in:Aktif,Selesai,Ditunda,Dibatalkan'
        ]);

        $project = Project::findOrFail($id);
        $project->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Status proyek berhasil diperbarui.'
        ]);
    }

    public function getStatistics()
    {
        $total = Project::count();
        $active = Project::where('pro_status', 'Aktif')->count();
        $completed = Project::where('pro_status', 'Selesai')->count();
        $delayed = Project::where('pro_tanggal_selesai', '<', now())
            ->where('pro_status', 'Aktif')
            ->count();
        
        return response()->json([
            'total' => $total,
            'active' => $active,
            'completed' => $completed,
            'delayed' => $delayed
        ]);
    }

    public function exportPDF(Request $request)
    {
        // Query projects dengan filter
        $query = Project::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('pro_nama', 'like', "%{$search}%")
                ->orWhere('pro_lokasi', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('pro_status', $request->status);
        }
        
        if ($request->filled('year')) {
            $query->whereYear('pro_tanggal_mulai', $request->year);
        }
        
        $projects = $query->withCount('risks')->orderBy('pro_tanggal_mulai', 'desc')->get();
        
        // Hitung statistik
        $totalProjects = $projects->count();
        $activeProjects = $projects->where('pro_status', 'Aktif')->count();
        $delayedProjects = $projects->filter(function($project) {
            return $project->pro_status == 'Aktif' && 
                \Carbon\Carbon::parse($project->pro_tanggal_selesai)->lt(now());
        })->count();
        
        // Load view PDF
        $pdf = Pdf::loadView('projects.export.pdf', [
            'projects' => $projects,
            'totalProjects' => $totalProjects,
            'activeProjects' => $activeProjects,
            'delayedProjects' => $delayedProjects,
            'filters' => $request->only(['search', 'status', 'year'])
        ]);
        
        // Set nama file
        $filename = 'daftar-proyek-' . date('Y-m-d-H-i-s') . '.pdf';
        
        // Return download
        return $pdf->download($filename);
    }

        public function report(Project $project)
    {
        // Logika untuk generate report
    }

    public function export(Project $project)
    {
        // Logika untuk export data
    }
}