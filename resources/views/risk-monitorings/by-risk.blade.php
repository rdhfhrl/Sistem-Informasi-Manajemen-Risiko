@extends('layouts.master')

@section('title', 'Pemantauan Risiko - SIMR')

@section('page-title', 'Pemantauan Risiko')

@section('breadcrumb')
@parent
<li class="breadcrumb-item"><a href="{{ route('risk-monitorings.index') }}">Pemantauan</a></li>
<li class="breadcrumb-item active">{{ $risk->risk_code }}</li>
@endsection

@section('page-action')
<a href="{{ route('risk-monitorings.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Pilih Risiko Lain
</a>
<a href="{{ route('risk-monitorings.create', $risk->risk_id) }}" class="btn btn-primary shadow-md mr-2">
    <i data-feather="plus" class="w-4 h-4 mr-2"></i> Pemantauan Baru
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
                        <div class="text-gray-600 text-sm mb-1">Skor Risiko Terakhir</div>
                        <div class="font-medium text-lg 
                            @if($risk->risk_level == 'sangat_tinggi') text-red-600
                            @elseif($risk->risk_level == 'tinggi') text-orange-600
                            @elseif($risk->risk_level == 'sedang') text-yellow-600
                            @elseif($risk->risk_level == 'rendah') text-blue-600
                            @else text-green-600
                            @endif">
                            {{ $risk->risk_score ?? 'N/A' }}
                        </div>
                    </div>
                </div>
                @if($risk->last_monitoring_date)
                <div class="mt-4 text-sm text-gray-600">
                    <i data-feather="calendar" class="w-4 h-4 inline mr-1"></i>
                    Pemantauan terakhir: {{ \Carbon\Carbon::parse($risk->last_monitoring_date)->format('d F Y') }}
                </div>
                @endif
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-teal-100">
                                <i data-feather="eye" class="w-6 h-6 text-teal-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <div class="text-3xl font-bold leading-8">{{ $monitorings->total() }}</div>
                            <div class="text-base text-gray-600 mt-1">Total Pemantauan</div>
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
                                $highRiskCount = collect($monitorings->items())->whereIn('current_risk_level', ['tinggi', 'sangat_tinggi'])->count();
                            @endphp
                            <div class="text-3xl font-bold leading-8">{{ $highRiskCount }}</div>
                            <div class="text-base text-gray-600 mt-1">Pemantauan Risiko Tinggi</div>
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
                                $avgEffectiveness = collect($monitorings->items())
                                    ->filter(function($item) {
                                        return $item->effectiveness_rating !== null;
                                    })
                                    ->avg('effectiveness_rating');
                            @endphp
                            <div class="text-3xl font-bold leading-8">{{ $avgEffectiveness ? round($avgEffectiveness, 1) : 'N/A' }}</div>
                            <div class="text-base text-gray-600 mt-1">Rata-rata Efektivitas</div>
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
                                $trendData = collect($monitorings->items());
                                if ($trendData->count() >= 2) {
                                    $firstScore = $trendData->last()->current_risk_score;
                                    $lastScore = $trendData->first()->current_risk_score;
                                    $trend = $lastScore < $firstScore ? 'down' : ($lastScore > $firstScore ? 'up' : 'stable');
                                } else {
                                    $trend = 'stable';
                                }
                            @endphp
                            <div class="text-3xl font-bold leading-8">
                                @if($trend == 'down') ↓
                                @elseif($trend == 'up') ↑
                                @else ➔
                                @endif
                            </div>
                            <div class="text-base text-gray-600 mt-1">
                                @if($trend == 'down') Menurun
                                @elseif($trend == 'up') Meningkat
                                @else Stabil
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Riwayat Pemantauan Risiko
                    <span class="text-gray-500 text-sm ml-2">({{ $monitorings->total() }} data)</span>
                </h2>
                
                @if($monitorings->count() > 0)
                <div class="flex items-center space-x-2 mt-3 sm:mt-0">
                    <span class="text-sm text-gray-600">Filter:</span>
                    <select id="filter-risk-level" class="form-select w-40">
                        <option value="">Semua Level Risiko</option>
                        <option value="sangat_tinggi">Sangat Tinggi</option>
                        <option value="tinggi">Tinggi</option>
                        <option value="sedang">Sedang</option>
                        <option value="rendah">Rendah</option>
                        <option value="sangat_rendah">Sangat Rendah</option>
                    </select>
                </div>
                @endif
            </div>
            <div class="p-5">
                @if($monitorings->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-report -mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">TANGGAL PEMANTAUAN</th>
                                    <th class="whitespace-nowrap">SKOR RISIKO</th>
                                    <th class="whitespace-nowrap">LEVEL RISIKO</th>
                                    <th class="whitespace-nowrap">EFEKTIVITAS</th>
                                    <th class="whitespace-nowrap">PEMANTAU</th>
                                    <th class="whitespace-nowrap">HASIL PEMANTAUAN</th>
                                    <th class="whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monitorings as $monitoring)
                                    <tr class="intro-x hover:bg-gray-50" data-risk-level="{{ $monitoring->current_risk_level }}">
                                        <td>
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                                    <i data-feather="calendar" class="w-5 h-5 text-gray-600"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium">
                                                        {{ \Carbon\Carbon::parse($monitoring->monitoring_date)->format('d M Y') }}
                                                    </div>
                                                    <div class="text-gray-500 text-xs mt-0.5">
                                                        @if($monitoring->is_latest)
                                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Pemantauan Terbaru</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="text-2xl font-bold 
                                                @if($monitoring->current_risk_level == 'sangat_tinggi') text-red-600
                                                @elseif($monitoring->current_risk_level == 'tinggi') text-orange-600
                                                @elseif($monitoring->current_risk_level == 'sedang') text-yellow-600
                                                @elseif($monitoring->current_risk_level == 'rendah') text-blue-600
                                                @else text-green-600
                                                @endif">
                                                {{ number_format($monitoring->current_risk_score, 2) }}
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <span class="px-3 py-1 rounded-full text-xs font-medium 
                                                @if($monitoring->current_risk_level == 'sangat_rendah') bg-green-100 text-green-800
                                                @elseif($monitoring->current_risk_level == 'rendah') bg-blue-100 text-blue-800
                                                @elseif($monitoring->current_risk_level == 'sedang') bg-yellow-100 text-yellow-800
                                                @elseif($monitoring->current_risk_level == 'tinggi') bg-orange-100 text-orange-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                @switch($monitoring->current_risk_level)
                                                    @case('sangat_rendah') Sangat Rendah @break
                                                    @case('rendah') Rendah @break
                                                    @case('sedang') Sedang @break
                                                    @case('tinggi') Tinggi @break
                                                    @case('sangat_tinggi') Sangat Tinggi @break
                                                @endswitch
                                            </span>
                                        </td>
                                        
                                        <td>
                                            @if($monitoring->effectiveness_rating)
                                                <div class="flex items-center">
                                                    <div class="mr-2 text-lg">{{ $monitoring->effectiveness_rating }}</div>
                                                    <div class="w-20 bg-gray-200 rounded-full h-2">
                                                        <div class="bg-teal-500 h-2 rounded-full" 
                                                             style="width: {{ ($monitoring->effectiveness_rating / 5) * 100 }}%"></div>
                                                    </div>
                                                </div>
                                                <div class="text-gray-500 text-xs mt-1">
                                                    {{ $monitoring->effectiveness_label }}
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-2">
                                                    <i data-feather="user-check" class="w-4 h-4 text-purple-600"></i>
                                                </div>
                                                <div class="font-medium">{{ $monitoring->monitored_by }}</div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="max-w-xs">
                                                <div class="font-medium text-gray-800">
                                                    {{ Str::limit($monitoring->monitoring_result, 40) }}
                                                </div>
                                                <div class="text-gray-500 text-xs mt-1">
                                                    {{ Str::limit($monitoring->monitoring_result, 60) }}
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td class="table-report__action w-56">
                                            <div class="flex justify-center items-center">
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('risk-monitorings.show', [$risk->risk_id, $monitoring->risk_monitoring_id]) }}">
                                                    <i data-feather="eye" class="w-4 h-4 mr-1"></i> Detail
                                                </a>
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('risk-monitorings.edit', [$risk->risk_id, $monitoring->risk_monitoring_id]) }}">
                                                    <i data-feather="edit" class="w-4 h-4 mr-1"></i> Edit
                                                </a>
                                                <form action="{{ route('risk-monitorings.destroy', [$risk->risk_id, $monitoring->risk_monitoring_id]) }}" 
                                                      method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pemantauan ini?')">
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
                    @if($monitorings->hasPages())
                    <div class="flex flex-col sm:flex-row items-center p-5 border-t border-gray-200">
                        <div class="text-sm text-gray-500">
                            Menampilkan {{ $monitorings->firstItem() }} - {{ $monitorings->lastItem() }} dari {{ $monitorings->total() }} pemantauan
                        </div>
                        <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                            {{ $monitorings->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="eye" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada pemantauan risiko</h3>
                        <p class="text-gray-500 mb-6">Pemantauan risiko akan muncul setelah Anda melakukan pemantauan untuk risiko ini</p>
                        <a href="{{ route('risk-monitorings.create', $risk->risk_id) }}" 
                           class="btn btn-primary">
                            <i data-feather="plus" class="w-4 h-4 mr-2"></i> Lakukan Pemantauan Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Risk Trend Chart -->
        @if($monitorings->count() > 0)
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="trending-up" class="w-5 h-5 mr-2"></i> Trend Pemantauan Risiko
                </h2>
                <button onclick="loadTrendChart()" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i> Refresh
                </button>
            </div>
            <div class="p-5">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <canvas id="riskTrendChart" height="150"></canvas>
                </div>
                
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Trend Analysis -->
                    <div>
                        <h4 class="font-medium mb-4">Analisis Trend</h4>
                        <div class="space-y-3">
                            @php
                                $trendData = collect($monitorings->items());
                                $firstMonitoring = $trendData->last();
                                $lastMonitoring = $trendData->first();
                                $scoreChange = $lastMonitoring->current_risk_score - $firstMonitoring->current_risk_score;
                                $percentageChange = $firstMonitoring->current_risk_score > 0 ? 
                                    ($scoreChange / $firstMonitoring->current_risk_score) * 100 : 0;
                            @endphp
                            
                            <div class="bg-white p-4 rounded-lg border">
                                <div class="flex justify-between items-center mb-2">
                                    <div class="text-gray-600">Skor Awal</div>
                                    <div class="font-bold">{{ number_format($firstMonitoring->current_risk_score, 2) }}</div>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <div class="text-gray-600">Skor Terakhir</div>
                                    <div class="font-bold">{{ number_format($lastMonitoring->current_risk_score, 2) }}</div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <div class="text-gray-600">Perubahan</div>
                                    <div class="font-bold {{ $scoreChange > 0 ? 'text-red-600' : ($scoreChange < 0 ? 'text-green-600' : 'text-gray-600') }}">
                                        @if($scoreChange > 0)
                                            +{{ number_format($scoreChange, 2) }} (↑ {{ round($percentageChange, 1) }}%)
                                        @elseif($scoreChange < 0)
                                            {{ number_format($scoreChange, 2) }} (↓ {{ abs(round($percentageChange, 1)) }}%)
                                        @else
                                            0 (Stabil)
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Effectiveness Summary -->
                            <div class="bg-white p-4 rounded-lg border">
                                <h5 class="font-medium mb-3">Rata-rata Efektivitas Mitigasi</h5>
                                @php
                                    $avgEffectiveness = $trendData->avg('effectiveness_rating');
                                    $effectivenessLabel = match(true) {
                                        $avgEffectiveness >= 4 => 'Efektif',
                                        $avgEffectiveness >= 3 => 'Cukup Efektif',
                                        $avgEffectiveness >= 2 => 'Tidak Efektif',
                                        default => 'Sangat Tidak Efektif'
                                    };
                                @endphp
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-teal-500 h-2 rounded-full" 
                                             style="width: {{ ($avgEffectiveness / 5) * 100 }}%"></div>
                                    </div>
                                    <div>
                                        <span class="font-bold">{{ round($avgEffectiveness, 1) }}/5</span>
                                        <span class="text-gray-600 ml-2">({{ $effectivenessLabel }})</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Latest Monitoring Details -->
                    <div>
                        <h4 class="font-medium mb-4">Detail Pemantauan Terbaru</h4>
                        @php
                            $latestMonitoring = $monitorings->first();
                        @endphp
                        <div class="space-y-3">
                            <div class="bg-white p-4 rounded-lg border">
                                <div class="text-gray-600 text-sm mb-2">Tanggal Pemantauan</div>
                                <div class="font-medium">{{ $latestMonitoring->monitoring_date->format('d F Y') }}</div>
                            </div>
                            
                            <div class="bg-white p-4 rounded-lg border">
                                <div class="text-gray-600 text-sm mb-2">Pemantau</div>
                                <div class="font-medium">{{ $latestMonitoring->monitored_by }}</div>
                            </div>
                            
                            @if($latestMonitoring->monitoring_result)
                            <div class="bg-white p-4 rounded-lg border">
                                <div class="text-gray-600 text-sm mb-2">Hasil Pemantauan</div>
                                <div class="font-medium">{{ Str::limit($latestMonitoring->monitoring_result, 100) }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Monitoring Schedule -->
        @if($monitorings->count() > 0)
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="calendar" class="w-5 h-5 mr-2"></i> Jadwal Pemantauan Selanjutnya
                </h2>
            </div>
            <div class="p-5">
                @php
                    $latestMonitoring = $monitorings->first();
                    $nextMonitoringDate = $latestMonitoring->next_monitoring_date;
                @endphp
                
                @if($nextMonitoringDate)
                    @php
                        $daysUntilNext = \Carbon\Carbon::parse($nextMonitoringDate)->diffInDays(now());
                        $isOverdue = \Carbon\Carbon::parse($nextMonitoringDate)->lt(now());
                    @endphp
                    
                    <div class="bg-gradient-to-r {{ $isOverdue ? 'from-red-500 to-red-600' : 'from-teal-500 to-teal-600' }} text-white p-6 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold mb-1">
                                    @if($isOverdue)
                                        <i data-feather="alert-triangle" class="w-5 h-5 inline mr-2"></i>
                                        JADWAL PEMANTAUAN TERLEWAT
                                    @else
                                        <i data-feather="calendar" class="w-5 h-5 inline mr-2"></i>
                                        JADWAL PEMANTAUAN BERIKUTNYA
                                    @endif
                                </h3>
                                <p class="text-white/90">
                                    {{ \Carbon\Carbon::parse($nextMonitoringDate)->format('d F Y') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold">
                                    @if($isOverdue)
                                        {{ abs($daysUntilNext) }} hari
                                    @else
                                        {{ $daysUntilNext }} hari
                                    @endif
                                </div>
                                <div class="text-sm">
                                    @if($isOverdue)
                                        Terlambat
                                    @else
                                        Mendatang
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-6">
                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                            <i data-feather="calendar" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada jadwal pemantauan selanjutnya</h3>
                        <p class="text-gray-500 mb-4">Tambahkan jadwal pemantauan selanjutnya pada form pemantauan</p>
                        <a href="{{ route('risk-monitorings.edit', [$risk->risk_id, $latestMonitoring->risk_monitoring_id]) }}" class="btn btn-primary">
                            <i data-feather="edit" class="w-4 h-4 mr-2"></i> Tambah Jadwal
                        </a>
                    </div>
                @endif
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
    const filterSelect = document.getElementById('filter-risk-level');
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
    
    // Render trend chart
    function renderTrendChart(trendData) {
        const ctx = document.getElementById('riskTrendChart');
        if (!ctx) return;
        
        const labels = trendData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        });
        
        const scores = trendData.map(item => item.score);
        const types = trendData.map(item => item.type);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Skor Risiko',
                    data: scores,
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: function(context) {
                        const index = context.dataIndex;
                        return types[index] === 'analysis' ? '#3B82F6' : '#EF4444';
                    },
                    pointRadius: function(context) {
                        const index = context.dataIndex;
                        return types[index] === 'analysis' ? 6 : 4;
                    },
                    pointHoverRadius: function(context) {
                        const index = context.dataIndex;
                        return types[index] === 'analysis' ? 8 : 6;
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                const type = types[index] === 'analysis' ? 'Analisis' : 'Pemantauan';
                                return `${type}: ${context.parsed.y}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 1,
                        max: 25,
                        title: {
                            display: true,
                            text: 'Skor Risiko'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tanggal'
                        }
                    }
                }
            }
        });
    }
    
    // Load trend chart on page load if there's data
    @if($monitorings->count() > 0)
        loadTrendChart();
    @endif
});
</script>
@endpush