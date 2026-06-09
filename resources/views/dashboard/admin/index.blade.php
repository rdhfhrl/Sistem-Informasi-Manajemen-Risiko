@extends('layouts.master')

@section('title', 'Dashboard Admin - SIMR')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">
        Dashboard Administrator
        <span class="text-sm text-slate-500 font-normal ml-2">Sistem Informasi Manajemen Risiko</span>
    </h2>
</div>

<!-- Admin Statistics -->
@include('dashboard.admin.components.stats', ['stats' => $stats])

<!-- Risk Matrix & Distribution -->
<div class="grid grid-cols-12 gap-6 mt-5">
    <!-- Risk Matrix -->
    <div class="col-span-12 lg:col-span-8">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-slate-200/60">
                <h2 class="font-medium text-base mr-auto">Matriks Risiko Sistem</h2>
                <div class="dropdown ml-auto sm:ml-0">
                    <a class="dropdown-toggle w-5 h-5 block" href="javascript:;" aria-expanded="false">
                        <i data-feather="more-horizontal" class="w-5 h-5 text-slate-500"></i>
                    </a>
                    <div class="dropdown-menu w-40">
                        <div class="dropdown-menu__content box dark:bg-dark-1 p-2">
                            <a href="{{ route('risks.index') }}" class="flex items-center p-2 transition duration-300 ease-in-out bg-white dark:bg-dark-1 hover:bg-slate-100 dark:hover:bg-dark-2 rounded-md">
                                <i data-feather="list" class="w-4 h-4 mr-2"></i> Lihat Semua Risiko
                            </a>
                            <a href="javascript:;" onclick="exportRiskMatrix()" class="flex items-center p-2 transition duration-300 ease-in-out bg-white dark:bg-dark-1 hover:bg-slate-100 dark:hover:bg-dark-2 rounded-md">
                                <i data-feather="download" class="w-4 h-4 mr-2"></i> Export Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-5">
                <div class="risk-matrix-container">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th class="text-center">Kemungkinan →<br>Dampak ↓</th>
                                @for($i = 1; $i <= 5; $i++)
                                <th class="text-center">{{ $i }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @for($impact = 5; $impact >= 1; $impact--)
                            <tr>
                                <th class="text-center">{{ $impact }}</th>
                                @for($likelihood = 1; $likelihood <= 5; $likelihood++)
                                @php
                                    $cell = $riskMatrix[$likelihood][$impact] ?? ['count' => 0, 'level' => '', 'color' => '#fff'];
                                    $score = $likelihood * $impact;
                                @endphp
                                <td class="text-center" 
                                    style="background-color: {{ $cell['color'] }}20; border: 2px solid {{ $cell['color'] }}"
                                    title="Skor: {{ $score }} | Level: {{ $cell['level'] }} | Jumlah: {{ $cell['count'] }}">
                                    <div class="font-bold">{{ $score }}</div>
                                </td>
                                @endfor
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Risk Distribution -->
    <div class="col-span-12 lg:col-span-4">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-slate-200/60">
                <h2 class="font-medium text-base mr-auto">Distribusi Risiko</h2>
            </div>
            <div class="p-5">
                <div class="mb-4">
                    <h4 class="font-medium text-sm mb-2">Berdasarkan Kategori</h4>
                    @php
                        $categoryColors = $categoryColors ?? [
                            'Lingkungan' => '#10b981',
                            'Waktu' => '#f59e0b',
                            'Hukum' => '#ef4444',
                            'Sumber Daya Manusia' => '#3b82f6',
                            'Teknis' => '#8b5cf6',
                            'Keuangan' => '#ec4899',
                        ];
                    @endphp
                    
                    @foreach($categoryRiskDistribution as $category)
                    <div class="flex items-center mb-2">
                        <div class="w-3 h-3 rounded-full mr-2" 
                            style="background-color: {{ $categoryColors[$category->risk_category_name] ?? '#6c757d' }}"></div>
                        <div class="flex-1">
                            <div class="flex justify-between">
                                <span class="text-xs">{{ $category->risk_category_name }}</span>
                                <span class="text-xs font-medium">{{ $category->total }}</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1">
                                <div class="bg-primary rounded-full h-1.5" 
                                    style="width: {{ ($category->total / max($categoryRiskDistribution->sum('total'), 1)) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-6">
                    <h4 class="font-medium text-sm mb-2">Berdasarkan Organisasi</h4>
                    @foreach($orgRiskDistribution as $org)
                    <div class="flex items-center mb-2">
                        <div class="w-2 h-2 rounded-full mr-2 bg-warning"></div>
                        <div class="flex-1">
                            <div class="flex justify-between">
                                <span class="text-xs">{{ $org->organization_name }}</span>
                                <span class="text-xs font-medium">{{ $org->total }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function exportRiskMatrix() {
    // Implement export functionality
    alert('Export feature coming soon');
}

// Get category color
function getCategoryColor(categoryName) {
    const colors = {
        'Lingkungan': '#10b981',
        'Waktu': '#f59e0b',
        'Hukum': '#ef4444',
        'Sumber Daya Manusia': '#3b82f6',
        'Teknis': '#8b5cf6',
        'Keuangan': '#ec4899'
    };
    return colors[categoryName] || '#6c757d';
}

// Initialize charts
document.addEventListener('DOMContentLoaded', function() {
    // Initialize any charts here
    console.log('Admin dashboard initialized');
});
</script>
@endsection