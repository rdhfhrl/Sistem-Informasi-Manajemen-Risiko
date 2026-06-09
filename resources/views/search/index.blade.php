@extends('layouts.master')

@section('title', 'Pencarian - SIMR')

@section('page-title', 'Hasil Pencarian')

@section('breadcrumb')
@parent
<li class="breadcrumb-item active">Pencarian</li>
@endsection

@section('page-action')
<a href="{{ route('search.advanced') }}" class="btn btn-outline-primary shadow-md">
    <i data-feather="search" class="w-4 h-4 mr-2"></i> Pencarian Lanjutan
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Search Header -->
        <div class="intro-y box mb-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="search" class="w-5 h-5 mr-2 text-blue-500"></i>
                    Pencarian
                </h2>
                @if($query)
                <span class="text-gray-500 text-sm">
                    {{ $totalResults }} hasil ditemukan
                </span>
                @endif
            </div>
            <div class="p-5">
                <!-- Search Form -->
                <form action="{{ route('search.index') }}" method="GET" class="mb-8">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <div class="relative">
                                <input type="text" 
                                       name="q" 
                                       class="form-control w-full pr-10" 
                                       placeholder="Cari risiko, proyek, laporan, pengguna..."
                                       value="{{ $query }}"
                                       required>
                                <i data-feather="search" class="absolute right-3 top-3 text-gray-400"></i>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="search" class="w-4 h-4 mr-2"></i> Cari
                        </button>
                    </div>
                </form>

                <!-- Quick Tips -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 mb-6">
                    <div class="flex items-start">
                        <i data-feather="info" class="w-5 h-5 text-blue-600 mr-3 mt-0.5"></i>
                        <div>
                            <h4 class="font-medium text-blue-800 mb-1">Tips Pencarian</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Gunakan minimal 2 karakter untuk pencarian</li>
                                <li>• Pisahkan kata kunci dengan spasi</li>
                                <li>• Gunakan <strong>pencarian lanjutan</strong> untuk filter lebih spesifik</li>
                            </ul>
                        </div>
                    </div>
                </div>

                @if(strlen($query) < 2)
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="search" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Masukkan Kata Kunci</h3>
                        <p class="text-gray-500 mb-6">
                            Masukkan minimal 2 karakter untuk memulai pencarian
                        </p>
                    </div>
                @elseif($totalResults == 0)
                    <!-- No Results -->
                    <div class="text-center py-12">
                        <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="frown" class="w-8 h-8 text-yellow-600"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Tidak ada hasil ditemukan</h3>
                        <p class="text-gray-500 mb-4">
                            Tidak ditemukan hasil untuk <strong>"{{ $query }}"</strong>
                        </p>
                        <div class="text-sm text-gray-600">
                            Coba kata kunci yang berbeda atau gunakan pencarian lanjutan
                        </div>
                    </div>
                @else
                    <!-- Search Results -->
                    <div class="space-y-8">
                        <!-- Risks Results -->
                        @if($results['risks']->total() > 0)
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-700">
                                    <i data-feather="alert-triangle" class="w-5 h-5 inline mr-2 text-orange-500"></i>
                                    Risiko
                                    <span class="text-gray-500 text-sm font-normal">({{ $results['risks']->total() }})</span>
                                </h3>
                                @if($results['risks']->total() > 5)
                                <a href="{{ route('risks.index', ['search' => $query]) }}" 
                                   class="text-blue-600 text-sm hover:text-blue-800">
                                    Lihat semua →
                                </a>
                                @endif
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($results['risks']->take(6) as $risk)
                                <a href="{{ route('risks.show', $risk->risk_id) }}" 
                                   class="group block p-4 border rounded-lg hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-start">
                                        <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center mr-3">
                                            <i data-feather="alert-triangle" class="w-5 h-5 text-orange-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-medium group-hover:text-blue-600 mb-1">
                                                {{ $risk->risk_name }}
                                            </div>
                                            <div class="text-sm text-gray-600 mb-2">
                                                {{ $risk->risk_code }}
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="px-2 py-1 rounded-full text-xs font-medium 
                                                    @if($risk->risk_level == 'sangat_tinggi') bg-red-100 text-red-800
                                                    @elseif($risk->risk_level == 'tinggi') bg-orange-100 text-orange-800
                                                    @elseif($risk->risk_level == 'sedang') bg-yellow-100 text-yellow-800
                                                    @else bg-green-100 text-green-800
                                                    @endif">
                                                    {{ $risk->risk_level_label }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    {{ $risk->created_at->format('d M Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Projects Results -->
                        @if($results['projects']->total() > 0)
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-700">
                                    <i data-feather="briefcase" class="w-5 h-5 inline mr-2 text-blue-500"></i>
                                    Proyek
                                    <span class="text-gray-500 text-sm font-normal">({{ $results['projects']->total() }})</span>
                                </h3>
                                @if($results['projects']->total() > 5)
                                <a href="{{ route('projects.index', ['search' => $query]) }}" 
                                   class="text-blue-600 text-sm hover:text-blue-800">
                                    Lihat semua →
                                </a>
                                @endif
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($results['projects']->take(6) as $project)
                                <a href="{{ route('projects.show', $project->pro_id) }}" 
                                   class="group block p-4 border rounded-lg hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-start">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                            <i data-feather="briefcase" class="w-5 h-5 text-blue-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-medium group-hover:text-blue-600 mb-1">
                                                {{ $project->pro_nama }}
                                            </div>
                                            <div class="text-sm text-gray-600 mb-2 line-clamp-2">
                                                {{ Str::limit($project->pro_deskripsi, 80) }}
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">
                                                    {{ $project->pro_status ?? 'Aktif' }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    {{ $project->created_at->format('d M Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Reports Results -->
                        @if($results['reports']->total() > 0)
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-700">
                                    <i data-feather="file-text" class="w-5 h-5 inline mr-2 text-green-500"></i>
                                    Laporan
                                    <span class="text-gray-500 text-sm font-normal">({{ $results['reports']->total() }})</span>
                                </h3>
                                @if($results['reports']->total() > 5)
                                <a href="{{ route('reports.index', ['search' => $query]) }}" 
                                   class="text-blue-600 text-sm hover:text-blue-800">
                                    Lihat semua →
                                </a>
                                @endif
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($results['reports']->take(6) as $report)
                                <a href="{{ route('reports.show', $report->report_id) }}" 
                                   class="group block p-4 border rounded-lg hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-start">
                                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                            <i data-feather="file-text" class="w-5 h-5 text-green-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-medium group-hover:text-blue-600 mb-1">
                                                {{ $report->title }}
                                            </div>
                                            <div class="text-sm text-gray-600 mb-2 line-clamp-2">
                                                {{ Str::limit($report->description, 80) }}
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="px-2 py-1 rounded-full text-xs 
                                                    @if($report->status == 'published') bg-green-100 text-green-800
                                                    @elseif($report->status == 'draft') bg-yellow-100 text-yellow-800
                                                    @elseif($report->status == 'generated') bg-blue-100 text-blue-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucfirst($report->status) }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    {{ $report->report_date?->format('d M Y') ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Users Results -->
                        @if($results['users']->total() > 0)
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-700">
                                    <i data-feather="users" class="w-5 h-5 inline mr-2 text-purple-500"></i>
                                    Pengguna
                                    <span class="text-gray-500 text-sm font-normal">({{ $results['users']->total() }})</span>
                                </h3>
                                @if($results['users']->total() > 5)
                                <a href="{{ route('users.index', ['search' => $query]) }}" 
                                   class="text-blue-600 text-sm hover:text-blue-800">
                                    Lihat semua →
                                </a>
                                @endif
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($results['users']->take(6) as $user)
                                <a href="{{ route('users.show', $user->id) }}" 
                                   class="group block p-4 border rounded-lg hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-center">
                                        @if($user->avatar && file_exists(public_path('storage/' . $user->avatar)))
                                            <img src="{{ asset('storage/' . $user->avatar) }}" 
                                                 alt="{{ $user->name }}"
                                                 class="w-10 h-10 rounded-full mr-3 object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                                <i data-feather="user" class="w-5 h-5 text-purple-600"></i>
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <div class="font-medium group-hover:text-blue-600 mb-1">
                                                {{ $user->name }}
                                            </div>
                                            <div class="text-sm text-gray-600 mb-2">
                                                {{ $user->email }}
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">
                                                    {{ $user->role_name }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif
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