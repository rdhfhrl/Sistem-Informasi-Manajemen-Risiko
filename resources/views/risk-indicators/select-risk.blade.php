@extends('layouts.master')

@section('title', 'Pilih Risiko untuk Indikator - SIMR')

@section('page-title', 'Pilih Risiko untuk Indikator')

@section('page-action')
<a href="{{ route('risk-indicators.all') }}" class="btn btn-outline-secondary shadow-md mr-2 flex items-center">
    <i data-feather="list" class="w-4 h-4 mr-2"></i> Semua Indikator
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Search Box -->
        <div class="intro-y box mb-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto flex items-center">
                    <i data-feather="search" class="w-5 h-5 mr-2 text-blue-500"></i>
                    Cari Risiko untuk Indikator
                </h2>
            </div>
            <div class="p-5">
                <form method="GET" action="{{ route('risks.index') }}" class="flex items-center">
                    <div class="relative flex-1">
                        <input type="text" 
                               name="search" 
                               class="form-control w-full pl-10" 
                               placeholder="Cari berdasarkan kode, deskripsi, atau proyek..."
                               value="{{ request('search') }}">
                        <i data-feather="search" class="w-4 h-4 absolute left-3 top-3 text-gray-500"></i>
                    </div>
                    <button type="submit" class="btn btn-primary ml-3 flex items-center">
                        <i data-feather="search" class="w-4 h-4 mr-2"></i> Cari
                    </button>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-100">
                                <i data-feather="activity" class="w-6 h-6 text-blue-600"></i>
                            </div>
                            <div class="ml-auto">
                                @php
                                    $totalIndicators = \App\Models\RiskIndicator::count();
                                @endphp
                                <div class="text-3xl font-bold leading-8">{{ $totalIndicators }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Total Indikator</div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-red-100">
                                <i data-feather="alert-triangle" class="w-6 h-6 text-red-600"></i>
                            </div>
                            <div class="ml-auto">
                                @php
                                    // PERBAIKAN: Gunakan isExceeded() method dari model
                                    $exceededCount = \App\Models\RiskIndicator::has('measurements')
                                        ->get()
                                        ->filter(function($indicator) {
                                            return $indicator->isExceeded();
                                        })
                                        ->count();
                                @endphp
                                <div class="text-3xl font-bold leading-8">{{ $exceededCount }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Melebihi Batas</div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-100">
                                <i data-feather="target" class="w-6 h-6 text-green-600"></i>
                            </div>
                            <div class="ml-auto">
                                @php
                                    $dampakCount = \App\Models\RiskIndicator::where('indicator_type', 'dampak')->count();
                                @endphp
                                <div class="text-3xl font-bold leading-8">{{ $dampakCount }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Indikator Dampak</div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-purple-100">
                                <i data-feather="alert-triangle" class="w-6 h-6 text-purple-600"></i>
                            </div>
                            <div class="ml-auto">
                                @php
                                    $highRiskWithIndicators = \App\Models\Risk::whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
                                        ->has('indicators')
                                        ->count();
                                @endphp
                                <div class="text-3xl font-bold leading-8">{{ $highRiskWithIndicators }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Risiko Tinggi dengan Indikator</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risiko dengan Indikator Aktif -->
        <div class="intro-y box mb-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto flex items-center">
                    <i data-feather="activity" class="w-5 h-5 mr-2 text-green-500"></i>
                    Risiko dengan Indikator Aktif
                </h2>
                <a href="{{ route('risk-indicators.all') }}" class="text-primary text-sm">Lihat Semua</a>
            </div>
            <div class="p-5">
                @php
                    $risksWithIndicators = \App\Models\Risk::has('indicators')
                        ->with(['project', 'organization'])
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                
                @if($risksWithIndicators->count() > 0)
                    <div class="space-y-4">
                        @foreach($risksWithIndicators as $risk)
                            <div class="flex items-center p-4 bg-white rounded-lg border hover:shadow-md transition-shadow">
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-4">
                                    <i data-feather="activity" class="w-5 h-5 text-green-600"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium">{{ $risk->risk_code }}</div>
                                    <div class="text-sm text-gray-600">{{ Str::limit($risk->risk_description, 50) }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $risk->project->project_name ?? 'Tidak ada proyek' }}
                                    </div>
                                </div>
                                <div class="ml-4 text-right">
                                    <div class="font-bold">{{ $risk->indicators()->count() }}</div>
                                    <div class="text-xs text-gray-500">Indikator</div>
                                </div>
                                <div class="ml-4">
                                    <a href="{{ route('risk-indicators.by-risk', $risk->risk_id) }}" 
                                       class="btn btn-primary btn-sm flex items-center">
                                        <i data-feather="eye" class="w-4 h-4 mr-1"></i> Lihat
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                            <i data-feather="activity" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada indikator</h3>
                        <p class="text-gray-500">Tidak ada risiko yang memiliki indikator aktif</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Daftar Risiko Terbaru -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto flex items-center">
                    <i data-feather="list" class="w-5 h-5 mr-2 text-teal-500"></i>
                    Daftar Semua Risiko
                </h2>
                <a href="{{ route('risks.index') }}" class="text-primary text-sm">Lihat Semua Risiko</a>
            </div>
            <div class="p-5">
                @php
                    $risks = \App\Models\Risk::with(['project', 'organization', 'indicators'])
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
                @endphp
                
                @if($risks->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-report">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">KODE RISIKO</th>
                                    <th class="whitespace-nowrap">DESKRIPSI</th>
                                    <th class="whitespace-nowrap">PROYEK</th>
                                    <th class="whitespace-nowrap">LEVEL</th>
                                    <th class="whitespace-nowrap">INDIKATOR</th>
                                    <th class="whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($risks as $risk)
                                    <tr class="intro-x hover:bg-gray-50">
                                        <td>
                                            <div class="font-medium">{{ $risk->risk_code }}</div>
                                        </td>
                                        <td>
                                            <div class="text-sm">{{ Str::limit($risk->risk_description, 40) }}</div>
                                        </td>
                                        <td>
                                            <div class="text-sm">{{ $risk->project->pro_nama ?? '-' }}</div>
                                        </td>
                                        <td>
                                            @if($risk->risk_level)
                                                <span class="px-2 py-1 rounded-full text-xs font-medium 
                                                    @if($risk->risk_level == 'sangat_rendah') bg-green-100 text-green-800
                                                    @elseif($risk->risk_level == 'rendah') bg-blue-100 text-blue-800
                                                    @elseif($risk->risk_level == 'sedang') bg-yellow-100 text-yellow-800
                                                    @elseif($risk->risk_level == 'tinggi') bg-orange-100 text-orange-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    @switch($risk->risk_level)
                                                        @case('sangat_rendah') SR @break
                                                        @case('rendah') R @break
                                                        @case('sedang') S @break
                                                        @case('tinggi') T @break
                                                        @case('sangat_tinggi') ST @break
                                                    @endswitch
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="font-medium">{{ $risk->indicators->count() }}</div>
                                            @if($risk->indicators->count() > 0)
                                                <div class="text-xs text-gray-500">
                                                    @php
                                                        $exceeded = $risk->indicators->where('current_value', '>', 'threshold')->count();
                                                    @endphp
                                                    @if($exceeded > 0)
                                                        <span class="text-red-500">{{ $exceeded }} melebihi batas</span>
                                                    @else
                                                        <span class="text-green-500">Semua normal</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex justify-center items-center space-x-2">
                                                <a href="{{ route('risk-indicators.by-risk', $risk->risk_id) }}" 
                                                   class="btn btn-outline-secondary btn-sm flex items-center" 
                                                   data-toggle="tooltip" 
                                                   title="Lihat indikator">
                                                    <i data-feather="eye" class="w-4 h-4"></i>
                                                </a>
                                                <a href="{{ route('risk-indicators.create', $risk->risk_id) }}" 
                                                   class="btn btn-primary btn-sm flex items-center" 
                                                   data-toggle="tooltip" 
                                                   title="Tambah indikator">
                                                    <i data-feather="plus" class="w-4 h-4"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                            <i data-feather="alert-triangle" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada risiko</h3>
                        <p class="text-gray-500 mb-4">Buat risiko terlebih dahulu sebelum menambahkan indikator</p>
                        <a href="{{ route('risks.create') }}" class="btn btn-primary flex items-center justify-center mx-auto">
                            <i data-feather="plus" class="w-4 h-4 mr-2"></i> Buat Risiko Baru
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush