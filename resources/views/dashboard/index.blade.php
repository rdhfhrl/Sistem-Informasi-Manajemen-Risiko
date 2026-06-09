@extends('layouts.master')

@section('title', 'Dashboard - SIMR')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">
        Dashboard Sistem Informasi Manajemen Risiko
    </h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <button onclick="location.reload()" class="btn btn-primary shadow-md mr-2">
            <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i> Refresh
        </button>
    </div>
</div>

<!-- System Overview Stats -->
<div class="grid grid-cols-12 gap-6 mt-5">
    <!-- Total Risks -->
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <i data-feather="alert-triangle" class="report-box__icon text-primary"></i>
                </div>
                <div class="text-3xl font-medium leading-8 mt-6">{{ $stats['total_risks'] ?? 0 }}</div>
                <div class="text-base text-slate-500 mt-1">Total Risiko</div>
                <div class="mt-2 text-xs text-slate-500">
                    <i data-feather="trending-up" class="w-4 h-4 mr-1"></i> 
                    {{ $stats['average_risk_score'] ?? 0 }} Avg Score
                </div>
            </div>
        </div>
    </div>

    <!-- High Risks -->
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <i data-feather="alert-octagon" class="report-box__icon text-danger"></i>
                </div>
                <div class="text-3xl font-medium leading-8 mt-6">{{ $stats['high_risks'] ?? 0 }}</div>
                <div class="text-base text-slate-500 mt-1">Risiko Tinggi</div>
                <div class="mt-2 text-xs text-danger">
                    <i data-feather="alert-circle" class="w-4 h-4 mr-1"></i> 
                    Perlu Perhatian
                </div>
            </div>
        </div>
    </div>

    <!-- Active Projects -->
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <i data-feather="briefcase" class="report-box__icon text-info"></i>
                </div>
                <div class="text-3xl font-medium leading-8 mt-6">{{ $stats['active_projects'] ?? 0 }}</div>
                <div class="text-base text-slate-500 mt-1">Proyek Aktif</div>
                <div class="mt-2 text-xs text-info">
                    <i data-feather="users" class="w-4 h-4 mr-1"></i> 
                    {{ $stats['total_organizations'] ?? 0 }} Organisasi
                </div>
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <i data-feather="activity" class="report-box__icon text-success"></i>
                </div>
                <div class="text-3xl font-medium leading-8 mt-6">{{ $stats['risk_coverage'] ?? 0 }}%</div>
                <div class="text-base text-slate-500 mt-1">Cakupan Risiko</div>
                <div class="mt-2 text-xs text-success">
                    <i data-feather="check-circle" class="w-4 h-4 mr-1"></i> 
                    System Online
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Welcome Message -->
<div class="intro-y box mt-5">
    <div class="flex flex-col sm:flex-row items-center p-5">
        <div class="w-20 h-20 rounded-full overflow-hidden flex-shrink-0">
            <i data-feather="user" class="w-full h-full text-slate-300"></i>
        </div>
        <div class="ml-0 sm:ml-5 mt-3 sm:mt-0 text-center sm:text-left">
            <h2 class="font-medium text-lg">Selamat datang di SIMR</h2>
            <p class="text-slate-500 mt-1">
                Sistem Informasi Manajemen Risiko untuk optimalisasi pengelolaan proyek konstruksi di Dinas PUPR Provinsi Sumatera Utara
            </p>
        </div>
        <div class="sm:ml-auto mt-3 sm:mt-0">
            <a href="{{ route('dashboard.data') }}" class="btn btn-primary">Mulai Menggunakan</a>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="col-span-12 lg:col-span-4">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-slate-200/60">
                <h2 class="font-medium text-base mr-auto">Untuk Administrator</h2>
                <i data-feather="settings" class="w-5 h-5 text-slate-500"></i>
            </div>
            <div class="p-5">
                <ul class="space-y-2">
                    <li><a href="{{ route('users.index') }}" class="flex items-center text-primary"><i data-feather="users" class="w-4 h-4 mr-2"></i> Kelola Pengguna</a></li>
                    <li><a href="{{ route('organizations.index') }}" class="flex items-center text-primary"><i data-feather="home" class="w-4 h-4 mr-2"></i> Organisasi</a></li>
                    <li><a href="{{ route('system-logs.index') }}" class="flex items-center text-primary"><i data-feather="activity" class="w-4 h-4 mr-2"></i> System Logs</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-span-12 lg:col-span-4">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-slate-200/60">
                <h2 class="font-medium text-base mr-auto">Untuk Unit Pemilik Risiko</h2>
                <i data-feather="alert-triangle" class="w-5 h-5 text-slate-500"></i>
            </div>
            <div class="p-5">
                <ul class="space-y-2">
                    <li><a href="{{ route('risks.index') }}" class="flex items-center text-primary"><i data-feather="list" class="w-4 h-4 mr-2"></i> Data Risiko</a></li>
                    <li><a href="{{ route('risk-identifications.create') }}" class="flex items-center text-primary"><i data-feather="search" class="w-4 h-4 mr-2"></i> Identifikasi Risiko</a></li>
                    <li><a href="{{ route('risk-mitigations.index') }}" class="flex items-center text-primary"><i data-feather="shield" class="w-4 h-4 mr-2"></i> Mitigasi Risiko</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-span-12 lg:col-span-4">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-slate-200/60">
                <h2 class="font-medium text-base mr-auto">Untuk Auditor</h2>
                <i data-feather="check-square" class="w-5 h-5 text-slate-500"></i>
            </div>
            <div class="p-5">
                <ul class="space-y-2">
                    <li><a href="{{ route('risk-evaluations.index') }}" class="flex items-center text-primary"><i data-feather="star" class="w-4 h-4 mr-2"></i> Evaluasi Risiko</a></li>
                    <li><a href="{{ route('audits.index') }}" class="flex items-center text-primary"><i data-feather="check-circle" class="w-4 h-4 mr-2"></i> Audit Risiko</a></li>
                    <li><a href="{{ route('reports.index') }}" class="flex items-center text-primary"><i data-feather="file-text" class="w-4 h-4 mr-2"></i> Laporan</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="intro-y box mt-5">
    <div class="flex items-center p-5 border-b border-slate-200/60">
        <h2 class="font-medium text-base mr-auto">Aktivitas Terbaru Sistem</h2>
        <a href="{{ route('dashboard.activities') }}" class="btn btn-outline-secondary py-1 px-2">
            Lihat Semua
        </a>
    </div>
    <div class="p-5">
        <div class="activity-feed">
            @foreach($recentActivities as $activity)
            <div class="flex items-start mt-4 first:mt-0">
                <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3" 
                     style="background-color: {{ $activity['color'] }}20;">
                    <i data-feather="{{ $activity['icon'] }}" 
                       style="color: {{ $activity['color'] }}"
                       class="w-4 h-4"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium">{{ $activity['title'] }}</div>
                    <div class="text-slate-500 text-xs mt-0.5">{{ $activity['description'] }}</div>
                    <div class="text-slate-400 text-xs mt-1">
                        <i data-feather="clock" class="w-3 h-3 mr-1"></i> {{ $activity['time'] }}
                    </div>
                </div>
            </div>
            @endforeach
            
            @if(count($recentActivities) === 0)
            <div class="text-center py-8">
                <i data-feather="activity" class="w-12 h-12 text-slate-300 mx-auto"></i>
                <p class="text-slate-500 mt-3">Tidak ada aktivitas terbaru</p>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Initialize feather icons
document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection