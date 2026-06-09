@extends('layouts.master')

@section('title', 'Detail Kategori Risiko - SIMR')

@section('page-title', 'Detail Kategori Risiko')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('risk-categories.index') }}">Kategori Risiko</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
@php
    // ===== HELPER FUNCTIONS =====
    $getCategoryColor = function($categoryName) {
        $colors = [
            'Waktu' => 'bg-red-500',
            'Lingkungan' => 'bg-green-500',
            'Manajemen' => 'bg-blue-500',
            'Hukum' => 'bg-purple-500',
            'SDM' => 'bg-yellow-500',
            'K3' => 'bg-orange-500',
        ];
        return $colors[$categoryName] ?? 'bg-gray-500';
    };

    $getCategoryFeatherIcon = function($categoryName) {
        $icons = [
            'Waktu' => 'clock',
            'Lingkungan' => 'globe',
            'Manajemen' => 'briefcase',
            'Hukum' => 'trello',
            'SDM' => 'users',
            'K3' => 'shield',
        ];
        return $icons[$categoryName] ?? 'folder';
    };
@endphp

<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 xl:col-span-8">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full {{ $getCategoryColor($category->risk_category_name) }} flex items-center justify-center mr-4">
                        <i data-feather="{{ $getCategoryFeatherIcon($category->risk_category_name) }}" class="w-6 h-6 text-black"></i>
                    </div>
                    <div>
                        <h2 class="font-medium text-base mr-auto">
                            {{ $category->risk_category_name }}
                        </h2>
                        <div class="text-gray-500 text-sm">Kode: {{ strtoupper(substr($category->risk_category_name, 0, 3)) }}</div>
                    </div>
                </div>
                <div class="ml-auto flex items-center space-x-2">
                    <a href="{{ route('risk-categories.edit', $category->risk_category_id) }}" class="btn btn-primary">
                        <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit
                    </a>
                    <a href="{{ route('risk-categories.index') }}" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="p-5">
                <!-- Informasi Utama -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-700 mb-2">Informasi Kategori</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nama Kategori:</span>
                                <span class="font-medium">{{ $category->risk_category_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kode Kategori:</span>
                                <span class="font-medium bg-gray-100 px-2 py-1 rounded text-sm">
                                    {{ strtoupper(substr($category->risk_category_name, 0, 3)) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Risiko:</span>
                                <span class="font-medium">{{ $category->risks_count ?? 0 }} risiko</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                    Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Deskripsi -->
                <div class="mb-8">
                    <h4 class="font-medium text-gray-700 mb-3">Deskripsi Kategori</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700 whitespace-pre-line">{{ $category->risk_category_description }}</p>
                    </div>
                </div>
                
                <!-- Statistik Risiko -->
                <div class="mb-8">
                    <h4 class="font-medium text-gray-700 mb-4">Statistik Risiko</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $category->risks_count ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Total Risiko</div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-600">
                                {{ $category->risks()->where('risk_level', 'Tinggi')->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Risiko Tinggi</div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-green-600">
                                {{ $category->risks()->where('risk_level', 'Rendah')->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Risiko Rendah</div>
                        </div>
                    </div>
                </div>
                
                <!-- Daftar Risiko -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-medium text-gray-700">Daftar Risiko</h4>
                        @if($category->risks_count > 0)
                        <a href="{{ route('risks.create', ['category' => $category->risk_category_id]) }}" 
                           class="btn btn-primary btn-sm">
                            <i data-feather="plus" class="w-4 h-4 mr-1"></i> Tambah Risiko
                        </a>
                        @endif
                    </div>
                    
                    @if($category->risks_count > 0)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Risiko</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($category->risks->take(5) as $risk)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <a href="{{ route('risks.show', $risk->risk_id) }}" class="text-blue-600 hover:underline">
                                                {{ $risk->risk_description }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $levelColors = [
                                                    'Tinggi' => 'bg-red-100 text-red-800',
                                                    'Sedang' => 'bg-yellow-100 text-yellow-800',
                                                    'Rendah' => 'bg-green-100 text-green-800',
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded-full {{ $levelColors[$risk->risk_level] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $risk->risk_level }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $statusColors = [
                                                    'Open' => 'bg-red-100 text-red-800',
                                                    'Dalam Proses' => 'bg-yellow-100 text-yellow-800',
                                                    'Tertangani' => 'bg-green-100 text-green-800',
                                                    'Ditutup' => 'bg-gray-100 text-gray-800',
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$risk->risk_status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $risk->risk_status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($risk->created_at)->format('d/m/Y') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                            @if($category->risks_count > 5)
                            <div class="px-4 py-3 bg-gray-50 text-center border-t">
                                <a href="{{ route('risks.index', ['category' => $category->risk_category_id]) }}" 
                                   class="text-blue-600 hover:underline text-sm">
                                    Lihat semua {{ $category->risks_count }} risiko →
                                </a>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200">
                            <i data-feather="alert-triangle" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                            <p class="text-gray-500 mb-4">Belum ada risiko dalam kategori ini</p>
                            <a href="{{ route('risks.create', ['category' => $category->risk_category_id]) }}" 
                               class="btn btn-primary">
                                <i data-feather="plus-circle" class="w-4 h-4 mr-2"></i> Tambah Risiko Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Informasi Penting -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="info" class="w-5 h-5 mr-2"></i> Informasi Kategori
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <h4 class="font-medium text-blue-800 mb-2">Penggunaan Kategori</h4>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>• Kategori ini telah digunakan untuk {{ $category->risks_count ?? 0 }} risiko</li>
                            <li>• Tidak dapat dihapus jika masih memiliki risiko aktif</li>
                            <li>• Dapat diedit untuk memperbarui deskripsi</li>
                            <li>• Digunakan dalam laporan dan analisis risiko</li>
                        </ul>
                    </div>
                    
                    <div class="p-4 bg-green-50 rounded-lg">
                        <h4 class="font-medium text-green-800 mb-2">Rekomendasi</h4>
                        <ul class="text-sm text-green-700 space-y-1">
                            <li>• Tinjau risiko secara berkala</li>
                            <li>• Update kategori jika ada perubahan standar</li>
                            <li>• Gunakan untuk filter dalam pencarian risiko</li>
                            <li>• Integrasikan dengan analisis dampak risiko</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-span-12 xl:col-span-4">
        <!-- Aksi Cepat -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="zap" class="w-5 h-5 mr-2"></i> Aksi Cepat
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    <a href="{{ route('risks.create', ['category' => $category->risk_category_id]) }}" 
                       class="flex items-center p-3 bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                        <div class="w-8 h-8 rounded-full bg-theme-1 flex items-center justify-center mr-3">
                            <i data-feather="plus" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <div class="font-medium">Tambah Risiko Baru</div>
                            <div class="text-xs text-gray-600">Tambah risiko ke kategori ini</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('risk-categories.edit', $category->risk_category_id) }}" 
                       class="flex items-center p-3 bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                        <div class="w-8 h-8 rounded-full bg-theme-1 flex items-center justify-center mr-3">
                            <i data-feather="edit" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <div class="font-medium">Edit Kategori</div>
                            <div class="text-xs text-gray-600">Ubah nama atau deskripsi</div>
                        </div>
                    </a>
                    
                    <form method="POST" action="{{ route('risk-categories.destroy', $category->risk_category_id) }}" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="w-full flex items-center p-3 bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                            <div class="w-8 h-8 rounded-full bg-theme-1 flex items-center justify-center mr-3">
                                <i data-feather="trash-2" class="w-4 h-4 text-white"></i>
                            </div>
                            <div>
                                <div class="font-medium">Hapus Kategori</div>
                                <div class="text-xs text-gray-600">Hanya jika tidak ada risiko</div>
                            </div>
                        </button>
                    </form>
                    
                    <a href="{{ route('risk-categories.index') }}" 
                       class="flex items-center p-3 bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                        <div class="w-8 h-8 rounded-full bg-theme-1 flex items-center justify-center mr-3">
                            <i data-feather="list" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <div class="font-medium">Lihat Semua Kategori</div>
                            <div class="text-xs text-gray-600">Kembali ke daftar kategori</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Informasi Teknis -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="database" class="w-5 h-5 mr-2"></i> Informasi Teknis
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">ID Kategori</div>
                        <div class="font-mono text-sm bg-gray-100 p-2 rounded">{{ $category->risk_category_id }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Dibuat Pada</div>
                        <div class="text-sm">{{ \Carbon\Carbon::parse($category->created_at)->format('d/m/Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Diperbarui Pada</div>
                        <div class="text-sm">{{ \Carbon\Carbon::parse($category->updated_at)->format('d/m/Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Kategori Standar</div>
                        <div class="text-sm flex items-center">
                            <i data-feather="check" class="w-4 h-4 text-green-500 mr-1"></i>
                            Sesuai ISO 31000
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Kategori Lainnya -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Kategori Lainnya
                </h2>
                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded">
                    {{ $otherCategories->count() }} kategori
                </span>
            </div>
            <div class="p-5">
                @if($otherCategories->count() > 0)
                    <div class="space-y-3 max-h-80 overflow-y-auto pr-2">
                        @foreach($otherCategories as $otherCategory)
                            <a href="{{ route('risk-categories.show', $otherCategory->risk_category_id) }}" 
                               class="block p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full {{ $getCategoryColor($otherCategory->risk_category_name) }} flex items-center justify-center mr-3">
                                        <i data-feather="{{ $getCategoryFeatherIcon($otherCategory->risk_category_name) }}" class="w-4 h-4 text-black"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $otherCategory->risk_category_name }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $otherCategory->risks_count ?? 0 }} risiko
                                        </div>
                                    </div>
                                    <i data-feather="chevron-right" class="w-4 h-4 text-gray-400"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-gray-500">Tidak ada kategori lainnya</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/feather-icons"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});
</script>
@endpush