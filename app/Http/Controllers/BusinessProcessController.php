<?php

namespace App\Http\Controllers;

use App\Models\BusinessProcess;
use App\Models\Organization;
use Illuminate\Http\Request;

class BusinessProcessController extends Controller
{
    public function index(Request $request)
    {
        $query = BusinessProcess::with(['organization'])
            ->withCount('risks');
        
        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('business_process_name', 'like', "%{$search}%")
                ->orWhere('business_process_description', 'like', "%{$search}%");
            });
        }
        
        // Filter by organization
        if ($request->has('organization') && !empty($request->organization)) {
            $query->where('business_process_organization_id', $request->organization);
        }
        
        // Sorting
        $sort = $request->get('sort', 'name_asc');
        switch ($sort) {
            case 'name_desc':
                $query->orderBy('business_process_name', 'desc');
                break;
            case 'org_asc':
                $query->join('organizations', 'business_processes.business_process_organization_id', '=', 'organizations.organization_id')
                    ->orderBy('organizations.organization_name', 'asc');
                break;
            case 'org_desc':
                $query->join('organizations', 'business_processes.business_process_organization_id', '=', 'organizations.organization_id')
                    ->orderBy('organizations.organization_name', 'desc');
                break;
            case 'risk_asc':
                $query->orderBy('risks_count', 'asc');
                break;
            case 'risk_desc':
                $query->orderBy('risks_count', 'desc');
                break;
            default: // name_asc
                $query->orderBy('business_process_name', 'asc');
        }
        
        $processes = $query->paginate(10);
        $organizations = Organization::orderBy('organization_name')->get();
        
        return view('business-processes.index', compact('processes', 'organizations'));
    }

    public function create()
    {
        $organizations = Organization::orderBy('organization_name')->get();
        return view('business-processes.create', compact('organizations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_process_organization_id' => 'required|exists:organizations,organization_id',
            'business_process_name' => 'required|string|max:255',
            'business_process_description' => 'required|string'
        ]);

        BusinessProcess::create($validated);

        return redirect()->route('business-processes.index')
            ->with('success', 'Proses bisnis berhasil ditambahkan.');
    }

    public function show($id)
    {
        $process = BusinessProcess::with(['organization', 'risks'])->findOrFail($id);
        return view('business-processes.show', compact('process'));
    }

    public function edit($id)
    {
        $process = BusinessProcess::findOrFail($id);
        $organizations = Organization::orderBy('organization_name')->get();
        
        return view('business-processes.edit', compact('process', 'organizations'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'business_process_organization_id' => 'required|exists:organizations,organization_id',
            'business_process_name' => 'required|string|max:255',
            'business_process_description' => 'required|string'
        ]);

        $process = BusinessProcess::findOrFail($id);
        $process->update($validated);

        return redirect()->route('business-processes.index')
            ->with('success', 'Proses bisnis berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $process = BusinessProcess::findOrFail($id);
        
        if ($process->risks()->count() > 0) {
            return redirect()->route('business-processes.index')
                ->with('error', 'Tidak dapat menghapus proses bisnis yang digunakan oleh data risiko.');
        }
        
        $process->delete();

        return redirect()->route('business-processes.index')
            ->with('success', 'Proses bisnis berhasil dihapus.');
    }
}