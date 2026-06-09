@extends('layouts.master')

@section('title', 'Detail Analisis Risiko - SIMR')

@section('page-title', 'Detail Analisis Risiko')

@section('page-action')
<a href="{{ route('risk-analyses.index', $risk->risk_id) }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Daftar
</a>
<a href="{{ route('risk-analyses.edit', [$risk->risk_id, $analysis->risk_analysis_id]) }}" class="btn btn-primary shadow-md mr-2">
    <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit Analisis
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 lg:col-span-8">
        <!-- Analysis Details Card -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="bar-chart" class="w-5 h-5 mr-2"></i>
                    Detail Analisis Risiko
                </h2>
                @if($analysis->isLatestAnalysis())
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                    <i data-feather="star" class="w-3 h-3 mr-1"></i> Analisis Terbaru
                </span>
                @endif
            </div>
            <div class="p-5">
                <!-- Risk Info -->
                <div class="mb-8">
                    <h4 class="font-medium text-gray-700 mb-4">Informasi Risiko</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-gray-600 text-sm mb-1">Kode Risiko</div>
                            <div class="font-medium text-lg">{{ $risk->risk_code }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-gray-600 text-sm mb-1">Deskripsi</div>
                            <div class="font-medium text-lg">{{ $risk->risk_description }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-gray-600 text-sm mb-1">Tanggal Analisis</div>
                            <div class="font-medium text-lg">{{ $analysis->analysis_date->format('d F Y') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Risk Score Summary -->
                <div class="mb-8">
                    <h4 class="font-medium text-gray-700 mb-4">Ringkasan Skor Risiko</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 p-6 rounded-lg text-center">
                            <div class="text-gray-600 text-sm mb-2">Skor Risiko</div>
                            <div class="text-5xl font-bold 
                                @if($analysis->risk_level == 'sangat_tinggi') text-red-600
                                @elseif($analysis->risk_level == 'tinggi') text-orange-600
                                @elseif($analysis->risk_level == 'sedang') text-yellow-600
                                @elseif($analysis->risk_level == 'rendah') text-blue-600
                                @else text-green-600
                                @endif">
                                {{ $analysis->risk_score }}
                            </div>
                            <div class="text-gray-500 text-sm mt-2">
                                ({{ $analysis->likelihood_level }} × {{ $analysis->impact_level }})
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-6 rounded-lg text-center">
                            <div class="text-gray-600 text-sm mb-2">Level Risiko</div>
                            <div class="flex flex-col items-center">
                                <span class="px-4 py-2 rounded-full text-lg font-bold mb-2
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
                                <div class="text-gray-500 text-sm">
                                    {{ $analysis->risk_matrix_position }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-6 rounded-lg text-center">
                            <div class="text-gray-600 text-sm mb-2">Status Analisis</div>
                            <div class="flex flex-col items-center">
                                @if($analysis->isLatestAnalysis())
                                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mb-2">
                                        <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                                    </div>
                                    <div class="font-medium text-green-600">Analisis Terkini</div>
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-2">
                                        <i data-feather="clock" class="w-6 h-6 text-gray-600"></i>
                                    </div>
                                    <div class="font-medium text-gray-600">Analisis Historis</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Likelihood and Impact Details -->
                <div class="mb-8">
                    <h4 class="font-medium text-gray-700 mb-4">Detail Penilaian</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Likelihood -->
                        <div class="bg-blue-50 p-5 rounded-lg">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <i data-feather="trending-up" class="w-5 h-5 text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="font-medium">Kemungkinan Terjadi (Likelihood)</div>
                                    <div class="text-blue-600 text-sm">Skala 1-5</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="flex justify-between items-center mb-1">
                                    <div class="text-gray-700">Level: {{ $analysis->likelihood_level }}/5</div>
                                    <div class="text-gray-700 font-medium">
                                        @php
                                            $likelihoodText = [
                                                1 => 'Sangat Rendah',
                                                2 => 'Rendah',
                                                3 => 'Sedang',
                                                4 => 'Tinggi',
                                                5 => 'Sangat Tinggi'
                                            ][$analysis->likelihood_level];
                                        @endphp
                                        {{ $likelihoodText }}
                                    </div>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-blue-500 h-3 rounded-full" 
                                         style="width: {{ ($analysis->likelihood_level / 5) * 100 }}%"></div>
                                </div>
                            </div>
                            
                            <div class="text-sm text-gray-600 bg-white p-3 rounded">
                                <div class="font-medium mb-1">Deskripsi:</div>
                                @php
                                    $likelihoodDesc = [
                                        1 => 'Jarang sekali terjadi, kemungkinan terjadi sangat kecil',
                                        2 => 'Jarang terjadi, kemungkinan terjadi kecil',
                                        3 => 'Mungkin terjadi dengan frekuensi yang wajar',
                                        4 => 'Sering terjadi, kemungkinan tinggi',
                                        5 => 'Hampir pasti terjadi, kemungkinan sangat tinggi'
                                    ][$analysis->likelihood_level];
                                @endphp
                                {{ $likelihoodDesc }}
                            </div>
                        </div>
                        
                        <!-- Impact -->
                        <div class="bg-red-50 p-5 rounded-lg">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                    <i data-feather="alert-circle" class="w-5 h-5 text-red-600"></i>
                                </div>
                                <div>
                                    <div class="font-medium">Dampak (Impact)</div>
                                    <div class="text-red-600 text-sm">Skala 1-5</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="flex justify-between items-center mb-1">
                                    <div class="text-gray-700">Level: {{ $analysis->impact_level }}/5</div>
                                    <div class="text-gray-700 font-medium">
                                        @php
                                            $impactText = [
                                                1 => 'Sangat Kecil',
                                                2 => 'Kecil',
                                                3 => 'Sedang',
                                                4 => 'Besar',
                                                5 => 'Sangat Besar'
                                            ][$analysis->impact_level];
                                        @endphp
                                        {{ $impactText }}
                                    </div>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-red-500 h-3 rounded-full" 
                                         style="width: {{ ($analysis->impact_level / 5) * 100 }}%"></div>
                                </div>
                            </div>
                            
                            <div class="text-sm text-gray-600 bg-white p-3 rounded">
                                <div class="font-medium mb-1">Deskripsi:</div>
                                @php
                                    $impactDesc = [
                                        1 => 'Dampak minimal, mudah diatasi dengan sumber daya biasa',
                                        2 => 'Dampak terbatas, dapat dikendalikan dengan sedikit penyesuaian',
                                        3 => 'Dampak signifikan, memerlukan perhatian dan tindakan khusus',
                                        4 => 'Dampak serius, mengganggu operasional dan memerlukan tindakan cepat',
                                        5 => 'Dampak kritis, mengancam keberlangsungan operasi'
                                    ][$analysis->impact_level];
                                @endphp
                                {{ $impactDesc }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trend Analysis -->
                @if($analysis->trend !== 'new')
                <div class="mb-8">
                    <h4 class="font-medium text-gray-700 mb-4">Analisis Perbandingan</h4>
                    <div class="bg-yellow-50 p-5 rounded-lg">
                        <div class="flex items-center mb-4">
                            <i data-feather="{{ $analysis->trend_icon }}" 
                               class="w-6 h-6 mr-3 text-{{ $analysis->trend_color }}-600"></i>
                            <div class="font-medium">Trend dari Analisis Sebelumnya</div>
                        </div>
                        
                        @php
                            $previousAnalysis = $analysis->previousAnalysis();
                        @endphp
                        
                        @if($previousAnalysis)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white p-4 rounded-lg">
                                <div class="text-sm text-gray-600 mb-2">Analisis Sebelumnya</div>
                                <div class="flex items-center">
                                    <div class="text-2xl font-bold mr-4">{{ $previousAnalysis->risk_score }}</div>
                                    <div>
                                        <div class="text-sm">Tanggal: {{ $previousAnalysis->analysis_date->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500">({{ $previousAnalysis->likelihood_level }} × {{ $previousAnalysis->impact_level }})</div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-lg">
                                <div class="text-sm text-gray-600 mb-2">Analisis Saat Ini</div>
                                <div class="flex items-center">
                                    <div class="text-2xl font-bold mr-4">{{ $analysis->risk_score }}</div>
                                    <div>
                                        <div class="text-sm">Tanggal: {{ $analysis->analysis_date->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500">({{ $analysis->likelihood_level }} × {{ $analysis->impact_level }})</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-white rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-medium">Perubahan Skor: </span>
                                    <span class="
                                        @if($analysis->trend == 'increase') text-red-600
                                        @elseif($analysis->trend == 'decrease') text-green-600
                                        @else text-blue-600
                                        @endif">
                                        @if($analysis->trend == 'increase')
                                            Meningkat {{ $analysis->risk_score - $previousAnalysis->risk_score }} poin
                                        @elseif($analysis->trend == 'decrease')
                                            Menurun {{ $previousAnalysis->risk_score - $analysis->risk_score }} poin
                                        @else
                                            Tidak berubah
                                        @endif
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600">
                                    Likelihood: 
                                    @if($analysis->likelihood_level > $previousAnalysis->likelihood_level)
                                        ↑ {{ $analysis->likelihood_level - $previousAnalysis->likelihood_level }}
                                    @elseif($analysis->likelihood_level < $previousAnalysis->likelihood_level)
                                        ↓ {{ $previousAnalysis->likelihood_level - $analysis->likelihood_level }}
                                    @else
                                        =
                                    @endif
                                    , Impact: 
                                    @if($analysis->impact_level > $previousAnalysis->impact_level)
                                        ↑ {{ $analysis->impact_level - $previousAnalysis->impact_level }}
                                    @elseif($analysis->impact_level < $previousAnalysis->impact_level)
                                        ↓ {{ $previousAnalysis->impact_level - $analysis->impact_level }}
                                    @else
                                        =
                                    @endif
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <div class="text-gray-600">Tidak ada analisis sebelumnya untuk dibandingkan</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Additional Information -->
                <div class="mb-8">
                    <h4 class="font-medium text-gray-700 mb-4">Informasi Tambahan</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i data-feather="calendar" class="w-4 h-4 mr-2 text-gray-500"></i>
                                <div class="text-gray-600">Dibuat Pada</div>
                            </div>
                            <div class="font-medium">{{ $analysis->created_at->format('d F Y H:i') }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i data-feather="edit" class="w-4 h-4 mr-2 text-gray-500"></i>
                                <div class="text-gray-600">Terakhir Diupdate</div>
                            </div>
                            <div class="font-medium">{{ $analysis->updated_at->format('d F Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end pt-5 border-t">
                    <a href="{{ route('risk-analyses.index', $risk->risk_id) }}" class="btn btn-outline-secondary mr-3">
                        <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
                    </a>
                    <a href="{{ route('risk-analyses.edit', [$risk->risk_id, $analysis->risk_analysis_id]) }}" class="btn btn-primary mr-3">
                        <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit Analisis
                    </a>
                    <form action="{{ route('risk-analyses.destroy', [$risk->risk_id, $analysis->risk_analysis_id]) }}" 
                          method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus analisis ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-span-12 lg:col-span-4">

        <!-- Risk Level Guide -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="info" class="w-5 h-5 mr-2"></i>
                    Panduan Level Risiko
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    @php
                        $riskLevels = [
                            [
                                'level' => 'Sangat Tinggi (20-25)',
                                'color' => 'bg-red-100 text-red-800',
                                'action' => 'Dihentikan segera, tindakan ekstrim diperlukan',
                                'icon' => 'alert-octagon'
                            ],
                            [
                                'level' => 'Tinggi (15-19)',
                                'color' => 'bg-orange-100 text-orange-800',
                                'action' => 'Diperlukan tindakan segera, monitoring intensif',
                                'icon' => 'alert-triangle'
                            ],
                            [
                                'level' => 'Sedang (10-14)',
                                'color' => 'bg-yellow-100 text-yellow-800',
                                'action' => 'Perlu pengendalian, monitoring rutin',
                                'icon' => 'alert-circle'
                            ],
                            [
                                'level' => 'Rendah (5-9)',
                                'color' => 'bg-blue-100 text-blue-800',
                                'action' => 'Dapat ditoleransi, monitoring berkala',
                                'icon' => 'eye'
                            ],
                            [
                                'level' => 'Sangat Rendah (1-4)',
                                'color' => 'bg-green-100 text-green-800',
                                'action' => 'Dapat diterima, monitoring minimal',
                                'icon' => 'check-circle'
                            ]
                        ];
                    @endphp
                    
                    @foreach($riskLevels as $riskInfo)
                        <div class="flex items-start p-3 rounded-lg {{ str_contains($analysis->risk_level_indo, $riskInfo['level']) ? 'border-2 border-theme-1' : '' }}">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3 {{ explode(' ', $riskInfo['color'])[0] }}">
                                <i data-feather="{{ $riskInfo['icon'] }}" class="w-4 h-4 {{ explode(' ', $riskInfo['color'])[1] }}"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium {{ $riskInfo['color'] }} inline-block px-2 py-1 rounded-full text-xs mb-1">
                                    {{ $riskInfo['level'] }}
                                </div>
                                <div class="text-xs text-gray-600">
                                    {{ $riskInfo['action'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Current Level Indicator -->
                @php
                    $currentLevelIndex = match($analysis->risk_level) {
                        'sangat_tinggi' => 0,
                        'tinggi' => 1,
                        'sedang' => 2,
                        'rendah' => 3,
                        'sangat_rendah' => 4,
                        default => 2
                    };
                @endphp
                
                <div class="mt-4 p-3 bg-theme-1/10 rounded-lg">
                    <div class="flex items-center">
                        <i data-feather="target" class="w-5 h-5 text-theme-1 mr-2"></i>
                        <div class="flex-1">
                            <div class="font-medium text-theme-1">Level Saat Ini:</div>
                            <div class="text-sm">{{ $riskLevels[$currentLevelIndex]['action'] }}</div>
                        </div>
                    </div>
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