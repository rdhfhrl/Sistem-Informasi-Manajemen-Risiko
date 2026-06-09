@extends('layouts.master')

@section('title', 'Detail Audit - SIMR')

@section('page-title', 'Detail Audit')

@section('breadcrumb')
@parent
<li class="breadcrumb-item"><a href="{{ route('audits.index') }}">Audit</a></li>
<li class="breadcrumb-item active">Detail</li>
@endsection

@section('page-action')
<a href="{{ route('audits.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
</a>
<div class="flex items-center space-x-2">
    <a href="{{ route('audits.edit', $audit->audit_id) }}" 
       class="btn btn-primary shadow-md">
        <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit
    </a>
    <form action="{{ route('audits.destroy', $audit->audit_id) }}" 
          method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus audit ini?')">
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
                        <i data-feather="clipboard" class="w-5 h-5 inline mr-2 text-blue-500"></i>
                        Detail Audit
                    </h2>
                    <div class="text-gray-600 text-sm mt-1">
                        Tanggal Audit: <span class="font-medium">
                            {{ \Carbon\Carbon::parse($audit->audit_date)->format('d F Y') }}
                        </span>
                    </div>
                </div>
                <div class="mt-3 sm:mt-0">
                    <span class="px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <i data-feather="user-check" class="w-4 h-4 inline mr-2"></i>
                        Auditor: {{ $audit->auditor }}
                    </span>
                </div>
            </div>
            
            <!-- Risk Info -->
            @if($audit->risk)
            <div class="p-5 bg-gradient-to-r from-blue-50 to-blue-100 border-b">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                        <i data-feather="alert-triangle" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-bold text-lg">{{ $audit->risk->risk_code }}</div>
                        <div class="text-gray-600">{{ $audit->risk->risk_description }}</div>
                        <div class="text-sm text-gray-500 mt-1">
                            @if($audit->risk->risk_level)
                                <span class="px-2 py-1 rounded-full text-xs font-medium 
                                    @if($audit->risk->risk_level == 'sangat_rendah') bg-green-100 text-green-800
                                    @elseif($audit->risk->risk_level == 'rendah') bg-blue-100 text-blue-800
                                    @elseif($audit->risk->risk_level == 'sedang') bg-yellow-100 text-yellow-800
                                    @elseif($audit->risk->risk_level == 'tinggi') bg-orange-100 text-orange-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    Level: {{ $audit->risk->risk_level_label }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <a href="{{ route('risks.show', $audit->risk->risk_id) }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i data-feather="external-link" class="w-4 h-4 mr-2"></i> Lihat Risiko
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Main Information Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Audit Report -->
                @if($audit->audit_report)
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="file-text" class="w-5 h-5 mr-2 text-teal-500"></i>
                            Ringkasan Laporan Audit
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="prose max-w-none">
                            {!! nl2br(e($audit->audit_report)) !!}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Audit Findings -->
                @if($audit->audit_findings)
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200 bg-orange-50">
                        <h3 class="font-medium text-base text-orange-700">
                            <i data-feather="alert-circle" class="w-5 h-5 mr-2"></i>
                            Temuan Audit
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="prose max-w-none">
                            {!! nl2br(e($audit->audit_findings)) !!}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Audit Recommendations -->
                @if($audit->audit_recommendations)
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200 bg-green-50">
                        <h3 class="font-medium text-base text-green-700">
                            <i data-feather="check-circle" class="w-5 h-5 mr-2"></i>
                            Rekomendasi Audit
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="prose max-w-none">
                            {!! nl2br(e($audit->audit_recommendations)) !!}
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Audit Information -->
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="info" class="w-5 h-5 mr-2 text-purple-500"></i>
                            Informasi Audit
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="space-y-4">
                            <div>
                                <label class="form-label">Auditor</label>
                                <div class="font-medium">{{ $audit->auditor }}</div>
                            </div>
                            
                            <div>
                                <label class="form-label">Tanggal Audit</label>
                                <div class="font-medium">
                                    {{ \Carbon\Carbon::parse($audit->audit_date)->format('d F Y') }}
                                </div>
                                <div class="text-gray-500 text-sm">
                                    ({{ \Carbon\Carbon::parse($audit->audit_date)->diffForHumans() }})
                                </div>
                            </div>
                            
                            @if($audit->risk)
                            <div>
                                <label class="form-label">Risiko Terkait</label>
                                <div class="font-medium">{{ $audit->risk->risk_code }}</div>
                                <div class="text-gray-500 text-sm">
                                    {{ Str::limit($audit->risk->risk_description, 40) }}
                                </div>
                            </div>
                            @endif
                            
                            <div>
                                <label class="form-label">Status</label>
                                <div>
                                    @if($audit->audit_findings)
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <i data-feather="alert-circle" class="w-3 h-3 inline mr-1"></i>
                                            Ada Temuan
                                        </span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i data-feather="check-circle" class="w-3 h-3 inline mr-1"></i>
                                            Tidak Ada Temuan
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="zap" class="w-5 h-5 mr-2 text-yellow-500"></i>
                            Aksi Cepat
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="space-y-3">
                            <a href="{{ route('audits.edit', $audit->audit_id) }}" 
                               class="btn btn-primary w-full">
                                <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit Audit
                            </a>
                            
                            <a href="{{ route('audits.index') }}" 
                               class="btn btn-outline-secondary w-full">
                                <i data-feather="list" class="w-4 h-4 mr-2"></i> Kembali ke Daftar
                            </a>
                            
                            @if($audit->risk)
                            <a href="{{ route('risks.show', $audit->risk->risk_id) }}" 
                               class="btn btn-outline-blue w-full">
                                <i data-feather="external-link" class="w-4 h-4 mr-2"></i> Lihat Risiko
                            </a>
                            @endif
                            
                            <form action="{{ route('audits.destroy', $audit->audit_id) }}" 
                                  method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus audit ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-full">
                                    <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus Audit
                                </button>
                            </form>
                        </div>
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
                                <label class="form-label">ID Audit</label>
                                <div class="font-mono text-sm text-gray-600">{{ $audit->audit_id }}</div>
                            </div>
                            
                            <div>
                                <label class="form-label">Dibuat Pada</label>
                                <div class="text-sm text-gray-600">
                                    {{ $audit->created_at->format('d M Y H:i') }}
                                </div>
                            </div>
                            
                            <div>
                                <label class="form-label">Diperbarui Pada</label>
                                <div class="text-sm text-gray-600">
                                    {{ $audit->updated_at->format('d M Y H:i') }}
                                </div>
                            </div>
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
});
</script>
@endpush