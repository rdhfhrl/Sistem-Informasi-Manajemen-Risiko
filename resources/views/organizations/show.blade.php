@extends('layouts.master')

@section('title', $organization->organization_name . ' - SIMR')

@section('page-title', 'Detail Organisasi: ' . $organization->organization_name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('organizations.index') }}">Organisasi</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($organization->organization_name, 20) }}</li>
@endsection

@section('page-action')
<div class="flex">
    <a href="{{ route('organizations.edit', $organization->organization_id) }}" 
       class="btn btn-primary shadow-md mr-2">
        <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit
    </a>
    <div class="dropdown">
        <button class="dropdown-toggle btn btn-outline-secondary shadow-md" aria-expanded="false">
            <i data-feather="more-vertical" class="w-4 h-4 mr-2"></i> Lainnya
        </button>
        <div class="dropdown-menu w-40">
            <div class="dropdown-content">
                <a href="{{ route('organizations.risks', $organization->organization_id) }}" 
                   class="dropdown-item">
                    <i data-feather="alert-triangle" class="w-4 h-4 mr-2"></i> Lihat Risiko
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('organizations.destroy', $organization->organization_id) }}" 
                      class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item text-danger" 
                            onclick="return confirm('Hapus organisasi ini? Data risiko terkait akan terpengaruh.')">
                        <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 xl:col-span-8">
        <!-- Informasi Utama -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Informasi Organisasi
                </h2>
                <span class="px-3 py-1 rounded-full text-sm font-medium 
                    @if($organization->parent_id) bg-blue-100 text-blue-800 
                    @else bg-green-100 text-green-800 @endif">
                    @if($organization->parent_id)
                        Sub-Organisasi
                    @else
                        Organisasi Induk
                    @endif
                </span>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <label class="form-label text-gray-500">Nama Organisasi</label>
                            <div class="mt-1 text-lg font-bold text-gray-800">
                                {{ $organization->organization_name }}
                            </div>
                        </div>
                        
                        <div>
                            <label class="form-label text-gray-500">Deskripsi</label>
                            <div class="mt-1 text-gray-700 whitespace-pre-line">
                                {{ $organization->organization_description ?: 'Tidak ada deskripsi' }}
                            </div>
                        </div>
                        
                        <div>
                            <label class="form-label text-gray-500">Organisasi Induk</label>
                            <div class="mt-1">
                                @if($organization->parent)
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                            <i data-feather="folder" class="w-4 h-4 text-blue-600"></i>
                                        </div>
                                        <a href="{{ route('organizations.show', $organization->parent->organization_id) }}" 
                                           class="text-blue-600 hover:underline font-medium">
                                            {{ $organization->parent->organization_name }}
                                        </a>
                                    </div>
                                @else
                                    <span class="text-gray-500">- (Organisasi tingkat atas)</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="form-label text-gray-500">Informasi Sistem</label>
                            <div class="mt-1 space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">ID Organisasi:</span>
                                    <span class="font-medium">{{ $organization->organization_id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Dibuat pada:</span>
                                    <span class="font-medium">{{ $organization->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Terakhir diupdate:</span>
                                    <span class="font-medium">{{ $organization->updated_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="form-label text-gray-500">Statistik</label>
                            <div class="mt-1 grid grid-cols-2 gap-4">
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <div class="text-2xl font-bold text-theme-1">{{ $organization->children_count ?? 0 }}</div>
                                    <div class="text-sm text-gray-600">Sub-Organisasi</div>
                                </div>
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <div class="text-2xl font-bold text-theme-1">{{ $organization->risks_count ?? 0 }}</div>
                                    <div class="text-sm text-gray-600">Data Risiko</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sub-Organisasi -->
        @if($organization->children->count() > 0)
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Sub-Organisasi
                    <span class="text-gray-500 text-sm ml-2">({{ $organization->children_count }} unit)</span>
                </h2>
                <a href="{{ route('organizations.create', ['parent_id' => $organization->organization_id]) }}" 
                   class="btn btn-primary btn-sm">
                    <i data-feather="plus" class="w-4 h-4 mr-2"></i> Tambah Sub
                </a>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($organization->children as $child)
                    <a href="{{ route('organizations.show', $child->organization_id) }}" 
                       class="block p-4 border border-gray-200 rounded-lg hover:border-theme-1 hover:shadow-md transition-all">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i data-feather="folder" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-800 mb-1">{{ $child->organization_name }}</h3>
                                <p class="text-sm text-gray-600 mb-2 line-clamp-2">
                                    {{ $child->organization_description ?: 'Tidak ada deskripsi' }}
                                </p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>{{ $child->children_count }} sub</span>
                                    <span>{{ $child->risks_count }} risiko</span>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-span-12 xl:col-span-4">
        <!-- Aksi Cepat -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Aksi Cepat
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    <a href="{{ route('risks.create', ['organization_id' => $organization->organization_id]) }}" 
                       class="flex items-center p-3 bg-theme-1/5 hover:bg-theme-1/10 rounded-lg transition-colors">
                        <div class="w-10 h-10 rounded-full bg-theme-1/10 flex items-center justify-center mr-3">
                            <i data-feather="plus-circle" class="w-5 h-5 text-theme-1"></i>
                        </div>
                        <div>
                            <div class="font-medium">Tambah Risiko Baru</div>
                            <div class="text-xs text-gray-600">Tambah data risiko untuk organisasi ini</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('organizations.risks', $organization->organization_id) }}" 
                       class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i data-feather="alert-triangle" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div>
                            <div class="font-medium">Lihat Semua Risiko</div>
                            <div class="text-xs text-gray-600">Lihat daftar risiko organisasi ini</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('reports.create', ['organization_id' => $organization->organization_id]) }}" 
                       class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                            <i data-feather="file-text" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div>
                            <div class="font-medium">Buat Laporan</div>
                            <div class="text-xs text-gray-600">Buat laporan untuk organisasi ini</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('organizations.create', ['parent_id' => $organization->organization_id]) }}" 
                       class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                            <i data-feather="git-branch" class="w-5 h-5 text-purple-600"></i>
                        </div>
                        <div>
                            <div class="font-medium">Tambah Sub-Organisasi</div>
                            <div class="text-xs text-gray-600">Tambah unit kerja di bawah organisasi ini</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Ringkasan Risiko -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Ringkasan Risiko
                </h2>
                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded">
                    Total: {{ $organization->risks_count ?? 0 }}
                </span>
            </div>
            <div class="p-5">
                @if($organization->risks_count > 0)
                    <div class="space-y-4">
                        <!-- Risk Level Distribution -->
                        @php
                            $riskLevels = [
                                'sangat_tinggi' => ['count' => 0, 'color' => 'bg-red-100 text-red-800'],
                                'tinggi' => ['count' => 0, 'color' => 'bg-orange-100 text-orange-800'],
                                'sedang' => ['count' => 0, 'color' => 'bg-yellow-100 text-yellow-800'],
                                'rendah' => ['count' => 0, 'color' => 'bg-green-100 text-green-800'],
                                'sangat_rendah' => ['count' => 0, 'color' => 'bg-blue-100 text-blue-800'],
                            ];
                            
                            // Count risks by level (assuming risks relationship exists)
                            foreach($organization->risks as $risk) {
                                if(isset($riskLevels[$risk->risk_level])) {
                                    $riskLevels[$risk->risk_level]['count']++;
                                }
                            }
                        @endphp
                        
                        @foreach($riskLevels as $level => $data)
                            @if($data['count'] > 0)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="capitalize">{{ str_replace('_', ' ', $level) }}</span>
                                    <span class="font-medium">{{ $data['count'] }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="{{ $data['color'] }} h-2 rounded-full" 
                                         style="width: {{ ($data['count'] / $organization->risks_count) * 100 }}%"></div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                        
                        <!-- Recent Risks -->
                        <div class="mt-6">
                            <h4 class="font-medium mb-3">Risiko Terbaru</h4>
                            <div class="space-y-2">
                                @foreach($organization->risks->take(3) as $risk)
                                <a href="{{ route('risks.show', $risk->risk_id) }}" 
                                   class="flex items-center p-2 hover:bg-gray-50 rounded">
                                    <div class="w-8 h-8 rounded-full {{ $riskLevels[$risk->risk_level]['color'] ?? 'bg-gray-100' }} 
                                              flex items-center justify-center mr-3">
                                        <i data-feather="alert-circle" class="w-4 h-4"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium text-sm">{{ $risk->risk_code }}</div>
                                        <div class="text-xs text-gray-500 truncate">{{ Str::limit($risk->risk_description, 30) }}</div>
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $risk->created_at->format('d/m') }}</div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i data-feather="alert-triangle" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                        <p class="text-gray-500 mb-2">Belum ada data risiko</p>
                        <a href="{{ route('risks.create', ['organization_id' => $organization->organization_id]) }}" 
                           class="text-theme-1 text-sm hover:underline">
                            + Tambah risiko pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});
</script>
@endpush