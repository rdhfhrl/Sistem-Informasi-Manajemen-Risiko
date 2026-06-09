<?php

namespace App\Http\Controllers;

use App\Models\RiskCategory;
use Illuminate\Http\Request;

class RiskCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = RiskCategory::withCount('risks')
            ->orderBy('risk_category_name');
        
        // Filter sort
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'risks_desc':
                    $query->orderBy('risks_count', 'desc');
                    break;
                case 'risks_asc':
                    $query->orderBy('risks_count', 'asc');
                    break;
            }
        }
        
        $categories = $query->get();
        
        // Hitung statistik
        $totalCategories = RiskCategory::count();
        $totalRisks = RiskCategory::withCount('risks')->get()->sum('risks_count');
        
        return view('risk-categories.index', compact(
            'categories',
            'totalCategories',
            'totalRisks'
        ));
    }

    public function create()
    {
        $categoryOptions = ['Waktu', 'Lingkungan', 'Manajemen', 'Hukum', 'SDM', 'K3'];
        
        // Ambil kategori yang sudah ada untuk ditampilkan di sidebar
        $existingCategories = RiskCategory::withCount('risks')
            ->orderBy('risk_category_name')
            ->get();
        
        // Filter untuk mendapatkan kategori yang belum digunakan
        $availableCategories = array_diff($categoryOptions, $existingCategories->pluck('risk_category_name')->toArray());
        
        return view('risk-categories.create', compact(
            'categoryOptions',
            'existingCategories',
            'availableCategories'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'risk_category_name' => 'required|in:Waktu,Lingkungan,Manajemen,Hukum,SDM,K3',
            'risk_category_description' => 'required|string|max:500'
        ]);

        RiskCategory::create($validated);

        return redirect()->route('risk-categories.index')
            ->with('success', 'Kategori risiko berhasil ditambahkan.');
    }

    public function show($id)
    {
        $category = RiskCategory::withCount('risks')
        ->with(['risks' => function($query) {
            $query->orderBy('risk_score', 'desc');
        }])
        ->findOrFail($id);
    
        // Ambil kategori lainnya (kecuali kategori saat ini)
        $otherCategories = RiskCategory::withCount('risks')
            ->where('risk_category_id', '!=', $id)
            ->orderBy('risk_category_name')
            ->get();
        
        return view('risk-categories.show', compact('category', 'otherCategories'));
    }

    public function edit($id)
    {
        $category = RiskCategory::findOrFail($id);
        $categoryOptions = ['Waktu', 'Lingkungan', 'Manajemen', 'Hukum', 'SDM', 'K3'];
        
        return view('risk-categories.edit', compact('category', 'categoryOptions'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'risk_category_name' => 'required|in:Waktu,Lingkungan,Manajemen,Hukum,SDM,K3',
            'risk_category_description' => 'required|string|max:500'
        ]);

        $category = RiskCategory::findOrFail($id);
        $category->update($validated);

        return redirect()->route('risk-categories.index')
            ->with('success', 'Kategori risiko berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $category = RiskCategory::findOrFail($id);
        
        if ($category->risks()->count() > 0) {
            return redirect()->route('risk-categories.index')
                ->with('error', 'Tidak dapat menghapus kategori yang digunakan oleh data risiko.');
        }
        
        $category->delete();

        return redirect()->route('risk-categories.index')
            ->with('success', 'Kategori risiko berhasil dihapus.');
    }

    public function getStatistics()
    {
        $categories = RiskCategory::withCount(['risks'])
            ->with(['risks' => function($query) {
                $query->select('risk_category_id')
                    ->selectRaw('COUNT(CASE WHEN risk_level = "sangat_tinggi" THEN 1 END) as sangat_tinggi_count')
                    ->selectRaw('COUNT(CASE WHEN risk_level = "tinggi" THEN 1 END) as tinggi_count')
                    ->selectRaw('COUNT(CASE WHEN risk_level = "sedang" THEN 1 END) as sedang_count')
                    ->groupBy('risk_category_id');
            }])
            ->get();
        
        return response()->json($categories);
    }
}