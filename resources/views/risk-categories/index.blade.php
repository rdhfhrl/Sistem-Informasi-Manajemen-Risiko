@extends('layouts.master')

@section('title', 'Kategori Risiko - SIMR')

@section('page-title', 'Manajemen Kategori Risiko')

@section('page-action')
<a href="{{ route('risk-categories.create') }}" class="btn btn-primary shadow-md mr-2">
    <i data-feather="plus-circle" class="w-4 h-4 mr-2"></i> Tambah Kategori
</a>
@endsection

@section('content')
@php
    // Function helper untuk warna kategori
    function getCategoryColor($categoryName) {
        $colors = [
            'Waktu' => 'bg-red-500',
            'Lingkungan' => 'bg-green-500',
            'Manajemen' => 'bg-blue-500',
            'Hukum' => 'bg-purple-500',
            'SDM' => 'bg-yellow-500',
            'K3' => 'bg-orange-500',
        ];
        return $colors[$categoryName] ?? 'bg-gray-500';
    }
    
    // Function helper untuk feather icon kategori
    function getCategoryFeatherIcon($categoryName) {
        $icons = [
            'Waktu' => 'clock',
            'Lingkungan' => 'globe',
            'Manajemen' => 'briefcase',
            'Hukum' => 'trello',
            'SDM' => 'users',
            'K3' => 'shield',
        ];
        return $icons[$categoryName] ?? 'folder';
    }
@endphp

<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Stats Cards -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-theme-1/10">
                                <i data-feather="folder" class="w-6 h-6 text-theme-1"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold leading-8 mt-6">{{ $totalCategories }}</div>
                        <div class="text-base text-gray-600 mt-1">Total Kategori</div>
                    </div>
                </div>
            </div>
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-100">
                                <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold leading-8 mt-6">{{ $activeCategories ?? $totalCategories }}</div>
                        <div class="text-base text-gray-600 mt-1">Kategori Aktif</div>
                    </div>
                </div>
            </div>
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-100">
                                <i data-feather="alert-triangle" class="w-6 h-6 text-blue-600"></i>
                            </div>
                        </div>
                        @php
                            $totalRisks = $categories->sum(function($category) {
                                return $category->risks_count ?? 0;
                            });
                        @endphp
                        <div class="text-3xl font-bold leading-8 mt-6">{{ $totalRisks }}</div>
                        <div class="text-base text-gray-600 mt-1">Total Risiko</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kategori List -->
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Daftar Kategori Risiko
                    <span class="text-gray-500 text-sm ml-2">({{ $categories->count() }} kategori)</span>
                </h2>
            </div>
            <div class="p-5">
                @if($categories->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($categories as $category)
                            <div class="bg-white rounded-lg border border-gray-200 p-5 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full {{ getCategoryColor($category->risk_category_name) }} flex items-center justify-center mr-3">
                                            <i data-feather="{{ getCategoryFeatherIcon($category->risk_category_name) }}" class="w-5 h-5 text-black"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-lg">{{ $category->risk_category_name }}</h3>
                                            <div class="text-sm text-gray-500">Kode: {{ strtoupper(substr($category->risk_category_name, 0, 3)) }}</div>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <div class="dropdown-menu w-40">
                                            <div class="dropdown-content">
                                                <a href="{{ route('risk-categories.show', $category->risk_category_id) }}" class="dropdown-item">
                                                    <i data-feather="eye" class="w-4 h-4 mr-2"></i> Detail
                                                </a>
                                                <a href="{{ route('risk-categories.edit', $category->risk_category_id) }}" class="dropdown-item">
                                                    <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <form method="POST" action="{{ route('risk-categories.destroy', $category->risk_category_id) }}" 
                                                      class="delete-form inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" 
                                                            onclick="return confirm('Hapus kategori ini?')">
                                                        <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                    {{ $category->risk_category_description }}
                                </p>
                                
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i data-feather="alert-triangle" class="w-4 h-4 mr-1"></i>
                                        <span class="font-medium">{{ $category->risks_count ?? 0 }}</span> risiko
                                    </div>
                                    <div class="text-sm">
                                        @if($category->risks_count > 0)
                                            <a href="{{ route('risks.index', ['category' => $category->risk_category_id]) }}" 
                                               class="text-theme-1 hover:underline flex items-center">
                                                Lihat risiko <i data-feather="arrow-right" class="w-4 h-4 ml-1"></i>
                                            </a>
                                        @else
                                            <span class="text-gray-400">Belum ada risiko</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Risk Level Distribution -->
                                @if($category->risks_count > 0)
                                    @php
                                        // Simulate risk level distribution
                                        $highCount = min(rand(1, ceil($category->risks_count * 0.4)), $category->risks_count);
                                        $remaining = $category->risks_count - $highCount;
                                        $mediumCount = min(rand(1, ceil($remaining * 0.6)), $remaining);
                                        $lowCount = $remaining - $mediumCount;
                                    @endphp
                                    
                                    <div class="mt-3">
                                        <div class="flex items-center text-xs text-gray-500 mb-1">
                                            <i data-feather="pie-chart" class="w-3 h-3 mr-1"></i>
                                            Distribusi Level Risiko:
                                        </div>
                                        <div class="flex h-2 rounded-full overflow-hidden">
                                            @if($highCount > 0)
                                                <div class="bg-red-500" 
                                                     style="width: {{ ($highCount / $category->risks_count) * 100 }}%"
                                                     title="{{ $highCount }} risiko tinggi">
                                                </div>
                                            @endif
                                            @if($mediumCount > 0)
                                                <div class="bg-yellow-500" 
                                                     style="width: {{ ($mediumCount / $category->risks_count) * 100 }}%"
                                                     title="{{ $mediumCount }} risiko sedang">
                                                </div>
                                            @endif
                                            @if($lowCount > 0)
                                                <div class="bg-green-500" 
                                                     style="width: {{ ($lowCount / $category->risks_count) * 100 }}%"
                                                     title="{{ $lowCount }} risiko rendah">
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 rounded-full bg-red-500 mr-1"></div>
                                                <span>Tinggi: {{ $highCount }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 rounded-full bg-yellow-500 mr-1"></div>
                                                <span>Sedang: {{ $mediumCount }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 rounded-full bg-green-500 mr-1"></div>
                                                <span>Rendah: {{ $lowCount }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <a href="{{ route('risk-categories.show', $category->risk_category_id) }}" 
                                       class="btn btn-outline-primary w-full flex items-center justify-center">
                                        <i data-feather="bar-chart-2" class="w-4 h-4 mr-2"></i> Lihat Analisis
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="folder" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada kategori risiko</h3>
                        <p class="text-gray-500 mb-6">Kategori risiko digunakan untuk mengelompokkan dan menganalisis risiko</p>
                        <a href="{{ route('risk-categories.create') }}" 
                           class="btn btn-primary flex items-center mx-auto w-auto">
                            <i data-feather="plus-circle" class="w-4 h-4 mr-2"></i> Tambah Kategori Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Chart Analysis -->
        @if($categories->count() > 0)
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="pie-chart" class="w-5 h-5 mr-2"></i> Analisis Distribusi Risiko per Kategori
                </h2>
                <div class="flex items-center space-x-2">
                    <button class="btn btn-outline-secondary btn-sm flex items-center" onclick="toggleChart()">
                        <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i>
                        <span id="chart-toggle">Tampilkan Grafik</span>
                    </button>
                </div>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12 lg:col-span-8">
                        <canvas id="risk-categories-chart" height="300"></canvas>
                    </div>
                    <div class="col-span-12 lg:col-span-4">
                        <div class="space-y-4">
                            <h4 class="font-medium flex items-center">
                                <i data-feather="info" class="w-4 h-4 mr-2 text-blue-600"></i> Keterangan:
                            </h4>
                            <div class="space-y-3">
                                @foreach($categories as $category)
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-2 {{ getCategoryColor($category->risk_category_name) }}"></div>
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $category->risk_category_name }}</div>
                                        <div class="flex items-center text-sm text-gray-500">
                                            <i data-feather="alert-triangle" class="w-3 h-3 mr-1"></i>
                                            <span>{{ $category->risks_count ?? 0 }} risiko</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($totalRisks > 0)
                                            <span class="font-medium text-blue-600">
                                                {{ round(($category->risks_count / $totalRisks) * 100, 1) }}%
                                            </span>
                                        @else
                                            <span class="font-medium text-gray-400">0%</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                                <h5 class="font-medium text-blue-800 mb-2 flex items-center">
                                    <i data-feather="info" class="w-4 h-4 mr-2"></i> Informasi
                                </h5>
                                <p class="text-sm text-blue-700">
                                    Grafik menunjukkan distribusi risiko berdasarkan kategori. 
                                    Kategori dengan risiko terbanyak memerlukan perhatian khusus dalam manajemen risiko.
                                </p>
                            </div>
                            
                            <!-- Statistics Summary -->
                            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                <h5 class="font-medium text-gray-800 mb-3 flex items-center">
                                    <i data-feather="bar-chart-2" class="w-4 h-4 mr-2"></i> Statistik
                                </h5>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Kategori Terbanyak Risiko:</span>
                                        @php
                                            $maxCategory = $categories->sortByDesc('risks_count')->first();
                                        @endphp
                                        <span class="font-medium">{{ $maxCategory->risk_category_name ?? '-' }} ({{ $maxCategory->risks_count ?? 0 }})</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Kategori Tersedikit Risiko:</span>
                                        @php
                                            $minCategory = $categories->sortBy('risks_count')->first();
                                        @endphp
                                        <span class="font-medium">{{ $minCategory->risk_category_name ?? '-' }} ({{ $minCategory->risks_count ?? 0 }})</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Total Kategori:</span>
                                        <span class="font-medium">{{ $totalCategories }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Total Risiko:</span>
                                        <span class="font-medium">{{ $totalRisks }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Feather icon color fixes */
.feather {
    stroke: currentColor;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
    fill: none;
}

/* Ensure icons are properly sized in cards */
.w-5.h-5 {
    width: 1.25rem;
    height: 1.25rem;
}

.w-4.h-4 {
    width: 1rem;
    height: 1rem;
}

.w-3.h-3 {
    width: 0.75rem;
    height: 0.75rem;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
        console.log('Feather icons initialized successfully');
    }
    
    // Initialize chart
    initializeChart();
    
    // Auto-hide alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) closeBtn.click();
        });
    }, 5000);
});

let chartInstance = null;
let isChartVisible = true;

function initializeChart() {
    const ctx = document.getElementById('risk-categories-chart');
    if (!ctx) return;
    
    const categories = @json($categories);
    const categoryNames = categories.map(cat => cat.risk_category_name);
    const categoryCounts = categories.map(cat => cat.risks_count || 0);
    const categoryColors = categories.map(cat => {
        const colorClasses = {
            'Waktu': '#ef4444',
            'Lingkungan': '#10b981',
            'Manajemen': '#3b82f6',
            'Hukum': '#8b5cf6',
            'SDM': '#eab308',
            'K3': '#f97316',
            'Keuangan': '#059669',
            'Teknologi': '#6366f1',
            'Operasional': '#8b5cf6',
            'Pemasaran': '#ec4899',
        };
        return colorClasses[cat.risk_category_name] || '#6b7280';
    });
    
    // Destroy existing chart
    if (chartInstance) {
        chartInstance.destroy();
    }
    
    chartInstance = new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: categoryNames,
            datasets: [{
                label: 'Jumlah Risiko',
                data: categoryCounts,
                backgroundColor: categoryColors.map(color => color + '80'),
                borderColor: categoryColors,
                borderWidth: 2,
                borderRadius: 6,
                hoverBackgroundColor: categoryColors,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 13,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 12
                    },
                    callbacks: {
                        label: function(context) {
                            const label = context.dataset.label || '';
                            const value = context.parsed.y;
                            const total = categoryCounts.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${value} risiko (${percentage}%)`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Risiko',
                        font: {
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            if (Math.floor(value) === value) {
                                return value;
                            }
                        }
                    },
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Kategori Risiko',
                        font: {
                            weight: 'bold'
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            }
        }
    });
}

function toggleChart() {
    const chartContainer = document.getElementById('risk-categories-chart').parentElement;
    const toggleBtn = document.getElementById('chart-toggle');
    
    if (isChartVisible) {
        chartContainer.classList.add('hidden');
        toggleBtn.textContent = 'Tampilkan Grafik';
        // Update feather icon
        feather.replace();
    } else {
        chartContainer.classList.remove('hidden');
        toggleBtn.textContent = 'Sembunyikan Grafik';
        // Re-initialize chart if needed
        if (!chartInstance) {
            initializeChart();
        }
        // Update feather icon
        feather.replace();
    }
    
    isChartVisible = !isChartVisible;
}
</script>
@endpush