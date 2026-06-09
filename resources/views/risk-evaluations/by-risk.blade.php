@extends('layouts.master')

@section('title', 'Evaluasi Risiko - SIMR')

@section('page-title', 'Evaluasi Risiko')

@section('page-action')
<a href="{{ route('risks.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Daftar Risiko
</a>
<a href="{{ route('risk-evaluations.create', $risk->risk_id) }}" class="btn btn-primary shadow-md mr-2">
    <i data-feather="plus" class="w-4 h-4 mr-2"></i> Evaluasi Baru
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
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-purple-100">
                                <i data-feather="star" class="w-6 h-6 text-purple-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <div class="text-3xl font-bold leading-8">{{ $evaluations->total() }}</div>
                            <div class="text-base text-gray-600 mt-1">Total Evaluasi</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-red-100">
                                <i data-feather="alert-octagon" class="w-6 h-6 text-red-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            @php
                                $highPriorityCount = collect($evaluations->items())->whereIn('risk_evaluation_priority', ['tinggi', 'sangat tinggi'])->count();
                            @endphp
                            <div class="text-3xl font-bold leading-8">{{ $highPriorityCount }}</div>
                            <div class="text-base text-gray-600 mt-1">Prioritas Tinggi</div>
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
                                $mediumPriorityCount = collect($evaluations->items())->where('risk_evaluation_priority', 'sedang')->count();
                            @endphp
                            <div class="text-3xl font-bold leading-8">{{ $mediumPriorityCount }}</div>
                            <div class="text-base text-gray-600 mt-1">Prioritas Sedang</div>
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
                            @php
                                $lowPriorityCount = collect($evaluations->items())->where('risk_evaluation_priority', 'rendah')->count();
                            @endphp
                            <div class="text-3xl font-bold leading-8">{{ $lowPriorityCount }}</div>
                            <div class="text-base text-gray-600 mt-1">Prioritas Rendah</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Riwayat Evaluasi Risiko
                    <span class="text-gray-500 text-sm ml-2">({{ $evaluations->total() }} data)</span>
                </h2>
                
                @if($evaluations->count() > 0)
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">Filter:</span>
                    <select id="filter-priority" class="form-select w-40">
                        <option value="">Semua Prioritas</option>
                        <option value="rendah">Rendah</option>
                        <option value="sedang">Sedang</option>
                        <option value="tinggi">Tinggi</option>
                        <option value="sangat tinggi">Sangat Tinggi</option>
                    </select>
                </div>
                @endif
            </div>
            <div class="p-5">
                @if($evaluations->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-report -mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">TANGGAL EVALUASI</th>
                                    <th class="whitespace-nowrap">PRIORITAS PENANGANAN</th>
                                    <th class="whitespace-nowrap">KEPUTUSAN MITIGASI</th>
                                    <th class="whitespace-nowrap">PROYEKSI SKOR</th>
                                    <th class="whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($evaluations as $evaluation)
                                    <tr class="intro-x hover:bg-gray-50" data-priority="{{ $evaluation->risk_evaluation_priority }}">
                                        <td>
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                                    <i data-feather="calendar" class="w-5 h-5 text-gray-600"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium">
                                                        {{ \Carbon\Carbon::parse($evaluation->evaluation_date)->format('d M Y') }}
                                                    </div>
                                                    <div class="text-gray-500 text-xs mt-0.5">
                                                        {{ \Carbon\Carbon::parse($evaluation->created_at)->format('H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <span class="px-3 py-1 rounded-full text-xs font-medium 
                                                @if($evaluation->risk_evaluation_priority == 'rendah') bg-green-100 text-green-800
                                                @elseif($evaluation->risk_evaluation_priority == 'sedang') bg-yellow-100 text-yellow-800
                                                @elseif($evaluation->risk_evaluation_priority == 'tinggi') bg-orange-100 text-orange-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucwords($evaluation->risk_evaluation_priority) }}
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <div class="max-w-xs">
                                                <div class="font-medium text-gray-800">
                                                    {{ Str::limit($evaluation->mitigation_decision, 60) }}
                                                </div>
                                                <div class="text-gray-500 text-xs mt-1">
                                                    {{ Str::limit($evaluation->mitigation_decision, 100) }}
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            @if($evaluation->projected_risk_score)
                                                <div class="flex items-center">
                                                    <div class="text-2xl font-bold 
                                                        @if($evaluation->projected_risk_score >= 20) text-red-600
                                                        @elseif($evaluation->projected_risk_score >= 15) text-orange-600
                                                        @elseif($evaluation->projected_risk_score >= 10) text-yellow-600
                                                        @elseif($evaluation->projected_risk_score >= 5) text-blue-600
                                                        @else text-green-600
                                                        @endif">
                                                        {{ $evaluation->projected_risk_score }}
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-xs text-gray-600">Proyeksi</div>
                                                        @if($risk->risk_score)
                                                            @php
                                                                $difference = $evaluation->projected_risk_score - $risk->risk_score;
                                                                $trend = $difference > 0 ? 'increase' : ($difference < 0 ? 'decrease' : 'stable');
                                                            @endphp
                                                            <div class="text-xs 
                                                                @if($trend == 'increase') text-red-600
                                                                @elseif($trend == 'decrease') text-green-600
                                                                @else text-gray-600
                                                                @endif">
                                                                @if($trend == 'increase')
                                                                    <i data-feather="arrow-up" class="w-3 h-3 inline"></i> +{{ $difference }}
                                                                @elseif($trend == 'decrease')
                                                                    <i data-feather="arrow-down" class="w-3 h-3 inline"></i> {{ $difference }}
                                                                @else
                                                                    <i data-feather="minus" class="w-3 h-3 inline"></i> {{ $difference }}
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </td>
                                        
                                        <td class="table-report__action w-56">
                                            <div class="flex justify-center items-center">
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('risk-evaluations.show', [$risk->risk_id, $evaluation->risk_evaluation_id]) }}">
                                                    <i data-feather="eye" class="w-4 h-4 mr-1"></i> Detail
                                                </a>
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('risk-evaluations.edit', [$risk->risk_id, $evaluation->risk_evaluation_id]) }}">
                                                    <i data-feather="edit" class="w-4 h-4 mr-1"></i> Edit
                                                </a>
                                                <form action="{{ route('risk-evaluations.destroy', [$risk->risk_id, $evaluation->risk_evaluation_id]) }}" 
                                                      method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus evaluasi ini?')">
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
                    @if($evaluations->hasPages())
                    <div class="flex flex-col sm:flex-row items-center p-5 border-t border-gray-200">
                        <div class="text-sm text-gray-500">
                            Menampilkan {{ $evaluations->firstItem() }} - {{ $evaluations->lastItem() }} dari {{ $evaluations->total() }} evaluasi
                        </div>
                        <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                            {{ $evaluations->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="star" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada evaluasi risiko</h3>
                        <p class="text-gray-500 mb-6">Evaluasi risiko akan muncul setelah Anda membuat evaluasi untuk risiko ini</p>
                        <a href="{{ route('risk-evaluations.create', $risk->risk_id) }}" 
                           class="btn btn-primary">
                            <i data-feather="plus" class="w-4 h-4 mr-2"></i> Buat Evaluasi Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Evaluation Summary -->
        @if($evaluations->count() > 0)
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="pie-chart" class="w-5 h-5 mr-2"></i> Ringkasan Evaluasi
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Priority Distribution -->
                    <div>
                        <h4 class="font-medium mb-4">Distribusi Prioritas Penanganan</h4>
                        <div class="space-y-3">
                            @php
                                $priorities = $evaluations->groupBy('risk_evaluation_priority');
                                $total = $evaluations->count();
                            @endphp
                            
                            @foreach(['sangat tinggi', 'tinggi', 'sedang', 'rendah'] as $priority)
                                @php
                                    $count = $priorities->get($priority) ? $priorities->get($priority)->count() : 0;
                                    $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                    $color = match($priority) {
                                        'sangat tinggi' => 'bg-red-500',
                                        'tinggi' => 'bg-orange-500',
                                        'sedang' => 'bg-yellow-500',
                                        'rendah' => 'bg-green-500'
                                    };
                                @endphp
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <div class="flex items-center">
                                            <span class="px-2 py-1 rounded text-xs font-medium 
                                                @if($priority == 'rendah') bg-green-100 text-green-800
                                                @elseif($priority == 'sedang') bg-yellow-100 text-yellow-800
                                                @elseif($priority == 'tinggi') bg-orange-100 text-orange-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucwords($priority) }}
                                            </span>
                                            <span class="ml-2 text-sm">{{ $count }} evaluasi</span>
                                        </div>
                                        <span class="text-sm font-medium">{{ round($percentage, 1) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="{{ $color }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Latest Evaluation Stats -->
                    <div>
                        <h4 class="font-medium mb-4">Statistik Evaluasi Terbaru</h4>
                        @php
                            $latestEvaluation = $evaluations->first();
                        @endphp
                        <div class="space-y-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-gray-600 text-sm mb-2">Prioritas Terbaru</div>
                                <div class="flex items-center">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium 
                                        @if($latestEvaluation->risk_evaluation_priority == 'rendah') bg-green-100 text-green-800
                                        @elseif($latestEvaluation->risk_evaluation_priority == 'sedang') bg-yellow-100 text-yellow-800
                                        @elseif($latestEvaluation->risk_evaluation_priority == 'tinggi') bg-orange-100 text-orange-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucwords($latestEvaluation->risk_evaluation_priority) }}
                                    </span>
                                    <span class="ml-3 text-sm text-gray-600">
                                        {{ $latestEvaluation->evaluation_date->format('d F Y') }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-gray-600 text-sm mb-2">Keputusan Mitigasi</div>
                                <div class="text-sm text-gray-800">
                                    {{ Str::limit($latestEvaluation->mitigation_decision, 100) }}
                                </div>
                            </div>
                            
                            @if($latestEvaluation->projected_risk_score)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-gray-600 text-sm mb-2">Proyeksi Skor Risiko</div>
                                <div class="flex items-center">
                                    <div class="text-2xl font-bold 
                                        @if($latestEvaluation->projected_risk_score >= 20) text-red-600
                                        @elseif($latestEvaluation->projected_risk_score >= 15) text-orange-600
                                        @elseif($latestEvaluation->projected_risk_score >= 10) text-yellow-600
                                        @elseif($latestEvaluation->projected_risk_score >= 5) text-blue-600
                                        @else text-green-600
                                        @endif">
                                        {{ $latestEvaluation->projected_risk_score }}
                                    </div>
                                    @if($risk->risk_score)
                                        @php
                                            $difference = $latestEvaluation->projected_risk_score - $risk->risk_score;
                                            $trend = $difference > 0 ? 'increase' : ($difference < 0 ? 'decrease' : 'stable');
                                        @endphp
                                        <div class="ml-4">
                                            <div class="text-sm 
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
                                            <div class="text-xs text-gray-500">dari skor saat ini: {{ $risk->risk_score }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Filter by priority
    const filterSelect = document.getElementById('filter-priority');
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const selectedPriority = this.value;
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                if (!selectedPriority || row.dataset.priority === selectedPriority) {
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
});
</script>
@endpush