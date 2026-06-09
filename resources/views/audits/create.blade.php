@extends('layouts.master')

@section('title', 'Tambah Audit - SIMR')

@section('page-title', 'Tambah Audit')

@section('breadcrumb')
@parent
<li class="breadcrumb-item"><a href="{{ route('audits.index') }}">Audit</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endsection

@section('page-action')
<a href="{{ route('audits.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Form Card -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="clipboard" class="w-5 h-5 mr-2 text-green-500"></i>
                    Form Tambah Audit
                </h2>
            </div>
            
            <form action="{{ route('audits.store') }}" method="POST">
                @csrf
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
                                        <option value="{{ $risk->risk_id }}" {{ old('risk_id') == $risk->risk_id ? 'selected' : '' }}>
                                            {{ $risk->risk_code }} - {{ Str::limit($risk->risk_description, 50) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('risk_id')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                                <div class="text-gray-500 text-xs mt-1">
                                    Pilih risiko jika audit terkait dengan risiko tertentu
                                </div>
                            </div>

                            <!-- Auditor -->
                            <div>
                                <label for="auditor" class="form-label">Nama Auditor <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       id="auditor" 
                                       name="auditor" 
                                       class="form-control w-full @error('auditor') border-red-500 @enderror" 
                                       value="{{ old('auditor') }}"
                                       placeholder="Nama auditor yang melakukan audit"
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
                                       value="{{ old('audit_date', date('Y-m-d')) }}"
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
                                          rows="5"
                                          placeholder="Ringkasan hasil audit">{{ old('audit_report') }}</textarea>
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
                                  rows="4"
                                  placeholder="Tuliskan temuan-temuan dari audit">{{ old('audit_findings') }}</textarea>
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
                                  rows="4"
                                  placeholder="Rekomendasi perbaikan berdasarkan temuan">{{ old('audit_recommendations') }}</textarea>
                        @error('audit_recommendations')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('audits.index') }}" 
                           class="btn btn-outline-secondary w-32 mr-3">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary w-32">
                            <i data-feather="save" class="w-4 h-4 mr-2"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Help Card -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="help-circle" class="w-5 h-5 mr-2 text-purple-500"></i>
                    Panduan Mengisi Form Audit
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i data-feather="clipboard" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <h3 class="font-medium text-blue-700">Temuan Audit</h3>
                        </div>
                        <p class="text-sm text-gray-600">
                            Jelaskan secara spesifik temuan-temuan yang ditemukan selama audit. Sertakan bukti dan contoh konkret.
                        </p>
                    </div>
                    
                    <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                <i data-feather="check-circle" class="w-5 h-5 text-green-600"></i>
                            </div>
                            <h3 class="font-medium text-green-700">Rekomendasi</h3>
                        </div>
                        <p class="text-sm text-gray-600">
                            Berikan rekomendasi yang spesifik, terukur, dan dapat ditindaklanjuti untuk setiap temuan.
                        </p>
                    </div>
                    
                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                <i data-feather="file-text" class="w-5 h-5 text-purple-600"></i>
                            </div>
                            <h3 class="font-medium text-purple-700">Laporan</h3>
                        </div>
                        <p class="text-sm text-gray-600">
                            Ringkasan laporan harus mencakup scope audit, metodologi, dan kesimpulan utama.
                        </p>
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