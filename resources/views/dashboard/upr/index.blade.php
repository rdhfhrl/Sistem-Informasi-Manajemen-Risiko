@extends('layouts.master')

@section('title', 'Dashboard UPR - SIMR')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">
        Dashboard Unit Pemilik Risiko
        <span class="text-sm text-slate-500 font-normal ml-2">Selamat datang, {{ Auth::user()->name }}</span>
    </h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <a href="{{ route('risks.create') }}" 
            class="btn btn-success shadow-md">
            <i data-feather="plus" class="w-4 h-4 mr-2"></i> Buat Risiko Baru
        </a>
    </div>
</div>

<!-- UPR Statistics -->
<div class="grid grid-cols-12 gap-6 mt-5">
    <!-- My Projects -->
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <i data-feather="briefcase" class="report-box__icon text-primary"></i>
                </div>
                <div class="text-3xl font-medium leading-8 mt-6">{{ $stats['my_projects'] ?? 0 }}</div>
                <div class="text-base text-slate-500 mt-1">Proyek Saya</div>
                <div class="mt-2 text-xs text-slate-500">
                    <i data-feather="clock" class="w-4 h-4 mr-1"></i> 
                    {{ $stats['ongoing_projects'] ?? 0 }} Berjalan
                </div>
                <div class="mt-4">
                    <a href="{{ route('projects.index') }}" class="text-primary font-medium">
                        Lihat Proyek <i data-feather="arrow-right" class="w-4 h-4 ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- My Risks -->
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <i data-feather="alert-triangle" class="report-box__icon text-danger"></i>
                    @if($stats['high_risks'] ?? 0 > 0)
                        <div class="report-box__indicator bg-danger tooltip cursor-pointer ml-auto" 
                            title="{{ $stats['high_risks'] }} risiko tinggi">
                            {{ $stats['high_risks'] }}
                        </div>
                    @endif
                </div>
                <div class="text-3xl font-medium leading-8 mt-6">{{ $stats['my_risks'] ?? 0 }}</div>
                <div class="text-base text-slate-500 mt-1">Risiko Saya</div>
                <div class="mt-2 text-xs text-danger">
                    <i data-feather="alert-circle" class="w-4 h-4 mr-1"></i> 
                    {{ $stats['high_risks'] ?? 0 }} Tinggi
                </div>
                <div class="mt-4">
                    <a href="{{ route('risks.index') }}" class="text-danger font-medium">
                        Lihat Risiko <i data-feather="arrow-right" class="w-4 h-4 ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Mitigations -->
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <i data-feather="shield" class="report-box__icon text-warning"></i>
                </div>
                <div class="text-3xl font-medium leading-8 mt-6">{{ $stats['pending_mitigations'] ?? 0 }}</div>
                <div class="text-base text-slate-500 mt-1">Mitigasi Tertunda</div>
                <div class="mt-2 text-xs text-warning">
                    <i data-feather="calendar" class="w-4 h-4 mr-1"></i> 
                    {{ $stats['overdue_mitigations'] ?? 0 }} Terlambat
                </div>
                <div class="mt-4">
                    <a href="{{ route('risk-mitigations.index') }}" class="text-warning font-medium">
                        Lihat Mitigasi <i data-feather="arrow-right" class="w-4 h-4 ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Monitoring -->
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <i data-feather="eye" class="report-box__icon text-success"></i>
                </div>
                <div class="text-3xl font-medium leading-8 mt-6">{{ $stats['upcoming_monitoring'] ?? 0 }}</div>
                <div class="text-base text-slate-500 mt-1">Monitoring Mendatang</div>
                <div class="mt-2 text-xs text-success">
                    <i data-feather="calendar" class="w-4 h-4 mr-1"></i> 
                    {{ $stats['today_monitoring'] ?? 0 }} Hari Ini
                </div>
                <div class="mt-4">
                    <a href="{{ route('risk-monitorings.index') }}" class="text-success font-medium">
                        Lihat Monitoring <i data-feather="arrow-right" class="w-4 h-4 ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- My Projects & Risks -->
<div class="grid grid-cols-12 gap-6 mt-5">
    <!-- My Projects -->
    <div class="col-span-12 lg:col-span-6">
        @include('dashboard.upr.components.my-projects', ['myProjects' => $myProjects])
    </div>

    <!-- My Recent Risks -->
    <div class="col-span-12 lg:col-span-6">
        @include('dashboard.upr.components.my-risks', ['myRecentRisks' => $myRecentRisks])
    </div>
</div>

<!-- Updates & Critical Tasks -->
<div class="grid grid-cols-12 gap-6 mt-5">
    <!-- Updates -->
    <div class="col-span-12 lg:col-span-6">
        @include('dashboard.upr.partials.updates', ['updates' => $updates])
    </div>

    <!-- Critical Mitigations -->
    <div class="col-span-12 lg:col-span-6">
        @include('dashboard.upr.components.mitigation-tasks', ['criticalMitigations' => $criticalMitigations])
    </div>
</div>

<!-- Quick Links -->
@include('dashboard.upr.partials.quick-links')

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let riskTrendChart;

function refreshDashboard() {
    location.reload();
}

function updateRiskTrend() {
    const period = document.getElementById('trendPeriod').value;
    // Implement AJAX call to get trend data
    console.log('Updating trend for period:', period);
    
    // Example data (replace with actual API call)
    const data = {
        labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
        datasets: [{
            label: 'Total Risiko',
            data: [5, 8, 12, 10],
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
        }, {
            label: 'Risiko Tinggi',
            data: [1, 2, 4, 3],
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4
        }]
    };
    
    if (riskTrendChart) {
        riskTrendChart.data = data;
        riskTrendChart.update();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize risk trend chart
    const ctx = document.getElementById('riskTrendChart').getContext('2d');
    
    riskTrendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
            datasets: [{
                label: 'Total Risiko',
                data: [5, 8, 12, 10],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Risiko Tinggi',
                data: [1, 2, 4, 3],
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Risiko'
                    }
                }
            }
        }
    });
    
    console.log('UPR dashboard initialized');
});
</script>
@endsection