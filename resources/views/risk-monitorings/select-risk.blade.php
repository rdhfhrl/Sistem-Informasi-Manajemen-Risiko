@extends('layouts.master')

@section('title', 'Pilih Risiko untuk Pemantauan - SIMR')

@section('page-title', 'Pilih Risiko untuk Pemantauan')

@section('page-action')
<a href="{{ route('risk-monitorings.all') }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="list" class="w-4 h-4 mr-2"></i> Semua Pemantauan
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Search Box -->
        <div class="intro-y box mb-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="search" class="w-5 h-5 mr-2 text-blue-500"></i>
                    Cari Risiko untuk Dipantau
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
                        <i data-feather="search" class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                    </div>
                    <button type="submit" class="btn btn-primary ml-3">
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
                                <i data-feather="alert-triangle" class="w-6 h-6 text-blue-600"></i>
                            </div>
                            <div class="ml-auto">
                                @php
                                    $totalRisks = \App\Models\Risk::count();
                                @endphp
                                <div class="text-3xl font-bold leading-8">{{ $totalRisks }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Total Risiko</div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-red-100">
                                <i data-feather="trending-up" class="w-6 h-6 text-red-600"></i>
                            </div>
                            <div class="ml-auto">
                                @php
                                    $highRiskCount = \App\Models\Risk::whereIn('risk_level', ['tinggi', 'sangat_tinggi'])->count();
                                @endphp
                                <div class="text-3xl font-bold leading-8">{{ $highRiskCount }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Risiko Tinggi</div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-yellow-100">
                                <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                            </div>
                            <div class="ml-auto">
                                @php
                                    $thirtyDaysAgo = \Carbon\Carbon::now()->subDays(30);
                                    $dueForMonitoring = \App\Models\Risk::where(function($query) use ($thirtyDaysAgo) {
                                        $query->whereNull('last_monitoring_date')
                                              ->orWhere('last_monitoring_date', '<', $thirtyDaysAgo);
                                    })
                                    ->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
                                    ->count();
                                @endphp
                                <div class="text-3xl font-bold leading-8">{{ $dueForMonitoring }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Perlu Pemantauan</div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-100">
                                <i data-feather="eye" class="w-6 h-6 text-green-600"></i>
                            </div>
                            <div class="ml-auto">
                                @php
                                    $totalMonitorings = \App\Models\RiskMonitoring::count();
                                @endphp
                                <div class="text-3xl font-bold leading-8">{{ $totalMonitorings }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Total Pemantauan</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risiko yang Perlu Pemantauan -->
        <div class="intro-y box mb-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="alert-circle" class="w-5 h-5 mr-2 text-orange-500"></i>
                    Risiko yang Perlu Pemantauan Segera
                </h2>
                <a href="javascript:;" class="text-primary text-sm">Lihat Semua</a>
            </div>
            <div class="p-5">
                @php
                    $risksDue = \App\Models\Risk::with(['organization', 'project'])
                        ->where(function($query) use ($thirtyDaysAgo) {
                            $query->whereNull('last_monitoring_date')
                                  ->orWhere('last_monitoring_date', '<', $thirtyDaysAgo);
                        })
                        ->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
                        ->orderBy('last_monitoring_date', 'asc')
                        ->limit(5)
                        ->get();
                @endphp
                
                @if($risksDue->count() > 0)
                    <div class="space-y-4">
                        @foreach($risksDue as $risk)
                            <div class="flex items-center p-4 bg-orange-50 rounded-lg border border-orange-100">
                                <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center mr-4">
                                    <i data-feather="alert-triangle" class="w-5 h-5 text-orange-600"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium">{{ $risk->risk_code }}</div>
                                    <div class="text-sm text-gray-600">{{ Str::limit($risk->risk_description, 50) }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        @if($risk->last_monitoring_date)
                                            Terakhir dipantau: {{ \Carbon\Carbon::parse($risk->last_monitoring_date)->diffForHumans() }}
                                        @else
                                            Belum pernah dipantau
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        @switch($risk->risk_level)
                                            @case('tinggi') Tinggi @break
                                            @case('sangat_tinggi') Sangat Tinggi @break
                                        @endswitch
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <a href="{{ route('risk-monitorings.create', $risk->risk_id) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i data-feather="plus" class="w-3 h-3 mr-1"></i> Pantau
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                            <i data-feather="check-circle" class="w-8 h-8 text-green-600"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Semua risiko sudah dipantau</h3>
                        <p class="text-gray-500">Tidak ada risiko yang memerlukan pemantauan segera</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Daftar Risiko Terbaru -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="list" class="w-5 h-5 mr-2 text-teal-500"></i>
                    Daftar Semua Risiko
                </h2>
                <a href="{{ route('risks.index') }}" class="text-primary text-sm">Lihat Semua Risiko</a>
            </div>
            <div class="p-5">
                @php
                    $risks = \App\Models\Risk::with(['organization', 'project'])
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
                                    <th class="whitespace-nowrap">PEMANTAUAN TERAKHIR</th>
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
                                            @if($risk->last_monitoring_date)
                                                <div class="text-sm">{{ \Carbon\Carbon::parse($risk->last_monitoring_date)->format('d M Y') }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ \Carbon\Carbon::parse($risk->last_monitoring_date)->diffForHumans() }}
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-sm">Belum pernah</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex justify-center items-center space-x-2">
                                                <a href="{{ route('risk-monitorings.by-risk', $risk->risk_id) }}" 
                                                   class="btn btn-outline-secondary btn-sm" 
                                                   data-toggle="tooltip" 
                                                   title="Lihat pemantauan">
                                                    <i data-feather="eye" class="w-3 h-3"></i>
                                                </a>
                                                <a href="{{ route('risk-monitorings.create', $risk->risk_id) }}" 
                                                   class="btn btn-primary btn-sm" 
                                                   data-toggle="tooltip" 
                                                   title="Tambah pemantauan">
                                                    <i data-feather="plus" class="w-3 h-3"></i>
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
                        <p class="text-gray-500 mb-4">Buat risiko terlebih dahulu sebelum melakukan pemantauan</p>
                        <a href="{{ route('risks.create') }}" class="btn btn-primary">
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