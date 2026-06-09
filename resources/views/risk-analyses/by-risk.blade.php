@extends('layouts.master')

@section('title', 'Analisis Risiko - SIMR')

@section('page-title', 'Analisis Risiko')

@section('page-action')
<a href="{{ route('risks.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Daftar Risiko
</a>
<a href="{{ route('risk-analyses.create', $risk->risk_id) }}" class="btn btn-primary shadow-md mr-2">
    <i data-feather="plus" class="w-4 h-4 mr-2"></i> Analisis Baru
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Risk Info Card -->
        <div class="intro-y box mb-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="alert-triangle" class="w-5 h-5 mr-2 text-red-500"></i>
                    Informasi Risiko
                </h2>
                @if($risk->risk_level)
                <span class="px-3 py-1 rounded-full text-xs font-medium 
                    @if($risk->risk_level == 'sangat_rendah') bg-green-100 text-green-800
                    @elseif($risk->risk_level == 'rendah') bg-blue-100 text-blue-800
                    @elseif($risk->risk_level == 'sedang') bg-yellow-100 text-yellow-800
                    @elseif($risk->risk_level == 'tinggi') bg-orange-100 text-orange-800
                    @else bg-red-100 text-red-800
                    @endif">
                    @switch($risk->risk_level)
                        @case('sangat_rendah') Sangat Rendah @break
                        @case('rendah') Rendah @break
                        @case('sedang') Sedang @break
                        @case('tinggi') Tinggi @break
                        @case('sangat_tinggi') Sangat Tinggi @break
                    @endswitch
                </span>
                @endif
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Kode Risiko</div>
                        <div class="font-medium">{{ $risk->risk_code }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Deskripsi Risiko</div>
                        <div class="font-medium">{{ Str::limit($risk->risk_description, 50) }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Tanggal Analisis Terakhir</div>
                        <div class="font-medium">
                            @if($risk->last_analysis_date)
                                {{ \Carbon\Carbon::parse($risk->last_analysis_date)->format('d F Y') }}
                            @else
                                <span class="text-gray-400">Belum ada analisis</span>
                            @endif
                        </div>
                    </div>
                    @if($risk->risk_score)
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Skor Risiko</div>
                        <div class="font-medium text-lg 
                            @if($risk->risk_level == 'sangat_tinggi') text-red-600
                            @elseif($risk->risk_level == 'tinggi') text-orange-600
                            @elseif($risk->risk_level == 'sedang') text-yellow-600
                            @elseif($risk->risk_level == 'rendah') text-blue-600
                            @else text-green-600
                            @endif">
                            {{ $risk->risk_score }}
                            <span class="text-gray-500 text-sm">({{ $risk->likelihood_level }} × {{ $risk->impact_level }})</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-red-100">
                                <i data-feather="bar-chart" class="w-6 h-6 text-red-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <div class="text-3xl font-bold leading-8">{{ $analyses->total() }}</div>
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
                                <i data-feather="trending-up" class="w-6 h-6 text-red-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            @php
                                $highRiskCount = collect($analyses->items())->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])->count();
                            @endphp
                            <div class="text-3xl font-bold leading-8">{{ $highRiskCount }}</div>
                            <div class="text-base text-gray-600 mt-1">Analisis Risiko Tinggi</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-yellow-100">
                                <i data-feather="activity" class="w-6 h-6 text-yellow-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            @php
                                $mediumRiskCount = collect($analyses->items())->where('risk_level', 'sedang')->count();
                            @endphp
                            <div class="text-3xl font-bold leading-8">{{ $mediumRiskCount }}</div>
                            <div class="text-base text-gray-600 mt-1">Analisis Risiko Sedang</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-100">
                                <i data-feather="trending-down" class="w-6 h-6 text-green-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            @php
                                $lowRiskCount = collect($analyses->items())->whereIn('risk_level', ['rendah', 'sangat_rendah'])->count();
                            @endphp
                            <div class="text-3xl font-bold leading-8">{{ $lowRiskCount }}</div>
                            <div class="text-base text-gray-600 mt-1">Analisis Risiko Rendah</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Riwayat Analisis Risiko
                    <span class="text-gray-500 text-sm ml-2">({{ $analyses->total() }} data)</span>
                </h2>
            </div>
            <div class="p-5">
                @if($analyses->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-report -mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">TANGGAL ANALISIS</th>
                                    <th class="whitespace-nowrap">SKOR RISIKO</th>
                                    <th class="whitespace-nowrap">LEVEL RISIKO</th>
                                    <th class="whitespace-nowrap">LIKELIHOOD</th>
                                    <th class="whitespace-nowrap">IMPACT</th>
                                    <th class="whitespace-nowrap">TREND</th>
                                    <th class="whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analyses as $analysis)
                                    <tr class="intro-x hover:bg-gray-50" data-risk-level="{{ $analysis->risk_level }}">
                                        <td>
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                                    <i data-feather="calendar" class="w-5 h-5 text-gray-600"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium">
                                                        {{ \Carbon\Carbon::parse($analysis->analysis_date)->format('d M Y') }}
                                                    </div>
                                                    <div class="text-gray-500 text-xs mt-0.5">
                                                        @if($analysis->isLatestAnalysis())
                                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Analisis Terbaru</span>
                                                        @endif
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
                                                <div class="w-24 bg-gray-200 rounded-full h-2">
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
                                                <div class="w-24 bg-gray-200 rounded-full h-2">
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
                                            @php
                                                $trend = $analysis->trend;
                                                $trendIcon = $analysis->trend_icon;
                                                $trendColor = $analysis->trend_color;
                                            @endphp
                                            @if($trend !== 'new')
                                                <div class="flex items-center">
                                                    <i data-feather="{{ $trendIcon }}" 
                                                       class="w-5 h-5 mr-2 text-{{ $trendColor }}-600"></i>
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
                                        
                                        <td class="table-report__action w-56">
                                            <div class="flex justify-center items-center">
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('risk-analyses.show', [$risk->risk_id, $analysis->risk_analysis_id]) }}">
                                                    <i data-feather="eye" class="w-4 h-4 mr-1"></i> Detail
                                                </a>
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('risk-analyses.edit', [$risk->risk_id, $analysis->risk_analysis_id]) }}">
                                                    <i data-feather="edit" class="w-4 h-4 mr-1"></i> Edit
                                                </a>
                                                <form action="{{ route('risk-analyses.destroy', [$risk->risk_id, $analysis->risk_analysis_id]) }}" 
                                                      method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus analisis ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex items-center text-red-600 hover:text-red-800">
                                                        <i data-feather="trash-2" class="w-4 h-4 mr-1"></i> Hapus
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
                            <i data-feather="bar-chart" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada analisis risiko</h3>
                        <p class="text-gray-500 mb-6">Analisis risiko akan muncul setelah Anda membuat analisis untuk risiko ini</p>
                        <a href="{{ route('risk-analyses.create', $risk->risk_id) }}" 
                           class="btn btn-primary">
                            <i data-feather="plus" class="w-4 h-4 mr-2"></i> Buat Analisis Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Risk Matrix Visualization -->
        @if($analyses->count() > 0)
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="grid" class="w-5 h-5 mr-2"></i> Matriks Risiko
                </h2>
                <div class="text-xs text-gray-500">
                    Posisi saat ini: Likelihood {{ $analyses->first()->likelihood_level }}, Impact {{ $analyses->first()->impact_level }}
                </div>
            </div>
            <div class="p-5">
                <div class="risk-matrix mb-8">
                    <h4 class="font-medium mb-4">Posisi Risiko Terbaru</h4>
                    
                    <!-- Versi 1: Tabel dengan header yang benar -->
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr>
                                    <th class="w-20 p-2"></th>
                                    @for($impact = 1; $impact <= 5; $impact++)
                                        <th class="text-center p-2 font-medium bg-gray-50 border">
                                            Impact {{ $impact }}
                                            <div class="text-xs font-normal text-gray-500">
                                                @php
                                                    $impactText = [
                                                        1 => 'Sangat Kecil',
                                                        2 => 'Kecil',
                                                        3 => 'Sedang',
                                                        4 => 'Besar',
                                                        5 => 'Sangat Besar'
                                                    ][$impact] ?? '';
                                                @endphp
                                                {{ $impactText }}
                                            </div>
                                        </th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @for($likelihood = 5; $likelihood >= 1; $likelihood--)
                                    <tr>
                                        <td class="p-2 font-medium bg-gray-50 border text-center">
                                            Likelihood {{ $likelihood }}
                                            <div class="text-xs font-normal text-gray-500">
                                                @php
                                                    $likelihoodText = [
                                                        1 => 'Sangat Rendah',
                                                        2 => 'Rendah',
                                                        3 => 'Sedang',
                                                        4 => 'Tinggi',
                                                        5 => 'Sangat Tinggi'
                                                    ][$likelihood] ?? '';
                                                @endphp
                                                {{ $likelihoodText }}
                                            </div>
                                        </td>
                                        @for($impact = 1; $impact <= 5; $impact++)
                                            @php
                                                $score = $likelihood * $impact;
                                                $latestAnalysis = $analyses->first();
                                                $isCurrent = $latestAnalysis && 
                                                            $latestAnalysis->likelihood_level == $likelihood && 
                                                            $latestAnalysis->impact_level == $impact;
                                                
                                                // Tentukan warna berdasarkan skor
                                                $color = '';
                                                $textColor = 'text-white';
                                                if ($score >= 20) {
                                                    $color = 'bg-red-500';
                                                } elseif ($score >= 15) {
                                                    $color = 'bg-orange-500';
                                                } elseif ($score >= 10) {
                                                    $color = 'bg-yellow-500';
                                                    $textColor = 'text-gray-800';
                                                } elseif ($score >= 5) {
                                                    $color = 'bg-blue-500';
                                                } else {
                                                    $color = 'bg-green-500';
                                                }
                                            @endphp
                                            <td class="border p-0">
                                                <div class="relative h-20 flex flex-col items-center justify-center {{ $color }} hover:opacity-90 transition-opacity cursor-pointer group"
                                                    title="Score: {{ $score }} (L{{ $likelihood }} × I{{ $impact }})">
                                                    <span class="{{ $textColor }} font-bold text-lg">{{ $score }}</span>
                                                    <span class="{{ $textColor }} text-xs opacity-75">L{{ $likelihood }}×I{{ $impact }}</span>
                                                    
                                                    @if($isCurrent)
                                                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-white border-2 border-red-500 rounded-full flex items-center justify-center shadow-lg">
                                                        <i data-feather="target" class="w-3 h-3 text-red-600"></i>
                                                    </div>
                                                    <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-1 px-2 py-1 bg-red-600 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10">
                                                        Posisi Saat Ini
                                                    </div>
                                                    @endif
                                                    
                                                    <!-- Tooltip untuk semua sel -->
                                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-1 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10">
                                                        Likelihood: {{ $likelihoodText[$likelihood] ?? $likelihood }}<br>
                                                        Impact: {{ $impactText[$impact] ?? $impact }}<br>
                                                        Score: {{ $score }}
                                                    </div>
                                                </div>
                                            </td>
                                        @endfor
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Versi 2: Grid layout alternatif (jika tabel tidak cocok) -->
                    <!--
                    <div class="grid grid-cols-6 gap-1">
                        <div class="col-span-1"></div>
                        @for($impact = 1; $impact <= 5; $impact++)
                            <div class="text-center p-2 bg-gray-100 rounded">
                                <div class="font-medium">Impact {{ $impact }}</div>
                                <div class="text-xs text-gray-600">
                                    @php
                                        $impactText = [
                                            1 => 'Sangat Kecil',
                                            2 => 'Kecil',
                                            3 => 'Sedang',
                                            4 => 'Besar',
                                            5 => 'Sangat Besar'
                                        ][$impact] ?? '';
                                    @endphp
                                    {{ $impactText }}
                                </div>
                            </div>
                        @endfor
                        
                        @for($likelihood = 5; $likelihood >= 1; $likelihood--)
                            <div class="p-2 bg-gray-100 rounded flex flex-col justify-center">
                                <div class="font-medium text-center">Likelihood {{ $likelihood }}</div>
                                <div class="text-xs text-gray-600 text-center">
                                    @php
                                        $likelihoodText = [
                                            1 => 'Sangat Rendah',
                                            2 => 'Rendah',
                                            3 => 'Sedang',
                                            4 => 'Tinggi',
                                            5 => 'Sangat Tinggi'
                                        ][$likelihood] ?? '';
                                    @endphp
                                    {{ $likelihoodText }}
                                </div>
                            </div>
                            
                            @for($impact = 1; $impact <= 5; $impact++)
                                @php
                                    $score = $likelihood * $impact;
                                    $latestAnalysis = $analyses->first();
                                    $isCurrent = $latestAnalysis && 
                                                $latestAnalysis->likelihood_level == $likelihood && 
                                                $latestAnalysis->impact_level == $impact;
                                    
                                    $color = '';
                                    $textColor = 'text-white';
                                    if ($score >= 20) {
                                        $color = 'bg-red-500';
                                    } elseif ($score >= 15) {
                                        $color = 'bg-orange-500';
                                    } elseif ($score >= 10) {
                                        $color = 'bg-yellow-500';
                                        $textColor = 'text-gray-800';
                                    } elseif ($score >= 5) {
                                        $color = 'bg-blue-500';
                                    } else {
                                        $color = 'bg-green-500';
                                    }
                                @endphp
                                
                                <div class="relative h-20 flex flex-col items-center justify-center {{ $color }} rounded hover:opacity-90 transition-opacity cursor-pointer group"
                                    title="Score: {{ $score }} (L{{ $likelihood }} × I{{ $impact }})">
                                    <span class="{{ $textColor }} font-bold text-lg">{{ $score }}</span>
                                    <span class="{{ $textColor }} text-xs opacity-75">L{{ $likelihood }}×I{{ $impact }}</span>
                                    
                                    @if($isCurrent)
                                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-white border-2 border-red-500 rounded-full flex items-center justify-center shadow-lg">
                                        <i data-feather="target" class="w-3 h-3 text-red-600"></i>
                                    </div>
                                    @endif
                                </div>
                            @endfor
                        @endfor
                    </div>
                    -->
                    
                    <!-- Legenda -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h5 class="font-medium mb-3">Legenda Matriks Risiko:</h5>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                            @php
                                $legendItems = [
                                    ['color' => 'bg-red-500', 'label' => 'Sangat Tinggi (20-25)', 'desc' => 'Risiko kritis, perlu tindakan segera'],
                                    ['color' => 'bg-orange-500', 'label' => 'Tinggi (15-19)', 'desc' => 'Risiko tinggi, butuh perhatian serius'],
                                    ['color' => 'bg-yellow-500', 'label' => 'Sedang (10-14)', 'desc' => 'Risiko sedang, perlu pemantauan'],
                                    ['color' => 'bg-blue-500', 'label' => 'Rendah (5-9)', 'desc' => 'Risiko rendah, bisa diterima'],
                                    ['color' => 'bg-green-500', 'label' => 'Sangat Rendah (1-4)', 'desc' => 'Risiko sangat rendah']
                                ];
                            @endphp
                            
                            @foreach($legendItems as $item)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-4 h-4 mt-1 {{ $item['color'] }} rounded mr-2"></div>
                                <div>
                                    <div class="font-medium text-sm">{{ $item['label'] }}</div>
                                    <div class="text-xs text-gray-600">{{ $item['desc'] }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Info posisi saat ini -->
                        @if($analyses->first())
                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded">
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-white border-2 border-red-500 rounded-full flex items-center justify-center mr-2">
                                    <i data-feather="target" class="w-3 h-3 text-red-600"></i>
                                </div>
                                <div>
                                    <span class="font-medium">Posisi Risiko Saat Ini:</span>
                                    Likelihood {{ $analyses->first()->likelihood_level }} 
                                    ({{ [
                                        1 => 'Sangat Rendah',
                                        2 => 'Rendah',
                                        3 => 'Sedang',
                                        4 => 'Tinggi',
                                        5 => 'Sangat Tinggi'
                                    ][$analyses->first()->likelihood_level] ?? 'Tidak diketahui' }}), 
                                    Impact {{ $analyses->first()->impact_level }} 
                                    ({{ [
                                        1 => 'Sangat Kecil',
                                        2 => 'Kecil',
                                        3 => 'Sedang',
                                        4 => 'Besar',
                                        5 => 'Sangat Besar'
                                    ][$analyses->first()->impact_level] ?? 'Tidak diketahui' }})
                                    = Skor {{ $analyses->first()->risk_score }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Score Trend Chart -->
                <div class="mt-8">
                    <h4 class="font-medium mb-4">Trend Skor Risiko</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <canvas id="scoreTrendChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Filter by risk level
    const filterSelect = document.getElementById('filter-level');
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const selectedLevel = this.value;
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                if (!selectedLevel || row.dataset.riskLevel === selectedLevel) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) closeBtn.click();
        });
    }, 5000);
    
    // Score Trend Chart
    @if($analyses->count() > 0)
    const ctx = document.getElementById('scoreTrendChart');
    if (ctx) {
        try {
            const analysesData = @json($analyses->items());
            // Urutkan dari yang paling lama ke terbaru
            analysesData.sort((a, b) => new Date(a.analysis_date) - new Date(b.analysis_date));
            
            const labels = analysesData.map(a => {
                const date = new Date(a.analysis_date);
                return date.toLocaleDateString('id-ID', { 
                    day: 'numeric', 
                    month: 'short' 
                });
            });
            
            const scores = analysesData.map(a => parseInt(a.risk_score) || 0);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Skor Risiko',
                        data: scores,
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: function(context) {
                            const index = context.dataIndex;
                            const score = scores[index];
                            if (score >= 20) return '#EF4444';
                            if (score >= 15) return '#F97316';
                            if (score >= 10) return '#EAB308';
                            if (score >= 5) return '#3B82F6';
                            return '#10B981';
                        },
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    const analysis = analysesData[context.dataIndex];
                                    return [
                                        `Skor: ${context.parsed.y}`,
                                        `Likelihood: ${analysis.likelihood_level}`,
                                        `Impact: ${analysis.impact_level}`,
                                        `Level: ${analysis.risk_level}`
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 25,
                            title: {
                                display: true,
                                text: 'Skor Risiko'
                            },
                            ticks: {
                                stepSize: 5
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Tanggal Analisis'
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating chart:', error);
            ctx.parentElement.innerHTML = '<p class="text-center text-gray-500">Gagal menampilkan chart. Data mungkin tidak lengkap.</p>';
        }
    }
    @endif
});
</script>
@endpush