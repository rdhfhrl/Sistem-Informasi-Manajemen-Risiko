@extends('layouts.master')

@section('title', $risk->risk_code . ' - SIMR')

@section('page-title', 'Detail Risiko: ' . $risk->risk_code)

@section('content')
<div class="grid grid-cols-12 gap-6">
    <!-- Page Title & Action - Dipindahkan ke dalam content -->
    <div class="col-span-12">
        <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                Detail Risiko: {{ $risk->risk_code }}
            </h2>
            <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
                <div class="flex items-center space-x-2">
                    <!-- Tombol Kembali -->
                    <a href="{{ route('risks.index') }}" class="btn btn-outline-secondary shadow-md">
                        <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
                    </a>
                    
                    <!-- Tombol Edit -->
                    <a href="{{ route('risks.edit', ['risk' => $risk->risk_id]) }}" 
                       class="btn btn-primary shadow-md">
                        <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit
                    </a>
                    
                    <!-- Dropdown Tambah Data -->
                    <div class="dropdown ml-2">
                        <button class="dropdown-toggle btn btn-success shadow-md" aria-expanded="false">
                            <i data-feather="plus" class="w-4 h-4 mr-2"></i> Tambah Data
                        </button>
                        <div class="dropdown-menu w-48">
                            <div class="dropdown-content">
                                @if($risk->identification)
                                    <a href="{{ route('risk-identifications.edit', ['riskId' => $risk->risk_id]) }}" 
                                       class="dropdown-item">
                                        <i data-feather="search" class="w-4 h-4 mr-2"></i> Edit Identifikasi
                                    </a>
                                @else
                                    <a href="{{ route('risk-identifications.create', ['riskId' => $risk->risk_id]) }}" 
                                       class="dropdown-item">
                                        <i data-feather="search" class="w-4 h-4 mr-2"></i> Identifikasi Risiko
                                    </a>
                                @endif
                                
                                <a href="{{ route('risk-analyses.create', ['riskId' => $risk->risk_id]) }}" 
                                   class="dropdown-item">
                                    <i data-feather="activity" class="w-4 h-4 mr-2"></i> Analisis
                                </a>
                                <a href="{{ route('risk-analyses.index', ['riskId' => $risk->risk_id]) }}" 
                                   class="dropdown-item">
                                    <i data-feather="list" class="w-4 h-4 mr-2"></i> Lihat Semua Analisis
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page Title & Action -->

    <div class="col-span-12 xl:col-span-8">
        <!-- Informasi Utama Risiko -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <div class="flex-1">
                    <h2 class="font-medium text-base mr-auto">
                        Informasi Utama Risiko
                    </h2>
                </div>
                <div class="flex items-center space-x-2">
                    @php
                        $levelColors = [
                            'sangat_rendah' => 'bg-green-100 text-green-800',
                            'rendah' => 'bg-yellow-100 text-yellow-800',
                            'sedang' => 'bg-orange-100 text-orange-800',
                            'tinggi' => 'bg-red-100 text-red-800',
                            'sangat_tinggi' => 'bg-red-600 text-white'
                        ];
                        $levelTexts = [
                            'sangat_rendah' => 'Sangat Rendah',
                            'rendah' => 'Rendah',
                            'sedang' => 'Sedang',
                            'tinggi' => 'Tinggi',
                            'sangat_tinggi' => 'Sangat Tinggi'
                        ];
                        $color = $levelColors[$risk->risk_level] ?? 'bg-gray-100 text-gray-800';
                        $text = $levelTexts[$risk->risk_level] ?? '-';
                    @endphp
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $color }}">
                        {{ $text }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        Skor: {{ $risk->risk_score ?? 0 }}
                    </span>
                </div>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <label class="form-label text-gray-500">Kode Risiko</label>
                            <div class="mt-1 text-lg font-bold text-red-600">
                                {{ $risk->risk_code }}
                            </div>
                        </div>
                        
                        <div>
                            <label class="form-label text-gray-500">Deskripsi Risiko</label>
                            <div class="mt-1 text-gray-700 whitespace-pre-line">
                                {{ $risk->risk_description }}
                            </div>
                        </div>
                        
                        <div>
                            <label class="form-label text-gray-500">Kategori Risiko</label>
                            <div class="mt-1">
                                @if($risk->category)
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        {{ $risk->category->risk_category_name }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <label class="form-label text-gray-500">Pemilik Risiko</label>
                            <div class="mt-1 flex items-center">
                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                    <i data-feather="user" class="w-4 h-4 text-green-600"></i>
                                </div>
                                <div>
                                    <div class="font-medium">{{ $risk->risk_owner }}</div>
                                    <div class="text-xs text-gray-500">Pemilik</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="form-label text-gray-500">Proyek</label>
                            <div class="mt-1">
                                @if($risk->project)
                                    <div class="flex items-center p-3 bg-theme-1/5 rounded-lg">
                                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3">
                                            <i data-feather="folder" class="w-4 h-4 text-theme-1"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium">{{ $risk->project->pro_nama }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $risk->project->pro_status ?? 'Status' }} • 
                                                {{ $risk->project->pro_tanggal_mulai ? \Carbon\Carbon::parse($risk->project->pro_tanggal_mulai)->format('d/m/Y') : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <label class="form-label text-gray-500">Organisasi</label>
                            <div class="mt-1">
                                @if($risk->organization)
                                    <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                            <i data-feather="home" class="w-4 h-4 text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium">{{ $risk->organization->organization_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $risk->organization->organization_code }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <label class="form-label text-gray-500">Tujuan Strategis</label>
                            <div class="mt-1">
                                @if($risk->strategicObjective)
                                    <div class="flex items-center p-3 bg-green-50 rounded-lg">
                                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                            <i data-feather="target" class="w-4 h-4 text-green-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium">{{ $risk->strategicObjective->strategic_objective_name }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <label class="form-label text-gray-500">Proses Bisnis</label>
                            <div class="mt-1">
                                @if($risk->businessProcess)
                                    <div class="flex items-center p-3 bg-purple-50 rounded-lg">
                                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                            <i data-feather="briefcase" class="w-4 h-4 text-purple-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium">{{ $risk->businessProcess->business_process_name }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Risk Score Visualization -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h4 class="font-medium mb-4">Visualisasi Skor Risiko</h4>
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-6">
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">Likelihood (Kemungkinan)</span>
                                    <span class="font-bold text-lg">{{ $risk->likelihood_level ?? 'N/A' }}/5</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    @php
                                        $likelihoodPercent = ($risk->likelihood_level ?? 0) * 20;
                                        $likelihoodColor = match($risk->likelihood_level ?? 0) {
                                            5 => 'bg-red-600',
                                            4 => 'bg-orange-500',
                                            3 => 'bg-yellow-500',
                                            2 => 'bg-blue-500',
                                            1 => 'bg-green-500',
                                            default => 'bg-gray-400'
                                        };
                                    @endphp
                                    <div class="{{ $likelihoodColor }} h-3 rounded-full" 
                                         style="width: {{ $likelihoodPercent }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    @php
                                        $likelihoodLabel = match($risk->likelihood_level ?? 0) {
                                            5 => 'Sangat Tinggi',
                                            4 => 'Tinggi',
                                            3 => 'Sedang',
                                            2 => 'Rendah',
                                            1 => 'Sangat Rendah',
                                            default => 'Belum dinilai'
                                        };
                                    @endphp
                                    {{ $likelihoodLabel }}
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 md:col-span-6">
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">Impact (Dampak)</span>
                                    <span class="font-bold text-lg">{{ $risk->impact_level ?? 'N/A' }}/5</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    @php
                                        $impactPercent = ($risk->impact_level ?? 0) * 20;
                                        $impactColor = match($risk->impact_level ?? 0) {
                                            5 => 'bg-red-600',
                                            4 => 'bg-orange-500',
                                            3 => 'bg-yellow-500',
                                            2 => 'bg-blue-500',
                                            1 => 'bg-green-500',
                                            default => 'bg-gray-400'
                                        };
                                    @endphp
                                    <div class="{{ $impactColor }} h-3 rounded-full" 
                                         style="width: {{ $impactPercent }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    @php
                                        $impactLabel = match($risk->impact_level ?? 0) {
                                            5 => 'Sangat Besar',
                                            4 => 'Besar',
                                            3 => 'Sedang',
                                            2 => 'Kecil',
                                            1 => 'Sangat Kecil',
                                            default => 'Belum dinilai'
                                        };
                                    @endphp
                                    {{ $impactLabel }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Identifikasi Risiko Section -->
        @if($risk->identification)
        <div class="intro-y box mt-6">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="search" class="w-5 h-5 mr-2"></i> Identifikasi Risiko
                </h2>
                <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                    <a href="{{ route('risk-identifications.edit', ['riskId' => $risk->risk_id]) }}" 
                    class="btn btn-warning btn-sm mr-2">
                        <i data-feather="edit" class="w-4 h-4 mr-1"></i> Edit
                    </a>
                    <a href="{{ route('risk-identifications.index') }}" 
                    class="btn btn-outline-primary btn-sm">
                        <i data-feather="list" class="w-4 h-4 mr-1"></i> Lihat Semua
                    </a>
                </div>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Jenis Kerugian -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                <i data-feather="dollar-sign" class="w-5 h-5 text-red-600"></i>
                            </div>
                            <div>
                                <div class="font-medium">Jenis Kerugian</div>
                                <div class="text-sm text-gray-500">Dampak potensial yang mungkin terjadi</div>
                            </div>
                        </div>
                        @if($risk->identification->loss_type)
                            <span class="px-3 py-1 rounded-full text-sm font-medium 
                                @if($risk->identification->loss_type == 'Reputasi') bg-red-100 text-red-800
                                @elseif($risk->identification->loss_type == 'Operasional') bg-orange-100 text-orange-800
                                @elseif($risk->identification->loss_type == 'Kepatuhan') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $risk->identification->loss_type }}
                            </span>
                        @else
                            <span class="text-gray-400">Belum diisi</span>
                        @endif
                    </div>
                    
                    <!-- Jenis Pelanggaran -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i data-feather="alert-octagon" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <div class="font-medium">Jenis Pelanggaran</div>
                                <div class="text-sm text-gray-500">Bentuk pelanggaran yang mungkin terjadi</div>
                            </div>
                        </div>
                        @if($risk->identification->violation_type)
                            <span class="px-3 py-1 rounded-full text-sm font-medium 
                                @if($risk->identification->violation_type == 'Hukum') bg-red-100 text-red-800
                                @elseif($risk->identification->violation_type == 'SOP') bg-blue-100 text-blue-800
                                @elseif($risk->identification->violation_type == 'Kontrak') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $risk->identification->violation_type }}
                            </span>
                        @else
                            <span class="text-gray-400">Belum diisi</span>
                        @endif
                    </div>
                    
                    <!-- Jenis Kegagalan -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center mr-3">
                                <i data-feather="x-circle" class="w-5 h-5 text-teal-600"></i>
                            </div>
                            <div>
                                <div class="font-medium">Jenis Kegagalan</div>
                                <div class="text-sm text-gray-500">Sumber kegagalan dalam operasional</div>
                            </div>
                        </div>
                        @if($risk->identification->failure_type)
                            <span class="px-3 py-1 rounded-full text-sm font-medium 
                                @if($risk->identification->failure_type == 'Manusia') bg-teal-100 text-teal-800
                                @elseif($risk->identification->failure_type == 'Proses') bg-indigo-100 text-indigo-800
                                @elseif($risk->identification->failure_type == 'Sistem') bg-pink-100 text-pink-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $risk->identification->failure_type }}
                            </span>
                        @else
                            <span class="text-gray-400">Belum diisi</span>
                        @endif
                    </div>
                    
                    <!-- Jenis Kesalahan -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center mr-3">
                                <i data-feather="alert-triangle" class="w-5 h-5 text-amber-600"></i>
                            </div>
                            <div>
                                <div class="font-medium">Jenis Kesalahan</div>
                                <div class="text-sm text-gray-500">Jenis kesalahan yang mungkin terjadi</div>
                            </div>
                        </div>
                        @if($risk->identification->error_type)
                            <span class="px-3 py-1 rounded-full text-sm font-medium 
                                @if($risk->identification->error_type == 'Human Error') bg-amber-100 text-amber-800
                                @elseif($risk->identification->error_type == 'Technical Error') bg-rose-100 text-rose-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $risk->identification->error_type }}
                            </span>
                        @else
                            <span class="text-gray-400">Belum diisi</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Analisis Risiko Section -->
        <div class="intro-y box mt-6">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="activity" class="w-5 h-5 mr-2"></i> Analisis Risiko
                </h2>   
                <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                    <a href="{{ route('risk-analyses.create', ['riskId' => $risk->risk_id]) }}" class="btn btn-primary btn-sm mr-2">
                        <i data-feather="plus" class="w-4 h-4 mr-1"></i> Analisis Baru
                    </a>
                    <a href="{{ route('risk-analyses.index', ['riskId' => $risk->risk_id]) }}" 
                        class="btn btn-outline-primary btn-sm">
                        <i data-feather="list" class="w-4 h-4 mr-1"></i> Lihat Semua
                    </a>
                </div>
            </div>
            <div class="p-5">
                @if($risk->analyses && $risk->analyses->count() > 0)
                    <div class="mb-4">
                        <h3 class="font-medium text-lg mb-3">Riwayat Analisis</h3>
                        <div class="overflow-x-auto">
                            <table class="table table-report -mt-2">
                                <thead>
                                    <tr>
                                        <th class="whitespace-nowrap">TANGGAL</th>
                                        <th class="whitespace-nowrap">LIKELIHOOD</th>
                                        <th class="whitespace-nowrap">IMPACT</th>
                                        <th class="whitespace-nowrap">SKOR</th>
                                        <th class="whitespace-nowrap">LEVEL</th>
                                        <th class="whitespace-nowrap text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($risk->analyses->take(5) as $analysis)
                                        <tr class="intro-x hover:bg-gray-50">
                                            <td class="font-medium">
                                                {{ \Carbon\Carbon::parse($analysis->analysis_date)->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <div class="text-xl font-bold text-blue-600">{{ $analysis->likelihood_level }}</div>
                                                    <div class="text-xs text-gray-500">/5</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <div class="text-xl font-bold text-red-600">{{ $analysis->impact_level }}</div>
                                                    <div class="text-xs text-gray-500">/5</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold">{{ $analysis->risk_score }}</div>
                                                    <div class="text-xs text-gray-500">({{ $analysis->likelihood_level }} × {{ $analysis->impact_level }})</div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $levelColors = [
                                                        'sangat_rendah' => 'bg-green-100 text-green-800',
                                                        'rendah' => 'bg-yellow-100 text-yellow-800',
                                                        'sedang' => 'bg-orange-100 text-orange-800',
                                                        'tinggi' => 'bg-red-100 text-red-800',
                                                        'sangat_tinggi' => 'bg-red-600 text-white'
                                                    ];
                                                    $levelTexts = [
                                                        'sangat_rendah' => 'Sangat Rendah',
                                                        'rendah' => 'Rendah',
                                                        'sedang' => 'Sedang',
                                                        'tinggi' => 'Tinggi',
                                                        'sangat_tinggi' => 'Sangat Tinggi'
                                                    ];
                                                    $color = $levelColors[$analysis->risk_level] ?? 'bg-gray-100 text-gray-800';
                                                    $text = $levelTexts[$analysis->risk_level] ?? '-';
                                                @endphp
                                                <span class="px-2 py-1 text-xs rounded font-medium {{ $color }}">
                                                    {{ $text }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="flex justify-center space-x-2">
                                                    <a href="{{ route('risk-analyses.show', ['riskId' => $risk->risk_id, 'analysisId' => $analysis->risk_analysis_id]) }}" 
                                                    class="btn btn-sm btn-primary">
                                                        <i data-feather="eye" class="w-4 h-4"></i>
                                                    </a>
                                                    <a href="{{ route('risk-analyses.edit', ['riskId' => $risk->risk_id, 'analysisId' => $analysis->risk_analysis_id]) }}" 
                                                    class="btn btn-sm btn-warning">
                                                        <i data-feather="edit" class="w-4 h-4"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($risk->analyses->count() > 5)
                        <div class="text-center mt-4">
                            <a href="{{ route('risk-analyses.index', ['riskId' => $risk->risk_id]) }}" class="text-primary hover:underline">
                                Lihat {{ $risk->analyses->count() - 5 }} analisis lainnya...
                            </a>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                            <i data-feather="activity" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada data analisis</h3>
                        <p class="text-gray-500 mb-4">Tambahkan analisis untuk menilai tingkat risiko</p>
                        <a href="{{ route('risk-analyses.create', ['riskId' => $risk->risk_id]) }}" 
                        class="btn btn-primary">
                            <i data-feather="plus" class="w-4 h-4 mr-2"></i> Tambah Analisis Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-span-12 xl:col-span-4">
        <!-- Timeline Risiko -->
<div class="intro-y box">
    <div class="flex items-center p-5 border-b border-gray-200">
        <h2 class="font-medium text-base mr-auto">
            <i data-feather="clock" class="w-5 h-5 mr-2"></i> Timeline Risiko
        </h2>
    </div>
    <div class="p-5">
        <div class="relative">
            <!-- Timeline Line -->
            <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-200"></div>
            
            <div class="space-y-4 relative">
                <!-- Created - Identifikasi Risiko -->
                @if($risk->identification)
                    <a href="{{ route('risk-identifications.edit') }}?riskId={{ $risk->risk_id }}" 
                       class="flex items-start p-3 bg-green-50 hover:bg-green-100 rounded-lg transition cursor-pointer group">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-4 z-10">
                            <i data-feather="search" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium flex items-center justify-between">
                                <span>Identifikasi Risiko</span>
                                <span class="text-xs text-green-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i data-feather="edit" class="w-3 h-3 mr-1"></i> Edit
                                </span>
                            </div>
                            <div class="text-sm text-gray-600">{{ $risk->created_at->format('d F Y H:i') }}</div>
                            <div class="text-xs text-gray-500 mt-1">Risiko pertama kali diidentifikasi</div>
                            
                            <!-- Detail Identifikasi -->
                            <div class="mt-2">
                                <div class="flex flex-wrap gap-1">
                                    @if($risk->identification->loss_type)
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded">
                                            <i data-feather="dollar-sign" class="w-3 h-3 inline mr-1"></i>
                                            {{ $risk->identification->loss_type }}
                                        </span>
                                    @endif
                                    @if($risk->identification->violation_type)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">
                                            <i data-feather="alert-octagon" class="w-3 h-3 inline mr-1"></i>
                                            {{ $risk->identification->violation_type }}
                                        </span>
                                    @endif
                                    @if($risk->identification->failure_type)
                                        <span class="px-2 py-1 bg-teal-100 text-teal-800 text-xs rounded">
                                            <i data-feather="x-circle" class="w-3 h-3 inline mr-1"></i>
                                            {{ $risk->identification->failure_type }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @else
                    <a href="{{ route('risk-identifications.create') }}?riskId={{ $risk->risk_id }}" 
                       class="flex items-start p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition cursor-pointer group">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-4 z-10">
                            <i data-feather="search" class="w-5 h-5 text-gray-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium flex items-center justify-between">
                                <span>Identifikasi Risiko</span>
                                <span class="text-xs text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i data-feather="plus" class="w-3 h-3 mr-1"></i> Tambah
                                </span>
                            </div>
                            <div class="text-sm text-gray-600">{{ $risk->created_at->format('d F Y H:i') }}</div>
                            <div class="text-xs text-gray-500 mt-1">Risiko pertama kali diidentifikasi</div>
                            
                            <!-- Prompt untuk identifikasi -->
                            <div class="mt-2 p-2 bg-yellow-50 border border-yellow-100 rounded">
                                <div class="text-xs text-yellow-800 flex items-center">
                                    <i data-feather="alert-circle" class="w-3 h-3 mr-1"></i>
                                    Klik untuk melakukan identifikasi risiko
                                </div>
                            </div>
                        </div>
                    </a>
                @endif
                
                <!-- First Analysis -->
                @if($risk->analyses && $risk->analyses->count() > 0)
                    @php
                        $firstAnalysis = $risk->analyses->first();
                    @endphp
                    <a href="{{ route('risk-analyses.show', ['riskId' => $risk->risk_id, 'analysisId' => $firstAnalysis->risk_analysis_id]) }}" 
                       class="flex items-start p-3 bg-orange-50 hover:bg-orange-100 rounded-lg transition cursor-pointer group">
                        <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center mr-4 z-10">
                            <i data-feather="activity" class="w-5 h-5 text-orange-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium flex items-center justify-between">
                                <span>Analisis Pertama</span>
                                <span class="text-xs text-orange-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i data-feather="eye" class="w-3 h-3 mr-1"></i> Lihat
                                </span>
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ $firstAnalysis->analysis_date ? \Carbon\Carbon::parse($firstAnalysis->analysis_date)->format('d F Y') : '-' }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Skor awal: 
                                <span class="font-bold 
                                    @if($firstAnalysis->risk_score >= 20) text-red-600
                                    @elseif($firstAnalysis->risk_score >= 15) text-orange-600
                                    @elseif($firstAnalysis->risk_score >= 10) text-yellow-600
                                    @else text-green-600
                                    @endif">
                                    {{ $firstAnalysis->risk_score }}
                                </span>
                            </div>
                        </div>
                    </a>
                @else
                    <a href="{{ route('risk-analyses.create', ['riskId' => $risk->risk_id]) }}" 
                       class="flex items-start p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition cursor-pointer group">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-4 z-10">
                            <i data-feather="activity" class="w-5 h-5 text-gray-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium flex items-center justify-between">
                                <span>Analisis Pertama</span>
                                <span class="text-xs text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i data-feather="plus" class="w-3 h-3 mr-1"></i> Tambah
                                </span>
                            </div>
                            <div class="text-sm text-gray-600">Belum ada analisis</div>
                            <div class="text-xs text-gray-500 mt-1">Klik untuk menambahkan analisis pertama</div>
                        </div>
                    </a>
                @endif
                
                <!-- Latest Analysis -->
                @if($risk->analyses && $risk->analyses->count() > 1)
                    @php
                        $latestAnalysis = $risk->analyses->last();
                    @endphp
                    <a href="{{ route('risk-analyses.show', ['riskId' => $risk->risk_id, 'analysisId' => $latestAnalysis->risk_analysis_id]) }}" 
                       class="flex items-start p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition cursor-pointer group">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-4 z-10">
                            <i data-feather="refresh-cw" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium flex items-center justify-between">
                                <span>Analisis Terbaru</span>
                                <span class="text-xs text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i data-feather="eye" class="w-3 h-3 mr-1"></i> Lihat
                                </span>
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ $latestAnalysis->analysis_date ? \Carbon\Carbon::parse($latestAnalysis->analysis_date)->format('d F Y') : '-' }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Skor terkini: 
                                <span class="font-bold 
                                    @if($latestAnalysis->risk_score >= 20) text-red-600
                                    @elseif($latestAnalysis->risk_score >= 15) text-orange-600
                                    @elseif($latestAnalysis->risk_score >= 10) text-yellow-600
                                    @else text-green-600
                                    @endif">
                                    {{ $latestAnalysis->risk_score }}
                                </span>
                            </div>
                        </div>
                    </a>
                @elseif($risk->analyses && $risk->analyses->count() === 1)
                    <a href="{{ route('risk-analyses.create', ['riskId' => $risk->risk_id]) }}" 
                       class="flex items-start p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition cursor-pointer group">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-4 z-10">
                            <i data-feather="refresh-cw" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium flex items-center justify-between">
                                <span>Update Analisis</span>
                                <span class="text-xs text-green-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i data-feather="plus" class="w-3 h-3 mr-1"></i> Update
                                </span>
                            </div>
                            <div class="text-sm text-gray-600">Update analisis terbaru</div>
                            <div class="text-xs text-gray-500 mt-1">Klik untuk update analisis risiko</div>
                        </div>
                    </a>
                @endif
            </div>
        </div>
        
        <!-- Quick Actions Horizontal -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('risk-analyses.create', ['riskId' => $risk->risk_id]) }}" 
                   class="px-4 py-2 bg-orange-100 hover:bg-orange-200 text-orange-700 rounded-lg text-sm flex items-center transition">
                    <i data-feather="activity" class="w-4 h-4 mr-2"></i> Analisis Baru
                </a>
                
                @if($risk->identification)
                    <a href="{{ route('risk-identifications.edit') }}?riskId={{ $risk->risk_id }}" 
                       class="px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-sm flex items-center transition">
                        <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit Identifikasi
                    </a>
                @else
                    <a href="{{ route('risk-identifications.create') }}?riskId={{ $risk->risk_id }}" 
                       class="px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-sm flex items-center transition">
                        <i data-feather="search" class="w-4 h-4 mr-2"></i> Identifikasi Risiko
                    </a>
                @endif
                
                <a href="{{ route('risk-analyses.index', ['riskId' => $risk->risk_id]) }}" 
                   class="px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-sm flex items-center transition">
                    <i data-feather="list" class="w-4 h-4 mr-2"></i> Riwayat Analisis
                </a>
                
                <a href="{{ route('risks.index') }}" 
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm flex items-center transition">
                    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
        
        <!-- Risk Matrix Position -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="grid" class="w-5 h-5 mr-2"></i> Posisi di Matriks Risiko
                </h2>
            </div>
            <div class="p-5">
                @if($risk->likelihood_level && $risk->impact_level)
                    <div class="risk-matrix-mini text-center">
                        <table class="w-full text-xs">
                            <thead>
                                <tr>
                                    <th class="p-1 text-center font-medium" colspan="6">Impact →</th>
                                </tr>
                                <tr>
                                    <th class="p-1 text-center font-medium">L↓</th>
                                    @for($i = 1; $i <= 5; $i++)
                                    <th class="p-1 text-center font-medium {{ $i == $risk->impact_level ? 'bg-blue-100' : '' }}">{{ $i }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @for($l = 5; $l >= 1; $l--)
                                <tr>
                                    <td class="p-1 text-center font-medium {{ $l == $risk->likelihood_level ? 'bg-blue-100' : '' }}">{{ $l }}</td>
                                    @for($i = 1; $i <= 5; $i++)
                                        @php
                                            $score = $l * $i;
                                            $level = match(true) {
                                                $score >= 20 => 'sangat_tinggi',
                                                $score >= 15 => 'tinggi',
                                                $score >= 10 => 'sedang',
                                                $score >= 5 => 'rendah',
                                                default => 'sangat_rendah'
                                            };
                                            $color = match($level) {
                                                'sangat_tinggi' => 'bg-red-600',
                                                'tinggi' => 'bg-red-400',
                                                'sedang' => 'bg-orange-400',
                                                'rendah' => 'bg-yellow-400',
                                                'sangat_rendah' => 'bg-green-400',
                                                default => 'bg-gray-400'
                                            };
                                            $isCurrent = ($l == $risk->likelihood_level && $i == $risk->impact_level);
                                        @endphp
                                        <td class="p-1 text-center border">
                                            <div class="{{ $color }} text-black rounded p-1 {{ $isCurrent ? 'relative' : '' }}">
                                                @if($isCurrent)
                                                    <!-- Titik indikator posisi risiko -->
                                                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-white border-2 border-red-500 rounded-full flex items-center justify-center">
                                                        <div class="w-1.5 h-1.5 bg-red-500 rounded-full"></div>
                                                    </div>
                                                @endif
                                                {{ $score }}
                                            </div>
                                        </td>
                                    @endfor
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                        <div class="mt-3 text-sm">
                            <div class="font-medium">Posisi Saat Ini:</div>
                            <div class="text-gray-600">
                                Likelihood: <span class="font-bold">{{ $risk->likelihood_level }}</span> • 
                                Impact: <span class="font-bold">{{ $risk->impact_level }}</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Skor: {{ $risk->risk_score ?? 0 }} • 
                                Level: {{ ucfirst(str_replace('_', ' ', $risk->risk_level ?? 'Belum dianalisis')) }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-6">
                        <i data-feather="alert-circle" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                        <p class="text-gray-500">Belum ada data analisis</p>
                        <p class="text-xs text-gray-400 mt-1">Lakukan analisis terlebih dahulu</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="bar-chart" class="w-5 h-5 mr-2"></i> Statistik Cepat
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <div class="text-xl font-bold text-blue-600">{{ $risk->analyses ? $risk->analyses->count() : 0 }}</div>
                        <div class="text-xs text-gray-600">Analisis</div>
                    </div>
                    <div class="text-center p-3 bg-purple-50 rounded-lg">
                        <div class="text-xl font-bold text-purple-600">0</div>
                        <div class="text-xs text-gray-600">Mitigasi</div>
                    </div>
                    <div class="text-center p-3 bg-teal-50 rounded-lg">
                        <div class="text-xl font-bold text-teal-600">0</div>
                        <div class="text-xs text-gray-600">Monitoring</div>
                    </div>
                    <div class="text-center p-3 bg-orange-50 rounded-lg">
                        <div class="text-xl font-bold text-orange-600">0</div>
                        <div class="text-xs text-gray-600">Evaluasi</div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="text-sm">
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600">Usia Risiko:</span>
                            <span class="font-medium">{{ floor($risk->created_at->diffInDays(now())) }} hari</span>
                        </div>
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600">Update Terakhir:</span>
                            <span class="font-medium">{{ $risk->updated_at->diffForHumans() }}</span>
                        </div>
                        @if($risk->last_analysis_date)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Analisis Terakhir:</span>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($risk->last_analysis_date)->diffForHumans() }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Analysis -->
<div class="modal" id="analysis-modal">
    <div class="modal__content">
        <div class="p-5">
            <h2 class="text-lg font-medium mb-4">Detail Analisis</h2>
            <div id="analysis-content">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="flex justify-end mt-6">
                <button onclick="closeAnalysisModal()" class="btn btn-outline-secondary w-24">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .line-clamp-2 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        line-clamp: 2;
    }
    
    .risk-matrix-mini table {
        border-collapse: separate;
        border-spacing: 1px;
    }
    
    .risk-matrix-mini th,
    .risk-matrix-mini td {
        border: 1px solid #e5e7eb;
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    
    .modal--show {
        display: flex;
    }
    
    .modal__content {
        background-color: white;
        border-radius: 0.5rem;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});

// Export risk data
function exportRiskData() {
    if (confirm('Export data risiko ini ke PDF?')) {
        // Simulate export process
        showAlert('info', 'Mempersiapkan laporan...');
        
        setTimeout(() => {
            showAlert('success', 'Laporan berhasil diexport');
            
            // Create and trigger download
            const link = document.createElement('a');
            link.href = '#'; // Replace with actual export URL
            link.download = `Risk-Report-{{ $risk->risk_code }}.pdf`;
            link.click();
        }, 2000);
    }
}

// Show alert
function showAlert(type, message) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible show flex items-center mb-2 mt-5`;
    alert.innerHTML = `
        <i data-feather="${type === 'success' ? 'check-circle' : type === 'error' ? 'alert-octagon' : 'info'}" class="w-6 h-6 mr-2"></i> 
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            <i data-feather="x" class="w-4 h-4"></i>
        </button>
    `;
    
    document.querySelector('.content > .grid > .col-span-12').insertBefore(alert, document.querySelector('.intro-y.box'));
    feather.replace();
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        alert.querySelector('.btn-close').click();
    }, 5000);
}

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('analysis-modal');
    if (e.target === modal) {
        modal.style.display = 'none';
        modal.classList.remove('modal--show');
    }
});
</script>
@endpush