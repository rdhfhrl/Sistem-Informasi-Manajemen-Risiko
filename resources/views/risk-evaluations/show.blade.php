@extends('layouts.master')

@section('title', 'Detail Evaluasi Risiko - SIMR')

@section('page-title', 'Detail Evaluasi Risiko')

@section('page-action')
<a href="{{ route('risk-evaluations.by-risk', $risk->risk_id) }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Daftar
</a>
<a href="{{ route('risk-evaluations.edit', [$risk->risk_id, $evaluation->risk_evaluation_id]) }}" class="btn btn-primary shadow-md mr-2">
    <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit Evaluasi
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 lg:col-span-8">
        <!-- Evaluation Details Card -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="star" class="w-5 h-5 mr-2"></i>
                    Detail Evaluasi Risiko
                </h2>
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
                </div>

                <!-- Evaluation Summary -->
                <div class="mb-8">
                    <h4 class="font-medium text-gray-700 mb-4">Ringkasan Evaluasi</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-blue-50 p-6 rounded-lg text-center">
                            <div class="text-blue-600 text-sm mb-2">Tanggal Evaluasi</div>
                            <div class="text-2xl font-bold text-blue-700">
                                {{ $evaluation->evaluation_date->format('d M Y') }}
                            </div>
                            <div class="text-gray-500 text-xs mt-2">
                                Dibuat: {{ $evaluation->created_at->format('H:i') }}
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 p-6 rounded-lg text-center">
                            <div class="text-yellow-600 text-sm mb-2">Prioritas Penanganan</div>
                            <div class="flex flex-col items-center">
                                <span class="px-4 py-2 rounded-full text-lg font-bold mb-2
                                    @if($evaluation->risk_evaluation_priority == 'rendah') bg-green-100 text-green-800
                                    @elseif($evaluation->risk_evaluation_priority == 'sedang') bg-yellow-100 text-yellow-800
                                    @elseif($evaluation->risk_evaluation_priority == 'tinggi') bg-orange-100 text-orange-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucwords($evaluation->risk_evaluation_priority) }}
                                </span>
                                <div class="text-yellow-500 text-sm">
                                    @php
                                        $priorityTime = match($evaluation->risk_evaluation_priority) {
                                            'rendah' => 'Bisa ditunda (≤ 1 bulan)',
                                            'sedang' => 'Menengah (≤ 2 minggu)',
                                            'tinggi' => 'Cepat (≤ 3 hari)',
                                            'sangat tinggi' => 'Segera (≤ 24 jam)'
                                        };
                                    @endphp
                                    {{ $priorityTime }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-6 rounded-lg text-center">
                            <div class="text-green-600 text-sm mb-2">Proyeksi Skor Risiko</div>
                            <div class="text-3xl font-bold 
                                @if($evaluation->projected_risk_score >= 20) text-red-600
                                @elseif($evaluation->projected_risk_score >= 15) text-orange-600
                                @elseif($evaluation->projected_risk_score >= 10) text-yellow-600
                                @elseif($evaluation->projected_risk_score >= 5) text-blue-600
                                @else text-green-600
                                @endif">
                                {{ $evaluation->projected_risk_score ?? '-' }}
                            </div>
                            @if($evaluation->projected_risk_score && $risk->risk_score)
                                @php
                                    $difference = $evaluation->projected_risk_score - $risk->risk_score;
                                    $trend = $difference > 0 ? 'increase' : ($difference < 0 ? 'decrease' : 'stable');
                                @endphp
                                <div class="text-sm mt-2 
                                    @if($trend == 'increase') text-red-600
                                    @elseif($trend == 'decrease') text-green-600
                                    @else text-gray-600
                                    @endif">
                                    @if($trend == 'increase')
                                        <i data-feather="arrow-up" class="w-4 h-4 inline"></i> Meningkat {{ $difference }} poin
                                    @elseif($trend == 'decrease')
                                        <i data-feather="arrow-down" class="w-4 h-4 inline"></i> Menurun {{ abs($difference) }} poin
                                    @else
                                        <i data-feather="minus" class="w-4 h-4 inline"></i> Stabil
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Mitigation Decision Details -->
                <div class="mb-8">
                    <h4 class="font-medium text-gray-700 mb-4">Keputusan Mitigasi</h4>
                    <div class="bg-white border rounded-lg p-6">
                        <div class="prose max-w-none">
                            {!! nl2br(e($evaluation->mitigation_decision)) !!}
                        </div>
                    </div>
                </div>

                <!-- Score Analysis -->
                @if($evaluation->projected_risk_score && $risk->risk_score)
                <div class="mb-8">
                    <h4 class="font-medium text-gray-700 mb-4">Analisis Skor</h4>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h5 class="font-medium text-gray-600 mb-3">Perbandingan Skor</h5>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <span>Skor Saat Ini:</span>
                                        <span class="font-bold">{{ $risk->risk_score }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span>Proyeksi Skor:</span>
                                        <span class="font-bold">{{ $evaluation->projected_risk_score }}</span>
                                    </div>
                                    <div class="flex justify-between items-center border-t pt-4">
                                        <span>Perubahan:</span>
                                        <span class="font-bold 
                                            @if($difference > 0) text-red-600
                                            @elseif($difference < 0) text-green-600
                                            @else text-gray-600
                                            @endif">
                                            @if($difference > 0)
                                                +{{ $difference }} poin
                                            @elseif($difference < 0)
                                                {{ $difference }} poin
                                            @else
                                                0 poin
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span>Persentase Perubahan:</span>
                                        <span class="font-bold">
                                            @php
                                                $percentageChange = ($difference / $risk->risk_score) * 100;
                                            @endphp
                                            {{ number_format($percentageChange, 1) }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-600 mb-3">Implikasi</h5>
                                <div class="space-y-2">
                                    @if($difference > 0)
                                        <div class="flex items-start">
                                            <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center mr-2 mt-0.5">
                                                <i data-feather="alert-circle" class="w-3 h-3 text-red-600"></i>
                                            </div>
                                            <div class="text-sm text-red-700">
                                                Risiko diproyeksikan akan meningkat, diperlukan tindakan mitigasi yang lebih intensif.
                                            </div>
                                        </div>
                                    @elseif($difference < 0)
                                        <div class="flex items-start">
                                            <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center mr-2 mt-0.5">
                                                <i data-feather="check-circle" class="w-3 h-3 text-green-600"></i>
                                            </div>
                                            <div class="text-sm text-green-700">
                                                Risiko diproyeksikan akan menurun, menunjukkan efektivitas rencana mitigasi.
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-start">
                                            <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center mr-2 mt-0.5">
                                                <i data-feather="minus-circle" class="w-3 h-3 text-blue-600"></i>
                                            </div>
                                            <div class="text-sm text-blue-700">
                                                Risiko diproyeksikan stabil, monitoring rutin tetap diperlukan.
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($evaluation->projected_risk_score >= 20)
                                        <div class="flex items-start mt-2">
                                            <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center mr-2 mt-0.5">
                                                <i data-feather="alert-octagon" class="w-3 h-3 text-red-600"></i>
                                            </div>
                                            <div class="text-sm text-red-700">
                                                Proyeksi skor sangat tinggi, pertimbangkan tindakan darurat.
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
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
                            <div class="font-medium">{{ $evaluation->created_at->format('d F Y H:i') }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i data-feather="edit" class="w-4 h-4 mr-2 text-gray-500"></i>
                                <div class="text-gray-600">Terakhir Diupdate</div>
                            </div>
                            <div class="font-medium">{{ $evaluation->updated_at->format('d F Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end pt-5 border-t">
                    <a href="{{ route('risk-evaluations.by-risk', $risk->risk_id) }}" class="btn btn-outline-secondary mr-3">
                        <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
                    </a>
                    <a href="{{ route('risk-evaluations.edit', [$risk->risk_id, $evaluation->risk_evaluation_id]) }}" class="btn btn-primary mr-3">
                        <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit Evaluasi
                    </a>
                    <form action="{{ route('risk-evaluations.destroy', [$risk->risk_id, $evaluation->risk_evaluation_id]) }}" 
                          method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus evaluasi ini?')">
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
        <!-- Priority Guide -->
        <div class="intro-y box mb-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="info" class="w-5 h-5 mr-2"></i>
                    Panduan Prioritas
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    @php
                        $priorityLevels = [
                            [
                                'level' => 'Sangat Tinggi',
                                'color' => 'bg-red-100 text-red-800',
                                'time' => 'Segera (≤ 24 jam)',
                                'action' => 'Tindakan darurat, alokasi sumber daya maksimal',
                                'icon' => 'alert-octagon'
                            ],
                            [
                                'level' => 'Tinggi',
                                'color' => 'bg-orange-100 text-orange-800',
                                'time' => 'Cepat (≤ 3 hari)',
                                'action' => 'Perlu tindakan segera, monitoring intensif',
                                'icon' => 'alert-triangle'
                            ],
                            [
                                'level' => 'Sedang',
                                'color' => 'bg-yellow-100 text-yellow-800',
                                'time' => 'Menengah (≤ 2 minggu)',
                                'action' => 'Perencanaan matang, tindakan terstruktur',
                                'icon' => 'alert-circle'
                            ],
                            [
                                'level' => 'Rendah',
                                'color' => 'bg-green-100 text-green-800',
                                'time' => 'Bisa ditunda (≤ 1 bulan)',
                                'action' => 'Tindakan rutin, monitoring berkala',
                                'icon' => 'check-circle'
                            ]
                        ];
                    @endphp
                    
                    @foreach($priorityLevels as $priorityInfo)
                        <div class="flex items-start p-3 rounded-lg {{ str_contains(ucwords($evaluation->risk_evaluation_priority), $priorityInfo['level']) ? 'border-2 border-theme-1' : '' }}">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3 {{ explode(' ', $priorityInfo['color'])[0] }}">
                                <i data-feather="{{ $priorityInfo['icon'] }}" class="w-4 h-4 {{ explode(' ', $priorityInfo['color'])[1] }}"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium {{ $priorityInfo['color'] }} inline-block px-2 py-1 rounded-full text-xs mb-1">
                                    {{ $priorityInfo['level'] }}
                                </div>
                                <div class="text-xs text-gray-600 font-medium mb-1">
                                    {{ $priorityInfo['time'] }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $priorityInfo['action'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Current Priority Indicator -->
                @php
                    $currentPriorityIndex = match($evaluation->risk_evaluation_priority) {
                        'sangat tinggi' => 0,
                        'tinggi' => 1,
                        'sedang' => 2,
                        'rendah' => 3,
                        default => 2
                    };
                @endphp
                
                <div class="mt-4 p-3 bg-theme-1/10 rounded-lg">
                    <div class="flex items-center">
                        <i data-feather="target" class="w-5 h-5 text-theme-1 mr-2"></i>
                        <div class="flex-1">
                            <div class="font-medium text-theme-1">Status Saat Ini:</div>
                            <div class="text-sm">{{ $priorityLevels[$currentPriorityIndex]['action'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Score Guide -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="bar-chart" class="w-5 h-5 mr-2"></i>
                    Panduan Skor Risiko
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-2">
                    @php
                        $scoreRanges = [
                            [
                                'range' => '20-25',
                                'level' => 'Sangat Tinggi',
                                'color' => 'bg-red-500',
                                'action' => 'Dihentikan segera, tindakan ekstrim diperlukan'
                            ],
                            [
                                'range' => '15-19',
                                'level' => 'Tinggi',
                                'color' => 'bg-orange-500',
                                'action' => 'Diperlukan tindakan segera, monitoring intensif'
                            ],
                            [
                                'range' => '10-14',
                                'level' => 'Sedang',
                                'color' => 'bg-yellow-500',
                                'action' => 'Perlu pengendalian, monitoring rutin'
                            ],
                            [
                                'range' => '5-9',
                                'level' => 'Rendah',
                                'color' => 'bg-blue-500',
                                'action' => 'Dapat ditoleransi, monitoring berkala'
                            ],
                            [
                                'range' => '1-4',
                                'level' => 'Sangat Rendah',
                                'color' => 'bg-green-500',
                                'action' => 'Dapat diterima, monitoring minimal'
                            ]
                        ];
                    @endphp
                    
                    @foreach($scoreRanges as $scoreInfo)
                        <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                            <div class="flex items-center">
                                <div class="w-3 h-3 {{ $scoreInfo['color'] }} rounded mr-2"></div>
                                <div>
                                    <div class="text-sm font-medium">{{ $scoreInfo['range'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $scoreInfo['level'] }}</div>
                                </div>
                            </div>
                            <div class="text-xs text-gray-600 text-right max-w-xs">
                                {{ $scoreInfo['action'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($evaluation->projected_risk_score)
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <div class="text-sm font-medium text-gray-700 mb-2">Proyeksi Skor Saat Ini:</div>
                        <div class="flex items-center justify-between">
                            <div class="text-2xl font-bold 
                                @if($evaluation->projected_risk_score >= 20) text-red-600
                                @elseif($evaluation->projected_risk_score >= 15) text-orange-600
                                @elseif($evaluation->projected_risk_score >= 10) text-yellow-600
                                @elseif($evaluation->projected_risk_score >= 5) text-blue-600
                                @else text-green-600
                                @endif">
                                {{ $evaluation->projected_risk_score }}
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ $scoreRanges[array_search(true, array_map(function($range) use ($evaluation) {
                                    return $evaluation->projected_risk_score >= $range['min'] && $evaluation->projected_risk_score <= $range['max'];
                                }, [
                                    ['min' => 20, 'max' => 25, 'level' => 'Sangat Tinggi'],
                                    ['min' => 15, 'max' => 19, 'level' => 'Tinggi'],
                                    ['min' => 10, 'max' => 14, 'level' => 'Sedang'],
                                    ['min' => 5, 'max' => 9, 'level' => 'Rendah'],
                                    ['min' => 1, 'max' => 4, 'level' => 'Sangat Rendah']
                                ]))]['level'] ?? 'Tidak diketahui' }}
                            </div>
                        </div>
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