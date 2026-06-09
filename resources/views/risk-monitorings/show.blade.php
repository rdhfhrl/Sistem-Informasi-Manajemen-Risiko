@extends('layouts.master')

@section('title', 'Detail Pemantauan Risiko - SIMR')

@section('page-title', 'Detail Pemantauan Risiko')

@section('breadcrumb')
@parent
<li class="breadcrumb-item"><a href="{{ route('risk-monitorings.index') }}">Pemantauan</a></li>
<li class="breadcrumb-item"><a href="{{ route('risk-monitorings.by-risk', $risk->risk_id) }}">{{ $risk->risk_code }}</a></li>
<li class="breadcrumb-item active">Detail</li>
@endsection

@section('page-action')
<a href="{{ route('risk-monitorings.by-risk', $risk->risk_id) }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
</a>
<div class="flex items-center space-x-2">
    <a href="{{ route('risk-monitorings.edit', [$risk->risk_id, $monitoring->risk_monitoring_id]) }}" 
       class="btn btn-primary shadow-md">
        <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit
    </a>
    <form action="{{ route('risk-monitorings.destroy', [$risk->risk_id, $monitoring->risk_monitoring_id]) }}" 
          method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pemantauan ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger shadow-md">
            <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus
        </button>
    </form>
</div>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Header Card -->
        <div class="intro-y box mb-6">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <div class="flex-1">
                    <h2 class="text-lg font-bold">
                        <i data-feather="eye" class="w-5 h-5 inline mr-2 text-blue-500"></i>
                        Detail Pemantauan Risiko
                    </h2>
                    <div class="text-gray-600 text-sm mt-1">
                        Kode Risiko: <span class="font-medium">{{ $risk->risk_code }}</span> • 
                        Tanggal: <span class="font-medium">{{ $monitoring->monitoring_date->format('d F Y') }}</span>
                    </div>
                </div>
                <div class="mt-3 sm:mt-0">
                    <span class="px-4 py-2 rounded-full text-sm font-medium 
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
                </div>
            </div>
            
            <!-- Risk Score Badge -->
            <div class="p-5 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                <div class="text-center">
                    <div class="text-sm text-gray-600 mb-2">SKOR RISIKO HASIL PEMANTAUAN</div>
                    <div class="text-5xl font-bold 
                        @if($monitoring->current_risk_level == 'sangat_tinggi') text-red-600
                        @elseif($monitoring->current_risk_level == 'tinggi') text-orange-600
                        @elseif($monitoring->current_risk_level == 'sedang') text-yellow-600
                        @elseif($monitoring->current_risk_level == 'rendah') text-blue-600
                        @else text-green-600
                        @endif">
                        {{ number_format($monitoring->current_risk_score, 2) }}
                    </div>
                    <div class="text-gray-500 mt-2">Skala 1-25</div>
                </div>
            </div>
        </div>

        <!-- Main Information Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Monitoring Details -->
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="info" class="w-5 h-5 mr-2 text-teal-500"></i>
                            Informasi Pemantauan
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="form-label">Tanggal Pemantauan</label>
                                <div class="font-medium text-lg">
                                    {{ $monitoring->monitoring_date->format('d F Y') }}
                                </div>
                            </div>
                            
                            <div>
                                <label class="form-label">Nama Pemantau</label>
                                <div class="font-medium text-lg flex items-center">
                                    <i data-feather="user-check" class="w-4 h-4 mr-2 text-purple-500"></i>
                                    {{ $monitoring->monitored_by }}
                                </div>
                            </div>
                            
                            @if($monitoring->effectiveness_rating)
                            <div>
                                <label class="form-label">Penilaian Efektivitas</label>
                                <div class="flex items-center">
                                    <div class="text-2xl font-bold mr-3">{{ $monitoring->effectiveness_rating }}/5</div>
                                    <div class="flex-1">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-teal-500 h-2 rounded-full" 
                                                 style="width: {{ ($monitoring->effectiveness_rating / 5) * 100 }}%"></div>
                                        </div>
                                        <div class="text-gray-500 text-xs mt-1">
                                            {{ $monitoring->effectiveness_label }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($monitoring->next_monitoring_date)
                            <div>
                                <label class="form-label">Jadwal Pemantauan Selanjutnya</label>
                                <div class="font-medium text-lg">
                                    {{ \Carbon\Carbon::parse($monitoring->next_monitoring_date)->format('d F Y') }}
                                </div>
                                @php
                                    $daysUntilNext = \Carbon\Carbon::parse($monitoring->next_monitoring_date)->diffInDays(now());
                                    $isOverdue = \Carbon\Carbon::parse($monitoring->next_monitoring_date)->lt(now());
                                @endphp
                                <div class="text-sm {{ $isOverdue ? 'text-red-500' : 'text-gray-500' }}">
                                    @if($isOverdue)
                                        <i data-feather="alert-triangle" class="w-4 h-4 inline mr-1"></i>
                                        Terlambat {{ abs($daysUntilNext) }} hari
                                    @else
                                        <i data-feather="calendar" class="w-4 h-4 inline mr-1"></i>
                                        {{ $daysUntilNext }} hari lagi
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Monitoring Result -->
                @if($monitoring->monitoring_result)
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="check-square" class="w-5 h-5 mr-2 text-green-500"></i>
                            Hasil Pemantauan
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="prose max-w-none">
                            {!! nl2br(e($monitoring->monitoring_result)) !!}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Monitoring Report -->
                @if($monitoring->monitoring_report)
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="file-text" class="w-5 h-5 mr-2 text-blue-500"></i>
                            Laporan Pemantauan
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="prose max-w-none">
                            {!! nl2br(e($monitoring->monitoring_report)) !!}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Recommendations -->
                @if($monitoring->recommendations)
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="clipboard" class="w-5 h-5 mr-2 text-orange-500"></i>
                            Rekomendasi
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="prose max-w-none">
                            {!! nl2br(e($monitoring->recommendations)) !!}
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Risk Info -->
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="alert-triangle" class="w-5 h-5 mr-2 text-red-500"></i>
                            Informasi Risiko
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="space-y-4">
                            <div>
                                <label class="form-label">Kode Risiko</label>
                                <div class="font-medium">{{ $risk->risk_code }}</div>
                            </div>
                            
                            <div>
                                <label class="form-label">Deskripsi Risiko</label>
                                <div class="font-medium">{{ Str::limit($risk->risk_description, 60) }}</div>
                            </div>
                            
                            <div>
                                <label class="form-label">Level Risiko Sebelumnya</label>
                                <div class="font-medium">
                                    @if($risk->risk_level)
                                        <span class="px-2 py-1 rounded-full text-xs font-medium 
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
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div>
                                <label class="form-label">Perubahan Level</label>
                                @php
                                    $levelChanged = $monitoring->current_risk_level != $risk->risk_level;
                                @endphp
                                <div class="flex items-center">
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
                                        <i data-feather="arrow-right" class="w-4 h-4 mx-2 text-gray-400"></i>
                                    @endif
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        @if($monitoring->current_risk_level == 'sangat_rendah') bg-green-100 text-green-800
                                        @elseif($monitoring->current_risk_level == 'rendah') bg-blue-100 text-blue-800
                                        @elseif($monitoring->current_risk_level == 'sedang') bg-yellow-100 text-yellow-800
                                        @elseif($monitoring->current_risk_level == 'tinggi') bg-orange-100 text-orange-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        @switch($monitoring->current_risk_level)
                                            @case('sangat_rendah') SR @break
                                            @case('rendah') R @break
                                            @case('sedang') S @break
                                            @case('tinggi') T @break
                                            @case('sangat_tinggi') ST @break
                                        @endswitch
                                    </span>
                                </div>
                                @if($levelChanged)
                                    <div class="text-xs mt-1 {{ 
                                        $monitoring->current_risk_score > $risk->risk_score ? 'text-red-500' : 
                                        ($monitoring->current_risk_score < $risk->risk_score ? 'text-green-500' : 'text-gray-500') 
                                    }}">
                                        <i data-feather="{{ 
                                            $monitoring->current_risk_score > $risk->risk_score ? 'trending-up' : 
                                            ($monitoring->current_risk_score < $risk->risk_score ? 'trending-down' : 'minus') 
                                        }}" class="w-3 h-3 inline mr-1"></i>
                                        @if($monitoring->current_risk_score > $risk->risk_score)
                                            Meningkat
                                        @elseif($monitoring->current_risk_score < $risk->risk_score)
                                            Menurun
                                        @else
                                            Stabil
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        @if($risk && $risk->risk_id)
                        <a href="{{ route('risks.show', $risk->risk_id) }}" 
                           class="btn btn-outline-primary w-full mt-6">
                            <i data-feather="external-link" class="w-4 h-4 mr-2"></i> Lihat Risiko
                        </a>
                        @endif
                    </div>
                </div>

                <!-- System Information -->
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="database" class="w-5 h-5 mr-2 text-gray-500"></i>
                            Informasi Sistem
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="space-y-3">
                            <div>
                                <label class="form-label">ID Pemantauan</label>
                                <div class="font-mono text-sm text-gray-600">{{ $monitoring->risk_monitoring_id }}</div>
                            </div>
                            
                            <div>
                                <label class="form-label">Dibuat Pada</label>
                                <div class="text-sm text-gray-600">
                                    {{ $monitoring->created_at->format('d M Y H:i') }}
                                </div>
                            </div>
                            
                            <div>
                                <label class="form-label">Diperbarui Pada</label>
                                <div class="text-sm text-gray-600">
                                    {{ $monitoring->updated_at->format('d M Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons Footer -->
        <div class="intro-y box mt-6">
            <div class="flex flex-col sm:flex-row items-center justify-center p-5">
                <div class="flex space-x-3">
                    <a href="{{ route('risk-monitorings.edit', [$risk->risk_id, $monitoring->risk_monitoring_id]) }}" 
                       class="btn btn-primary">
                        <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit Pemantauan
                    </a>
                    
                    <form action="{{ route('risk-monitorings.destroy', [$risk->risk_id, $monitoring->risk_monitoring_id]) }}" 
                          method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pemantauan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus
                        </button>
                    </form>
                    
                    <a href="{{ route('risk-monitorings.by-risk', $risk->risk_id) }}" 
                       class="btn btn-outline-secondary">
                        <i data-feather="list" class="w-4 h-4 mr-2"></i> Daftar Pemantauan
                    </a>
                </div>
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