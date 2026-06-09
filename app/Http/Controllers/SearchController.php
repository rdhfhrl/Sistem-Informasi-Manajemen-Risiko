<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Risk;
use App\Models\Project;
use App\Models\Report;
use App\Models\User;
use App\Models\Organization;
use App\Models\ReportSchedule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Display search page results
     */
    public function index(Request $request)
    {
        $query = $request->input('q', '');
        $results = [
            'risks' => collect(),
            'projects' => collect(),
            'reports' => collect(),
            'users' => collect(),
            'schedules' => collect(),
        ];
        
        $totalResults = 0;
        
        if (strlen($query) >= 2) {
            try {
                // Search Risks
                $risks = Risk::where(function($q) use ($query) {
                        $q->where('risk_name', 'like', "%{$query}%")
                          ->orWhere('risk_description', 'like', "%{$query}%")
                          ->orWhere('risk_code', 'like', "%{$query}%");
                    })
                    ->with('project', 'organization')
                    ->paginate(10, ['*'], 'risks_page')
                    ->appends(['q' => $query]);
                
                $results['risks'] = $risks;
                $totalResults += $risks->total();

                // Search Projects
                $projects = Project::where('pro_nama', 'like', "%{$query}%")
                    ->orWhere('pro_deskripsi', 'like', "%{$query}%")
                    ->paginate(10, ['*'], 'projects_page')
                    ->appends(['q' => $query]);
                
                $results['projects'] = $projects;
                $totalResults += $projects->total();

                // Search Reports
                $reports = Report::where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->paginate(10, ['*'], 'reports_page')
                    ->appends(['q' => $query]);
                
                $results['reports'] = $reports;
                $totalResults += $reports->total();

                // Search Users
                $users = User::where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->paginate(10, ['*'], 'users_page')
                    ->appends(['q' => $query]);
                
                $results['users'] = $users;
                $totalResults += $users->total();

                // Search Report Schedules
                $schedules = ReportSchedule::where('schedule_name', 'like', "%{$query}%")
                    ->with('creator')
                    ->paginate(10, ['*'], 'schedules_page')
                    ->appends(['q' => $query]);
                
                $results['schedules'] = $schedules;
                $totalResults += $schedules->total();

            } catch (\Exception $e) {
                // Log error but don't break the page
                \Log::error('Search error: ' . $e->getMessage());
            }
        }

        return view('search.index', [
            'query' => $query,
            'results' => $results,
            'totalResults' => $totalResults
        ]);
    }
    
    /**
     * API Search for real-time suggestions
     */
    public function apiSearch(Request $request)
    {
        $query = $request->input('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [];
        
        try {
            // Search Risks
            $risks = Risk::where(function($q) use ($query) {
                    $q->where('risk_name', 'like', "%{$query}%")
                      ->orWhere('risk_description', 'like', "%{$query}%")
                      ->orWhere('risk_code', 'like', "%{$query}%");
                })
                ->take(5)
                ->get();
            
            foreach ($risks as $risk) {
                $results[] = [
                    'id' => $risk->risk_id,
                    'title' => $risk->risk_name,
                    'subtitle' => $risk->risk_code,
                    'type' => 'Risiko',
                    'icon' => 'alert-triangle',
                    'icon_bg' => 'bg-orange-100',
                    'icon_color' => 'text-orange-600',
                    'url' => route('risks.show', $risk->risk_id),
                    'score' => $risk->risk_score,
                    'level' => $risk->risk_level_label,
                    'level_color' => $risk->risk_level_color,
                ];
            }

            // Search Projects
            $projects = Project::where('pro_nama', 'like', "%{$query}%")
                ->orWhere('pro_deskripsi', 'like', "%{$query}%")
                ->take(5)
                ->get();
            
            foreach ($projects as $project) {
                $results[] = [
                    'id' => $project->pro_id,
                    'title' => $project->pro_nama,
                    'subtitle' => Str::limit($project->pro_deskripsi, 50),
                    'type' => 'Proyek',
                    'icon' => 'briefcase',
                    'icon_bg' => 'bg-blue-100',
                    'icon_color' => 'text-blue-600',
                    'url' => route('projects.show', $project->pro_id),
                    'status' => $project->pro_status ?? 'Aktif',
                ];
            }

            // Search Reports
            $reports = Report::where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->take(5)
                ->get();
            
            foreach ($reports as $report) {
                $results[] = [
                    'id' => $report->report_id,
                    'title' => $report->title,
                    'subtitle' => Str::limit($report->description, 50),
                    'type' => 'Laporan',
                    'icon' => 'file-text',
                    'icon_bg' => 'bg-green-100',
                    'icon_color' => 'text-green-600',
                    'url' => route('reports.show', $report->report_id),
                    'status' => $report->status_label,
                    'date' => $report->report_date?->format('d M Y'),
                ];
            }

            // Search Users
            $users = User::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->take(5)
                ->get();
            
            foreach ($users as $user) {
                $results[] = [
                    'id' => $user->id,
                    'title' => $user->name,
                    'subtitle' => $user->email,
                    'type' => 'Pengguna',
                    'icon' => 'user',
                    'icon_bg' => 'bg-purple-100',
                    'icon_color' => 'text-purple-600',
                    'url' => route('users.show', $user->id),
                    'role' => $user->role_name,
                ];
            }

            // Search Report Schedules
            $schedules = ReportSchedule::where('schedule_name', 'like', "%{$query}%")
                ->take(5)
                ->get();
            
            foreach ($schedules as $schedule) {
                $results[] = [
                    'id' => $schedule->schedule_id,
                    'title' => $schedule->schedule_name,
                    'subtitle' => $schedule->report_type_label,
                    'type' => 'Jadwal Laporan',
                    'icon' => 'calendar',
                    'icon_bg' => 'bg-teal-100',
                    'icon_color' => 'text-teal-600',
                    'url' => route('report-schedules.show', $schedule->schedule_id),
                    'frequency' => $schedule->frequency_label,
                    'status' => $schedule->status_label,
                ];
            }

        } catch (\Exception $e) {
            \Log::error('API Search error: ' . $e->getMessage());
            return response()->json([
                [
                    'title' => 'Error',
                    'subtitle' => 'Terjadi kesalahan dalam pencarian',
                    'type' => 'Error',
                    'icon' => 'alert-circle',
                    'icon_bg' => 'bg-red-100',
                    'icon_color' => 'text-red-600',
                    'url' => '#',
                ]
            ]);
        }

        // Limit results and sort by relevance
        $results = array_slice($results, 0, 15);
        
        return response()->json($results);
    }
    
    /**
     * Get advanced search form
     */
    public function advancedSearch()
    {
        $organizations = Organization::all();
        $projects = Project::all();
        
        return view('search.advanced', compact('organizations', 'projects'));
    }
    
    /**
     * Perform advanced search
     */
    public function performAdvancedSearch(Request $request)
    {
        $query = Risk::query()->with('project', 'organization');
        
        // Search by keyword
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('risk_name', 'like', "%{$keyword}%")
                  ->orWhere('risk_description', 'like', "%{$keyword}%")
                  ->orWhere('risk_code', 'like', "%{$keyword}%");
            });
        }
        
        // Filter by organization
        if ($request->filled('organization_id')) {
            $query->where('risk_organization_id', $request->organization_id);
        }
        
        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('risk_pro_id', $request->project_id);
        }
        
        // Filter by risk level
        if ($request->filled('risk_level')) {
            $query->where('risk_level', $request->risk_level);
        }
        
        // Filter by status
        if ($request->filled('risk_status')) {
            $query->where('risk_status', $request->risk_status);
        }
        
        // Date range filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Sort results
        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_dir', 'desc');
        $query->orderBy($sortField, $sortDirection);
        
        $results = $query->paginate(20);
        
        return view('search.results', [
            'results' => $results,
            'filters' => $request->all()
        ]);
    }
    
    /**
     * Get search statistics
     */
    public function getSearchStats()
    {
        try {
            $stats = [
                'total_risks' => Risk::count(),
                'total_projects' => Project::count(),
                'total_reports' => Report::count(),
                'total_users' => User::count(),
                'total_schedules' => ReportSchedule::count(),
                'recent_searches' => [], // You can implement search history if needed
            ];
            
            return response()->json($stats);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get search statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}