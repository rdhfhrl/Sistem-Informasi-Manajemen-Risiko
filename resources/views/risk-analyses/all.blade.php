@extends('layouts.master')

@section('title', 'Semua Analisis Risiko - SIMR')

@section('page-title', 'Semua Analisis Risiko')

@section('page-action')
<a href="{{ route('risks.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Daftar Risiko
</a>
<a href="{{ route('risk-analyses.index') }}" class="btn btn-primary shadow-md mr-2">
    <i data-feather="filter" class="w-4 h-4 mr-2"></i> Analisis Per Risiko
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Stats Cards -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-100">
                                <i data-feather="bar-chart-2" class="w-6 h-6 text-blue-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <div class="text-3xl font-bold leading-8">{{ $totalAnalyses }}</div>
                            <div class="text-base text-gray-600 mt-1">Total Analisis</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-red-100">
                                <i data-feather="alert-triangle" class="w-6 h-6 text-red-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <div class="text-3xl font-bold leading-8">{{ $highRiskCount }}</div>
                            <div class="text-base text-gray-600 mt-1">Risiko Tinggi</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-yellow-100">
                                <i data-feather="alert-triangle" class="w-6 h-6 text-yellow-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <div class="text-3xl font-bold leading-8">{{ $mediumRiskCount }}</div>
                            <div class="text-base text-gray-600 mt-1">Risiko Sedang</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-100">
                                <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <div class="text-3xl font-bold leading-8">{{ $lowRiskCount }}</div>
                            <div class="text-base text-gray-600 mt-1">Risiko Rendah</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Daftar Semua Analisis Risiko
                    <span class="text-gray-500 text-sm ml-2">({{ $analyses->total() }} data)</span>
                </h2>
            </div>
            <div class="p-5">
                @if($analyses->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-report -mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">RISIKO</th>
                                    <th class="whitespace-nowrap">TANGGAL ANALISIS</th>
                                    <th class="whitespace-nowrap">SKOR RISIKO</th>
                                    <th class="whitespace-nowrap">LEVEL RISIKO</th>
                                    <th class="whitespace-nowrap">LIKELIHOOD</th>
                                    <th class="whitespace-nowrap">IMPACT</th>
                                    <th class="whitespace-nowrap">TREND</th>
                                    <th class="whitespace-nowrap text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analyses as $analysis)
                                    @php
                                        $trend = $analysis->trend;
                                        $trendIcon = $analysis->trend_icon;
                                        $trendColor = $analysis->trend_color;
                                    @endphp
                                    <tr class="intro-x hover:bg-gray-50">
                                        <td>
                                            @if($analysis->risk)
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                                        <i data-feather="alert-triangle" class="w-5 h-5 text-red-600"></i>
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('risks.show', $analysis->risk->risk_id) }}" 
                                                           class="font-medium hover:text-red-600">
                                                            {{ $analysis->risk->risk_code }}
                                                        </a>
                                                        <div class="text-gray-500 text-xs mt-0.5">
                                                            {{ Str::limit($analysis->risk->risk_description, 30) }}
                                                        </div>
                                                        @if($analysis->risk->project)
                                                            <div class="text-xs text-blue-600 mt-1">
                                                                <i data-feather="briefcase" class="w-3 h-3 mr-1"></i>
                                                                {{ Str::limit($analysis->risk->project->pro_nama, 20) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-400">Risiko tidak ditemukan</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                                    <i data-feather="calendar" class="w-5 h-5 text-gray-600"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium">
                                                        {{ $analysis->analysis_date->format('d M Y') }}
                                                    </div>
                                                    <div class="text-gray-500 text-xs mt-0.5">
                                                        {{ $analysis->analysis_date->format('H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="text-2xl font-bold 
                                                @if($analysis->risk_level == 'sangat_tinggi') text-red-600
                                                @elseif($analysis->risk_level == 'tinggi') text-orange-600
                                                @elseif($analysis->risk_level == 'sedang') text-yellow-600
                                                @elseif($analysis->risk_level == 'rendah') text-blue-600
                                                @else text-green-600
                                                @endif">
                                                {{ $analysis->risk_score }}
                                            </div>
                                            <div class="text-gray-500 text-xs">
                                                ({{ $analysis->likelihood_level }} × {{ $analysis->impact_level }})
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <span class="px-3 py-1 rounded-full text-xs font-medium 
                                                @if($analysis->risk_level == 'sangat_rendah') bg-green-100 text-green-800
                                                @elseif($analysis->risk_level == 'rendah') bg-blue-100 text-blue-800
                                                @elseif($analysis->risk_level == 'sedang') bg-yellow-100 text-yellow-800
                                                @elseif($analysis->risk_level == 'tinggi') bg-orange-100 text-orange-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                @switch($analysis->risk_level)
                                                    @case('sangat_rendah') Sangat Rendah @break
                                                    @case('rendah') Rendah @break
                                                    @case('sedang') Sedang @break
                                                    @case('tinggi') Tinggi @break
                                                    @case('sangat_tinggi') Sangat Tinggi @break
                                                @endswitch
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <div class="flex items-center">
                                                <div class="mr-2 text-lg">{{ $analysis->likelihood_level }}</div>
                                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-500 h-2 rounded-full" 
                                                         style="width: {{ ($analysis->likelihood_level / 5) * 100 }}%"></div>
                                                </div>
                                            </div>
                                            <div class="text-gray-500 text-xs mt-1">
                                                @php
                                                    $likelihoodText = [
                                                        1 => 'Sangat Rendah',
                                                        2 => 'Rendah',
                                                        3 => 'Sedang',
                                                        4 => 'Tinggi',
                                                        5 => 'Sangat Tinggi'
                                                    ][$analysis->likelihood_level] ?? 'Tidak diketahui';
                                                @endphp
                                                {{ $likelihoodText }}
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="flex items-center">
                                                <div class="mr-2 text-lg">{{ $analysis->impact_level }}</div>
                                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-red-500 h-2 rounded-full" 
                                                         style="width: {{ ($analysis->impact_level / 5) * 100 }}%"></div>
                                                </div>
                                            </div>
                                            <div class="text-gray-500 text-xs mt-1">
                                                @php
                                                    $impactText = [
                                                        1 => 'Sangat Kecil',
                                                        2 => 'Kecil',
                                                        3 => 'Sedang',
                                                        4 => 'Besar',
                                                        5 => 'Sangat Besar'
                                                    ][$analysis->impact_level] ?? 'Tidak diketahui';
                                                @endphp
                                                {{ $impactText }}
                                            </div>
                                        </td>
                                        
                                        <td>
                                            @if($trend !== 'new')
                                                <div class="flex items-center">
                                                    <i data-feather="{{ $trendIcon }}" 
                                                       class="w-4 h-4 mr-2 text-{{ $trendColor }}-600"></i>
                                                    <span class="text-sm">
                                                        @switch($trend)
                                                            @case('increase') Meningkat @break
                                                            @case('decrease') Menurun @break
                                                            @case('stable') Stabil @break
                                                        @endswitch
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-sm">Analisis Baru</span>
                                            @endif
                                        </td>
                                        
                                        <!-- PERBAIKAN TOMBOL AKSI -->
                                        <td class="text-center">
                                            <div class="flex justify-center items-center space-x-2">
                                                <!-- Tombol Detail Analisis -->
                                                <a href="{{ route('risk-analyses.show', [$analysis->risk_analysis_risk_id, $analysis->risk_analysis_id]) }}" 
                                                   class="btn btn-primary btn-sm" title="Detail Analisis">
                                                    <i data-feather="eye" class="w-4 h-4"></i>
                                                </a>
                                                
                                                <!-- Tombol Edit Analisis -->
                                                <a href="{{ route('risk-analyses.edit', [$analysis->risk_analysis_risk_id, $analysis->risk_analysis_id]) }}" 
                                                   class="btn btn-warning btn-sm" title="Edit Analisis">
                                                    <i data-feather="edit" class="w-4 h-4"></i>
                                                </a>
                                                
                                                <!-- Tombol Lihat Risiko -->
                                                <a href="{{ route('risks.show', $analysis->risk_analysis_risk_id) }}" 
                                                   class="btn btn-secondary btn-sm" title="Lihat Risiko">
                                                    <i data-feather="alert-triangle" class="w-4 h-4"></i>
                                                </a>
                                                
                                                <!-- Tombol Hapus -->
                                                <form method="POST" 
                                                      action="{{ route('risk-analyses.destroy', [$analysis->risk_analysis_risk_id, $analysis->risk_analysis_id]) }}" 
                                                      class="delete-form inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                            onclick="return confirm('Hapus analisis ini?')"
                                                            title="Hapus Analisis">
                                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($analyses->hasPages())
                    <div class="flex flex-col sm:flex-row items-center p-5 border-t border-gray-200">
                        <div class="text-sm text-gray-500">
                            Menampilkan {{ $analyses->firstItem() }} - {{ $analyses->lastItem() }} dari {{ $analyses->total() }} analisis
                        </div>
                        <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                            {{ $analyses->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="bar-chart-2" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        @if(request()->hasAny(['search', 'risk_level', 'date_range']))
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Tidak ditemukan</h3>
                            <p class="text-gray-500 mb-6">Tidak ada analisis risiko yang sesuai dengan filter yang dipilih</p>
                            <a href="{{ route('risk-analyses.all') }}" 
                               class="btn btn-secondary mr-2">
                                <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i> Reset Filter
                            </a>
                        @else
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada analisis risiko</h3>
                            <p class="text-gray-500 mb-6">Analisis risiko akan muncul setelah dibuat pada masing-masing risiko</p>
                        @endif
                        <a href="{{ route('risks.index') }}" 
                           class="btn btn-primary mr-2">
                            <i data-feather="alert-triangle" class="w-4 h-4 mr-2"></i> Lihat Daftar Risiko
                        </a>
                        <a href="{{ route('risk-analyses.index') }}" 
                           class="btn btn-success">
                            <i data-feather="filter" class="w-4 h-4 mr-2"></i> Analisis Per Risiko
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
    .table-report td {
        vertical-align: middle;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1.5;
        border-radius: 0.25rem;
    }
    
    .space-x-2 > * + * {
        margin-left: 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) closeBtn.click();
        });
    }, 5000);
});
</script>
@endpush