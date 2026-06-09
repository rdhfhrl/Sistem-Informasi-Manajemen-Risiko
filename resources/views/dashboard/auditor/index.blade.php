@extends('layouts.master')

@section('title', 'Dashboard Auditor - SIMR')

@section('content')
@php
    use Carbon\Carbon;

    function getRiskColorForAuditor($level) {
        $colors = [
            'sangat_rendah' => '#28a745',
            'rendah' => '#ffc107',
            'sedang' => '#fd7e14',
            'tinggi' => '#dc3545',
            'sangat_tinggi' => '#721c24'
        ];
        return $colors[$level] ?? '#6c757d';
    }
@endphp

<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">
        Dashboard Auditor
        <span class="text-sm text-slate-500 font-normal ml-2">Auditor: {{ Auth::user()->name }}</span>
    </h2>
</div>

<!-- Debug Info -->
@if(env('APP_DEBUG') && false) {{-- Nonaktifkan untuk production --}}
<div class="alert alert-warning mt-5">
    <h4>Debug Info:</h4>
    <p>Stats exists: {{ isset($stats) ? 'Yes' : 'No' }}</p>
    <p>Risks count: {{ isset($risksNeedingEvaluation) ? $risksNeedingEvaluation->count() : 0 }}</p>
</div>
@endif

<!-- Auditor Statistics -->
@if(isset($stats) && is_array($stats))
<div class="grid grid-cols-12 gap-6 mt-5">
    <!-- Pending Evaluations -->
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <i data-feather="clock" class="report-box__icon text-warning"></i>
                </div>
                <div class="text-3xl font-medium leading-8 mt-6">{{ $stats['pending_evaluations'] ?? 0 }}</div>
                <div class="text-base text-slate-500 mt-1">Evaluasi Tertunda</div>
            </div>
        </div>
    </div>

    <!-- Completed Audits -->
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <i data-feather="check-square" class="report-box__icon text-success"></i>
                </div>
                <div class="text-3xl font-medium leading-8 mt-6">{{ $stats['completed_audits'] ?? 0 }}</div>
                <div class="text-base text-slate-500 mt-1">Audit Selesai</div>
            </div>
        </div>
    </div>

    <!-- High Risk Projects -->
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <i data-feather="alert-octagon" class="report-box__icon text-danger"></i>
                </div>
                <div class="text-3xl font-medium leading-8 mt-6">{{ $stats['high_risk_projects'] ?? 0 }}</div>
                <div class="text-base text-slate-500 mt-1">Proyek Berisiko Tinggi</div>
            </div>
        </div>
    </div>

    <!-- Compliance Rate -->
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <i data-feather="bar-chart-2" class="report-box__icon text-info"></i>
                </div>
                <div class="text-3xl font-medium leading-8 mt-6">{{ $stats['compliance_rate'] ?? 0 }}%</div>
                <div class="text-base text-slate-500 mt-1">Tingkat Kepatuhan</div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Risks Needing Evaluation -->
<div class="grid grid-cols-12 gap-6 mt-5">
    <!-- Risks Needing Evaluation -->
    <div class="col-span-12 lg:col-span-12">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-slate-200/60">
                <h2 class="font-medium text-base mr-auto">Risiko Perlu Evaluasi</h2>
                <a href="{{ route('risks.index') }}?filter=high_risk" class="btn btn-outline-secondary py-1 px-2">
                    Lihat Semua
                </a>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    @if(isset($risksNeedingEvaluation) && $risksNeedingEvaluation->count() > 0)
                        @foreach($risksNeedingEvaluation as $risk)
                        <div class="evaluation-item p-3 rounded-md border border-slate-200">
                            <div class="flex items-start">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3" 
                                    style="background-color: {{ getRiskColorForAuditor($risk->risk_level) }}20; color: {{ getRiskColorForAuditor($risk->risk_level) }}">
                                    <i data-feather="alert-triangle" class="w-4 h-4"></i>
                                </div>
                                <div class="flex-1">
                                    <!-- PERBAIKAN: Pastikan route memiliki parameter -->
                                    @if(isset($risk->risk_id))
                                    <a href="{{ route('risks.show', $risk->risk_id) }}" class="font-medium hover:text-primary">
                                        {{ $risk->risk_code }}
                                    </a>
                                    @else
                                    <span class="font-medium">{{ $risk->risk_code ?? 'N/A' }}</span>
                                    @endif
                                    
                                    <div class="text-slate-500 text-xs mt-0.5">
                                        {{ Str::limit($risk->risk_description ?? '', 70) }}
                                    </div>
                                    <div class="flex items-center mt-2">
                                        <span class="text-xs px-2 py-1 rounded-full mr-2" 
                                            style="background-color: {{ getRiskColorForAuditor($risk->risk_level) }}; color: white">
                                            {{ ucfirst(str_replace('_', ' ', $risk->risk_level ?? '')) }}
                                        </span>
                                        <span class="text-xs text-slate-500 mr-2">
                                            <i data-feather="briefcase" class="w-3 h-3 mr-1"></i>
                                            {{ $risk->project->pro_nama ?? 'Tidak ada proyek' }}
                                        </span>
                                    </div>
                                    <div class="mt-2">
                                        @if(isset($risk->risk_id))
                                        <a href="{{ route('risk-evaluations.create', ['riskId' => $risk->risk_id]) }}" 
                                        class="btn btn-primary btn-sm py-1 px-3 text-xs">
                                            <i data-feather="edit" class="w-3 h-3 mr-1"></i> Buat Evaluasi
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                    <div class="text-center py-4">
                        <i data-feather="check-circle" class="w-12 h-12 text-slate-300 mx-auto"></i>
                        <p class="text-slate-500 mt-2">Tidak ada risiko yang perlu evaluasi</p>
                        <a href="{{ route('risks.index') }}" class="btn btn-outline-primary btn-sm mt-2">
                            <i data-feather="search" class="w-3 h-3 mr-1"></i> Cari Risiko Lainnya
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="intro-y box mt-5">
    <div class="flex items-center p-5 border-b border-slate-200/60">
        <h2 class="font-medium text-base mr-auto">Menu Cepat</h2>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('risks.index') }}" class="btn btn-outline-primary w-full">
                <i data-feather="alert-triangle" class="w-4 h-4 mr-2"></i> Risiko
            </a>
            <a href="{{ route('audits.index') }}" class="btn btn-outline-success w-full">
                <i data-feather="clipboard" class="w-4 h-4 mr-2"></i> Audit
            </a>
            <a href="{{ route('risk-evaluations.index') }}" class="btn btn-outline-warning w-full">
                <i data-feather="edit" class="w-4 h-4 mr-2"></i> Evaluasi
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-info w-full">
                <i data-feather="file-text" class="w-4 h-4 mr-2"></i> Laporan
            </a>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function refreshDashboard() {
    location.reload();
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Auditor dashboard loaded');
});
</script>
@endsection