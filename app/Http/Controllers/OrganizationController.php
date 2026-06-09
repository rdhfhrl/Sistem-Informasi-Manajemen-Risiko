<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\StrategicObjective;
use App\Models\BusinessProcess;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index()
    {
        // Ambil Dinas PUPR (parent)
        $dinasPUPR = Organization::where('organization_type', 'Dinas')->first();
        
        // Ambil semua UPTD (anak dari Dinas) dengan menghitung risks
        $uptdList = Organization::where('organization_type', 'UPTD')
            ->withCount('risks') // TAMBAHKAN INI
            ->orderBy('location')
            ->orderBy('organization_code')
            ->get();
        
        return view('organizations.index', compact('dinasPUPR', 'uptdList'));
    }

    public function create()
    {
        // Cek apakah Dinas PUPR sudah ada
        $dinasPUPR = Organization::where('organization_type', 'Dinas')->first();
        
        // Jika belum ada Dinas PUPR, buat otomatis
        if (!$dinasPUPR) {
            $dinasPUPR = Organization::create([
                'organization_name' => 'Dinas Pekerjaan Umum dan Penataan Ruang Provinsi Sumatera Utara',
                'organization_type' => 'Dinas',
                'organization_code' => 'DPUPR-SU',
                'organization_description' => 'Dinas PUPR Provinsi Sumatera Utara sebagai induk organisasi',
                'parent_id' => null,
                'is_active' => true,
            ]);
        }
        
        // Ambil UPTD yang sudah ada
        $existingUptd = Organization::where('organization_type', 'UPTD')
            ->withCount('risks')
            ->orderBy('location')
            ->take(10)
            ->get();
        
        return view('organizations.create', compact('dinasPUPR', 'existingUptd'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location' => 'required|string|max:100',
            'organization_code' => 'required|string|max:50|unique:organizations,organization_code',
            'organization_description' => 'nullable|string',
        ]);
        
        // Dapatkan Dinas PUPR
        $dinasPUPR = Organization::where('organization_type', 'Dinas')->first();
        
        if (!$dinasPUPR) {
            return back()->with('error', 'Dinas PUPR tidak ditemukan. Silakan refresh halaman.');
        }
        
        // Buat UPTD baru
        Organization::create([
            'organization_name' => 'UPTD PUPR Medan', // Nama tetap
            'organization_type' => 'UPTD',
            'organization_code' => $validated['organization_code'],
            'location' => $validated['location'],
            'organization_description' => $validated['organization_description'],
            'parent_id' => $dinasPUPR->organization_id,
            'is_active' => true,
        ]);

        return redirect()->route('organizations.index')
            ->with('success', 'UPTD berhasil ditambahkan.');
    }

    public function show($id)
    {
        $organization = Organization::with([
            'parent',
            'strategicObjectives',
            'businessProcesses',
            'risks' => function($query) {
                $query->orderBy('risk_score', 'desc')->take(10);
            }
        ])->findOrFail($id);
        
        // Statistik
        $stats = [
            'total_risks' => $organization->risks->count(),
            'high_risks' => $organization->risks->where('risk_level', 'Tinggi')->count(),
            'objectives' => $organization->strategicObjectives->count(),
            'processes' => $organization->businessProcesses->count()
        ];
        
        return view('organizations.show', compact('organization', 'stats'));
    }

    public function edit($id)
    {
        $organization = Organization::withCount(['risks', 'strategicObjectives', 'businessProcesses', 'audits'])
            ->findOrFail($id);
        
        // Hanya UPTD yang bisa diedit (Dinas tidak boleh diedit)
        if ($organization->organization_type === 'Dinas') {
            return redirect()->route('organizations.index')
                ->with('error', 'Data Dinas tidak dapat diubah.');
        }
        
        return view('organizations.edit', compact('organization'));
    }

    public function update(Request $request, $id)
    {
        $organization = Organization::findOrFail($id);
        
        // Hanya UPTD yang bisa diedit
        if ($organization->organization_type === 'Dinas') {
            return back()->with('error', 'Data Dinas tidak dapat diubah.');
        }
        
        $validated = $request->validate([
            'location' => 'required|string|max:100',
            'organization_code' => 'required|string|max:50|unique:organizations,organization_code,' . $id . ',organization_id',
            'organization_description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $organization->update($validated);

        return redirect()->route('organizations.show', $id)
            ->with('success', 'UPTD berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $organization = Organization::findOrFail($id);
        
        // Tidak boleh hapus Dinas
        if ($organization->organization_type === 'Dinas') {
            return redirect()->route('organizations.index')
                ->with('error', 'Dinas PUPR tidak dapat dihapus.');
        }
        
        // Cek apakah UPTD memiliki data terkait
        if ($organization->children()->count() > 0) {
            return redirect()->route('organizations.index')
                ->with('error', 'Tidak dapat menghapus UPTD yang memiliki anak organisasi.');
        }
        
        if ($organization->strategicObjectives()->count() > 0) {
            return redirect()->route('organizations.index')
                ->with('error', 'Tidak dapat menghapus UPTD yang memiliki tujuan strategis.');
        }
        
        if ($organization->businessProcesses()->count() > 0) {
            return redirect()->route('organizations.index')
                ->with('error', 'Tidak dapat menghapus UPTD yang memiliki proses bisnis.');
        }
        
        if ($organization->risks()->count() > 0) {
            return redirect()->route('organizations.index')
                ->with('error', 'Tidak dapat menghapus UPTD yang memiliki data risiko.');
        }
        
        $organization->delete();

        return redirect()->route('organizations.index')
            ->with('success', 'UPTD berhasil dihapus.');
    }

    public function getStrategicObjectives($organizationId)
    {
        $objectives = StrategicObjective::where('strategic_objective_organization_id', $organizationId)
            ->orderBy('strategic_objective_name')
            ->get();
        
        return response()->json($objectives);
    }

    public function getBusinessProcesses($organizationId)
    {
        $processes = BusinessProcess::where('business_process_organization_id', $organizationId)
            ->orderBy('business_process_name')
            ->get();
        
        return response()->json($processes);
    }
    
    // Method tambahan untuk API dropdown
    public function getUptdOptions()
    {
        $uptdList = Organization::where('organization_type', 'UPTD')
            ->where('is_active', true)
            ->orderBy('location')
            ->get(['organization_id', 'organization_name', 'location', 'organization_code']);
        
        return response()->json($uptdList);
    }
}