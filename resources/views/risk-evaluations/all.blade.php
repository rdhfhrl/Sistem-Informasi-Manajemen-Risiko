@extends('layouts.master')

@section('title', 'Semua Evaluasi Risiko - SIMR')

@section('page-title', 'Semua Evaluasi Risiko')

@section('page-action')
<a href="{{ route('risks.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Daftar Risiko
</a>
<a href="{{ route('risk-evaluations.index') }}" class="btn btn-primary shadow-md mr-2">
    <i data-feather="filter" class="w-4 h-4 mr-2"></i> Evaluasi Per Risiko
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Filter Section -->
        <div class="intro-y box p-5 mb-6">
            <form method="GET" action="{{ route('risk-evaluations.all') }}" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="form-label">Cari</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" 
                               class="form-control w-full pl-10" 
                               placeholder="Cari berdasarkan kode risiko, keputusan mitigasi..."
                               value="{{ request('search') }}">
                        <div class="absolute left-3 top-2.5">
                            <i data-feather="search" class="w-5 h-5 text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <div class="w-full md:w-48">
                    <label for="priority" class="form-label">Prioritas</label>
                    <select id="priority" name="priority" class="form-select">
                        <option value="">Semua Prioritas</option>
                        <option value="rendah" {{ request('priority') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                        <option value="sedang" {{ request('priority') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                        <option value="tinggi" {{ request('priority') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                        <option value="sangat tinggi" {{ request('priority') == 'sangat tinggi' ? 'selected' : '' }}>Sangat Tinggi</option>
                    </select>
                </div>
                
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn btn-primary w-24">
                        <i data-feather="filter" class="w-4 h-4 mr-2"></i> Filter
                    </button>
                    <a href="{{ route('risk-evaluations.all') }}" class="btn btn-secondary w-24">
                        <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            @php
                $totalEvaluations = \App\Models\RiskEvaluation::count();
                $highPriorityCount = \App\Models\RiskEvaluation::whereIn('risk_evaluation_priority', ['tinggi', 'sangat tinggi'])->count();
                $mediumPriorityCount = \App\Models\RiskEvaluation::where('risk_evaluation_priority', 'sedang')->count();
                $lowPriorityCount = \App\Models\RiskEvaluation::where('risk_evaluation_priority', 'rendah')->count();
            @endphp
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-purple-100">
                                <i data-feather="star" class="w-6 h-6 text-purple-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <div class="text-3xl font-bold leading-8">{{ $totalEvaluations }}</div>
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
                    Daftar Semua Evaluasi Risiko
                    <span class="text-gray-500 text-sm ml-2">({{ $evaluations->total() }} data)</span>
                </h2>
                
                <!-- Export Options -->
                <div class="flex items-center space-x-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown">
                            <i data-feather="download" class="w-4 h-4 mr-2"></i> Export
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">
                                <i data-feather="file-text" class="w-4 h-4 mr-2"></i> Excel
                            </a>
                            <a class="dropdown-item" href="#">
                                <i data-feather="file" class="w-4 h-4 mr-2"></i> PDF
                            </a>
                            <a class="dropdown-item" href="#">
                                <i data-feather="printer" class="w-4 h-4 mr-2"></i> Print
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-5">
                @if($evaluations->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-report -mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">RISIKO</th>
                                    <th class="whitespace-nowrap">TANGGAL EVALUASI</th>
                                    <th class="whitespace-nowrap">PRIORITAS</th>
                                    <th class="whitespace-nowrap">KEPUTUSAN MITIGASI</th>
                                    <th class="whitespace-nowrap">PROYEKSI SKOR</th>
                                    <th class="whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($evaluations as $evaluation)
                                    <tr class="intro-x hover:bg-gray-50">
                                        <td>
                                            @if($evaluation->risk)
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                                        <i data-feather="alert-triangle" class="w-5 h-5 text-red-600"></i>
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('risks.show', $evaluation->risk->risk_id) }}" 
                                                           class="font-medium hover:text-red-600">
                                                            {{ $evaluation->risk->risk_code }}
                                                        </a>
                                                        <div class="text-gray-500 text-xs mt-0.5">
                                                            {{ Str::limit($evaluation->risk->risk_description, 30) }}
                                                        </div>
                                                        @if($evaluation->risk->risk_score)
                                                            <div class="text-xs text-blue-600 mt-1">
                                                                Skor: {{ $evaluation->risk->risk_score }}
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
                                                        {{ $evaluation->evaluation_date->format('d M Y') }}
                                                    </div>
                                                    <div class="text-gray-500 text-xs mt-0.5">
                                                        {{ $evaluation->created_at->format('H:i') }}
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
                                                    {{ Str::limit($evaluation->mitigation_decision, 50) }}
                                                </div>
                                                <div class="text-gray-500 text-xs mt-1">
                                                    {{ Str::limit($evaluation->mitigation_decision, 80) }}
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            @if($evaluation->projected_risk_score)
                                                <div class="text-xl font-bold 
                                                    @if($evaluation->projected_risk_score >= 20) text-red-600
                                                    @elseif($evaluation->projected_risk_score >= 15) text-orange-600
                                                    @elseif($evaluation->projected_risk_score >= 10) text-yellow-600
                                                    @elseif($evaluation->projected_risk_score >= 5) text-blue-600
                                                    @else text-green-600
                                                    @endif">
                                                    {{ $evaluation->projected_risk_score }}
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </td>
                                        
                                        <td class="table-report__action w-32">
                                            <div class="flex justify-center items-center">
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('risk-evaluations.show', [$evaluation->risk_evaluation_risk_id, $evaluation->risk_evaluation_id]) }}"
                                                   data-toggle="tooltip" title="Detail Evaluasi">
                                                    <i data-feather="eye" class="w-4 h-4"></i>
                                                </a>
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('risk-evaluations.edit', [$evaluation->risk_evaluation_risk_id, $evaluation->risk_evaluation_id]) }}"
                                                   data-toggle="tooltip" title="Edit Evaluasi">
                                                    <i data-feather="edit" class="w-4 h-4"></i>
                                                </a>
                                                <a class="flex items-center" 
                                                   href="{{ route('risk-evaluations.by-risk', $evaluation->risk_evaluation_risk_id) }}"
                                                   data-toggle="tooltip" title="Lihat Risiko">
                                                    <i data-feather="alert-triangle" class="w-4 h-4"></i>
                                                </a>
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
                        @if(request()->hasAny(['search', 'priority']))
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Tidak ditemukan</h3>
                            <p class="text-gray-500 mb-6">Tidak ada evaluasi risiko yang sesuai dengan filter yang dipilih</p>
                            <a href="{{ route('risk-evaluations.all') }}" 
                               class="btn btn-secondary mr-2">
                                <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i> Reset Filter
                            </a>
                        @else
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada evaluasi risiko</h3>
                            <p class="text-gray-500 mb-6">Evaluasi risiko akan muncul setelah dibuat pada masing-masing risiko</p>
                        @endif
                        <a href="{{ route('risks.index') }}" 
                           class="btn btn-primary mr-2">
                            <i data-feather="alert-triangle" class="w-4 h-4 mr-2"></i> Lihat Daftar Risiko
                        </a>
                        <a href="{{ route('risk-evaluations.index') }}" 
                           class="btn btn-success">
                            <i data-feather="filter" class="w-4 h-4 mr-2"></i> Evaluasi Per Risiko
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistics Charts -->
        @if($evaluations->count() > 0)
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="pie-chart" class="w-5 h-5 mr-2"></i> Statistik Evaluasi Risiko
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Priority Distribution -->
                    <div>
                        <h4 class="font-medium mb-3">Distribusi Prioritas</h4>
                        <div class="space-y-2">
                            @php
                                $priorityGroups = $evaluations->groupBy('risk_evaluation_priority');
                                $total = $evaluations->count();
                            @endphp
                            
                            @foreach(['sangat tinggi', 'tinggi', 'sedang', 'rendah'] as $priority)
                                @php
                                    $count = $priorityGroups->get($priority) ? $priorityGroups->get($priority)->count() : 0;
                                    $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                    $color = match($priority) {
                                        'sangat tinggi' => 'bg-red-500',
                                        'tinggi' => 'bg-orange-500',
                                        'sedang' => 'bg-yellow-500',
                                        'rendah' => 'bg-green-500'
                                    };
                                @endphp
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span>{{ ucwords($priority) }}</span>
                                        <span>{{ $count }} ({{ round($percentage, 1) }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="{{ $color }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Monthly Evaluation Trend -->
                    <div>
                        <h4 class="font-medium mb-3">Trend Evaluasi Bulanan</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <canvas id="monthlyTrendChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Quick Summary -->
        @if($evaluations->count() > 0)
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="zap" class="w-5 h-5 mr-2"></i> Ringkasan Cepat
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Highest Priority -->
                    @php
                        $highestPriority = $evaluations->where('risk_evaluation_priority', 'sangat tinggi')->first();
                    @endphp
                    <div class="bg-red-50 p-4 rounded-lg">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                <i data-feather="alert-octagon" class="w-5 h-5 text-red-600"></i>
                            </div>
                            <div>
                                <div class="font-medium text-red-800">Evaluasi Prioritas Tertinggi</div>
                            </div>
                        </div>
                        @if($highestPriority)
                        <div class="text-center mb-3">
                            <div class="text-2xl font-bold text-red-600">{{ $highestPriority->risk->risk_code ?? 'N/A' }}</div>
                            <div class="text-sm text-red-700">{{ Str::limit($highestPriority->mitigation_decision, 30) }}</div>
                        </div>
                        <div class="text-xs text-gray-600">
                            Tanggal: {{ $highestPriority->evaluation_date->format('d M Y') }}
                        </div>
                        @endif
                    </div>
                    
                    <!-- Latest Evaluation -->
                    @php
                        $latestEvaluation = $evaluations->sortByDesc('evaluation_date')->first();
                    @endphp
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i data-feather="clock" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <div class="font-medium text-blue-800">Evaluasi Terbaru</div>
                            </div>
                        </div>
                        @if($latestEvaluation)
                        <div class="text-center mb-3">
                            <div class="text-2xl font-bold text-blue-600">
                                {{ $latestEvaluation->evaluation_date->format('d M') }}
                            </div>
                            <div class="text-sm text-blue-700">{{ $latestEvaluation->risk->risk_code ?? 'N/A' }}</div>
                        </div>
                        <div class="text-xs text-gray-600">
                            Prioritas: {{ ucwords($latestEvaluation->risk_evaluation_priority) }}
                        </div>
                        @endif
                    </div>
                    
                    <!-- Most Evaluated Risk -->
                    @php
                        $mostEvaluated = \App\Models\RiskEvaluation::select('risk_evaluation_risk_id', \DB::raw('COUNT(*) as evaluation_count'))
                            ->groupBy('risk_evaluation_risk_id')
                            ->orderBy('evaluation_count', 'desc')
                            ->first();
                    @endphp
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                <i data-feather="repeat" class="w-5 h-5 text-green-600"></i>
                            </div>
                            <div>
                                <div class="font-medium text-green-800">Risiko Paling Sering Dievaluasi</div>
                            </div>
                        </div>
                        @if($mostEvaluated && $mostEvaluated->risk)
                        <div class="text-center mb-3">
                            <div class="text-3xl font-bold text-green-600">{{ $mostEvaluated->evaluation_count }}</div>
                            <div class="text-sm text-green-700">{{ $mostEvaluated->risk->risk_code }}</div>
                        </div>
                        <div class="text-xs text-gray-600">
                            {{ Str::limit($mostEvaluated->risk->risk_description, 30) }}
                        </div>
                        @endif
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
    
    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) closeBtn.click();
        });
    }, 5000);
    
    // Quick search on enter
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });
    }
    
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
    
    // Monthly Trend Chart
    @if($evaluations->count() > 0)
    const monthlyChartCtx = document.getElementById('monthlyTrendChart');
    if (monthlyChartCtx) {
        @php
            // Group evaluations by month
            $monthlyData = [];
            $evaluations->each(function($evaluation) use (&$monthlyData) {
                $month = $evaluation->evaluation_date->format('Y-m');
                if (!isset($monthlyData[$month])) {
                    $monthlyData[$month] = 0;
                }
                $monthlyData[$month]++;
            });
            
            // Prepare chart data
            $months = [];
            $counts = [];
            
            ksort($monthlyData);
            foreach($monthlyData as $month => $count) {
                $months[] = \Carbon\Carbon::parse($month)->format('M Y');
                $counts[] = $count;
            }
        @endphp
        
        new Chart(monthlyChartCtx, {
            type: 'line',
            data: {
                labels: @json($months),
                datasets: [{
                    label: 'Jumlah Evaluasi',
                    data: @json($counts),
                    borderColor: '#8B5CF6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
    @endif
});
</script>
@endpush