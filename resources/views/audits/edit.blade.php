@extends('layouts.master')

@section('title', 'Edit Audit - SIMR')

@section('page-title', 'Edit Audit')

@section('breadcrumb')
@parent
<li class="breadcrumb-item"><a href="{{ route('audits.index') }}">Audit</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-action')
<a href="{{ route('audits.show', $audit->audit_id) }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Header Info -->
        <div class="intro-y box mb-6">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <div class="flex-1">
                    <h2 class="text-lg font-bold">
                        <i data-feather="edit-2" class="w-5 h-5 inline mr-2 text-yellow-500"></i>
                        Edit Audit
                    </h2>
                    <div class="text-gray-600 text-sm mt-1">
                        Tanggal Audit: <span class="font-medium">
                            {{ \Carbon\Carbon::parse($audit->audit_date)->format('d F Y') }}
                        </span> • 
                        Auditor: <span class="font-medium">{{ $audit->auditor }}</span>
                    </div>
                </div>
                <div class="mt-3 sm:mt-0">
                    @if($audit->audit_findings)
                        <span class="px-4 py-2 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                            <i data-feather="alert-circle" class="w-4 h-4 inline mr-2"></i>
                            Ada Temuan
                        </span>
                    @else
                        <span class="px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i data-feather="check-circle" class="w-4 h-4 inline mr-2"></i>
                            Tidak Ada Temuan
                        </span>
                    @endif
                </div>
            </div>
            
            <!-- Audit Info Summary -->
            <div class="p-5 bg-gradient-to-r from-gray-50 to-gray-100">
                <div class="text-center">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm text-gray-600 mb-2">TANGGAL AUDIT</div>
                            <div class="text-3xl font-bold text-blue-600">
                                {{ \Carbon\Carbon::parse($audit->audit_date)->format('d M Y') }}
                            </div>
                        </div>
                        
                        <div>
                            <div class="text-sm text-gray-600 mb-2">AUDITOR</div>
                            <div class="text-2xl font-bold text-green-600">{{ $audit->auditor }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="edit" class="w-5 h-5 mr-2 text-green-500"></i>
                    Form Edit Audit
                </h2>
            </div>
            
            <form action="{{ route('audits.update', $audit->audit_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Risk Selection -->
                            <div>
                                <label for="risk_id" class="form-label">Risiko Terkait (Opsional)</label>
                                <select id="risk_id" 
                                        name="risk_id" 
                                        class="form-select w-full @error('risk_id') border-red-500 @enderror">
                                    <option value="">Pilih Risiko (Opsional)</option>
                                    @foreach($risks as $risk)
                                        <option value="{{ $risk->risk_id }}" {{ old('risk_id', $audit->risk_id) == $risk->risk_id ? 'selected' : '' }}>
                                            {{ $risk->risk_code }} - {{ Str::limit($risk->risk_description, 50) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('risk_id')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Auditor -->
                            <div>
                                <label for="auditor" class="form-label">Nama Auditor <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       id="auditor" 
                                       name="auditor" 
                                       class="form-control w-full @error('auditor') border-red-500 @enderror" 
                                       value="{{ old('auditor', $audit->auditor) }}"
                                       required>
                                @error('auditor')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Audit Date -->
                            <div>
                                <label for="audit_date" class="form-label">Tanggal Audit <span class="text-red-500">*</span></label>
                                <input type="date" 
                                       id="audit_date" 
                                       name="audit_date" 
                                       class="form-control w-full @error('audit_date') border-red-500 @enderror" 
                                       value="{{ old('audit_date', $audit->audit_date) }}"
                                       required>
                                @error('audit_date')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Audit Report -->
                            <div>
                                <label for="audit_report" class="form-label">Ringkasan Laporan</label>
                                <textarea id="audit_report" 
                                          name="audit_report" 
                                          class="form-control w-full @error('audit_report') border-red-500 @enderror" 
                                          rows="5">{{ old('audit_report', $audit->audit_report) }}</textarea>
                                @error('audit_report')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Audit Findings -->
                    <div class="mt-6">
                        <label for="audit_findings" class="form-label">Temuan Audit</label>
                        <textarea id="audit_findings" 
                                  name="audit_findings" 
                                  class="form-control w-full @error('audit_findings') border-red-500 @enderror" 
                                  rows="4">{{ old('audit_findings', $audit->audit_findings) }}</textarea>
                        @error('audit_findings')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Audit Recommendations -->
                    <div class="mt-6">
                        <label for="audit_recommendations" class="form-label">Rekomendasi Audit</label>
                        <textarea id="audit_recommendations" 
                                  name="audit_recommendations" 
                                  class="form-control w-full @error('audit_recommendations') border-red-500 @enderror" 
                                  rows="4">{{ old('audit_recommendations', $audit->audit_recommendations) }}</textarea>
                        @error('audit_recommendations')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
                        <div>
                            <a href="{{ route('audits.show', $audit->audit_id) }}" 
                               class="btn btn-outline-secondary w-32">
                                <i data-feather="x" class="w-4 h-4 mr-2"></i> Batal
                            </a>
                        </div>
                        <div class="flex space-x-3">
                            <button type="reset" class="btn btn-outline-secondary w-32">
                                <i data-feather="refresh-ccw" class="w-4 h-4 mr-2"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary w-32">
                                <i data-feather="save" class="w-4 h-4 mr-2"></i> Update
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- System Info -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="info" class="w-5 h-5 mr-2 text-gray-500"></i>
                    Informasi Sistem
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <div class="text-gray-600 text-sm mb-1">ID Audit</div>
                        <div class="font-mono text-gray-700">{{ $audit->audit_id }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Dibuat Pada</div>
                        <div class="font-medium">{{ $audit->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Diperbarui Pada</div>
                        <div class="font-medium">{{ $audit->updated_at->format('d M Y H:i') }}</div>
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