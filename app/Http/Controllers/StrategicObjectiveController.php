<?php

namespace App\Http\Controllers;

use App\Models\StrategicObjective;
use App\Models\Organization;
use Illuminate\Http\Request;

class StrategicObjectiveController extends Controller
{
    public function index(Request $request)
    {
        $query = StrategicObjective::with(['organization'])
            ->withCount('risks');
        
        // Filter pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('strategic_objective_name', 'like', '%'.$search.'%');
        }
        
        // Filter organization
        if ($request->has('organization') && $request->organization != '') {
            $query->where('strategic_objective_organization_id', $request->organization);
        }
        
        // Sorting
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'name_asc':
                    $query->orderBy('strategic_objective_name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('strategic_objective_name', 'desc');
                    break;
                case 'org_asc':
                    $query->join('organizations', 'strategic_objectives.strategic_objective_organization_id', '=', 'organizations.organization_id')
                        ->orderBy('organizations.organization_name', 'asc')
                        ->select('strategic_objectives.*');
                    break;
                case 'org_desc':
                    $query->join('organizations', 'strategic_objectives.strategic_objective_organization_id', '=', 'organizations.organization_id')
                        ->orderBy('organizations.organization_name', 'desc')
                        ->select('strategic_objectives.*');
                    break;
                default:
                    $query->orderBy('strategic_objective_name');
                    break;
            }
        } else {
            $query->orderBy('strategic_objective_name');
        }
        
        $objectives = $query->get();
        
        // Ambil data organizations untuk dropdown filter
        $organizations = Organization::where('organization_type', 'UPTD')
            ->orderBy('organization_code')
            ->get();
        
        return view('strategic-objectives.index', compact('objectives', 'organizations'));
    }

    public function create()
    {
        $organizations = Organization::where('organization_type', 'UPTD')
            ->orderBy('organization_code')
            ->get();
        
        return view('strategic-objectives.create', compact('organizations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'strategic_objective_organization_id' => 'required|exists:organizations,organization_id',
            'strategic_objective_name' => 'required|string|max:255'
        ]);

        StrategicObjective::create($validated);

        return redirect()->route('strategic-objectives.index')
            ->with('success', 'Tujuan strategis berhasil ditambahkan.');
    }

    public function show($id)
    {
        $objective = StrategicObjective::with(['organization', 'risks'])->findOrFail($id);
        return view('strategic-objectives.show', compact('objective'));
    }

    public function edit($id)
    {
        $objective = StrategicObjective::with(['organization', 'risks'])
            ->withCount('risks')
            ->findOrFail($id);
        
        $organizations = Organization::where('organization_type', 'UPTD')
            ->orderBy('organization_code')
            ->get();
        
        return view('strategic-objectives.edit', compact('objective', 'organizations'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'strategic_objective_organization_id' => 'required|exists:organizations,organization_id',
            'strategic_objective_name' => 'required|string|max:255'
        ]);

        $objective = StrategicObjective::findOrFail($id);
        $objective->update($validated);

        return redirect()->route('strategic-objectives.index')
            ->with('success', 'Tujuan strategis berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $objective = StrategicObjective::findOrFail($id);
        
        if ($objective->risks()->count() > 0) {
            return redirect()->route('strategic-objectives.index')
                ->with('error', 'Tidak dapat menghapus tujuan strategis yang digunakan oleh data risiko.');
        }
        
        $objective->delete();

        return redirect()->route('strategic-objectives.index')
            ->with('success', 'Tujuan strategis berhasil dihapus.');
    }
}