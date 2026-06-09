@extends('layouts.master')

@section('title', 'Edit Laporan - SIMR')

@section('page-title', 'Edit Laporan')

@section('breadcrumb')
@parent
<li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Laporan</a></li>
<li class="breadcrumb-item"><a href="{{ route('reports.show', $report->report_id) }}">Detail</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-action')
<a href="{{ route('reports.show', $report->report_id) }}" class="btn btn-outline-secondary shadow-md mr-2">
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
                        Edit Laporan
                    </h2>
                    <div class="text-gray-600 text-sm mt-1">
                        Jenis: <span class="font-medium">{{ $report->report_type_label }}</span> • 
                        Tanggal: <span class="font-medium">{{ $report->formatted_report_date }}</span> • 
                        Status: <span class="font-medium">{{ $report->status_label }}</span>
                    </div>
                </div>
                <div class="mt-3 sm:mt-0">
                    <span class="px-4 py-2 rounded-full text-sm font-medium 
                        @if($report->status == 'draft') bg-yellow-100 text-yellow-800
                        @elseif($report->status == 'generated') bg-blue-100 text-blue-800
                        @elseif($report->status == 'published') bg-green-100 text-green-800
                        @elseif($report->status == 'archived') bg-gray-100 text-gray-800
                        @endif">
                        @if($report->status == 'draft') Draft
                        @elseif($report->status == 'generated') Generated
                        @elseif($report->status == 'published') Published
                        @elseif($report->status == 'archived') Archived
                        @endif
                    </span>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="p-5 bg-gradient-to-r from-gray-50 to-gray-100">
                <div class="text-center">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <div class="text-sm text-gray-600 mb-2">JENIS LAPORAN</div>
                            <div class="text-3xl font-bold text-blue-600">
                                {{ $report->report_type_label }}
                            </div>
                        </div>
                        
                        <div>
                            <div class="text-sm text-gray-600 mb-2">TANGGAL LAPORAN</div>
                            <div class="text-3xl font-bold text-green-600">
                                {{ $report->report_date->format('d M Y') }}
                            </div>
                        </div>
                        
                        <div>
                            <div class="text-sm text-gray-600 mb-2">FILE</div>
                            @if($report->hasFile())
                                <div class="flex items-center justify-center">
                                    <i data-feather="file-text" class="w-8 h-8 text-green-500 mr-2"></i>
                                    <div class="text-left">
                                        <div class="font-bold">{{ $report->file_size }}</div>
                                        <div class="text-xs text-gray-500">PDF tersedia</div>
                                    </div>
                                </div>
                            @else
                                <div class="text-xl font-bold text-gray-400">
                                    Tidak ada file
                                </div>
                            @endif
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
                    Form Edit Laporan
                </h2>
            </div>
            
            <form action="{{ route('reports.update', $report->report_id) }}" method="POST" id="edit-report-form">
                @csrf
                @method('PUT')
                <div class="p-5">
                    <!-- Basic Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">
                            <i data-feather="info" class="w-5 h-5 inline mr-2 text-blue-500"></i>
                            Informasi Dasar
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Title -->
                            <div class="md:col-span-2">
                                <label for="title" class="form-label">Judul Laporan <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       id="title" 
                                       name="title" 
                                       class="form-control w-full @error('title') border-red-500 @enderror" 
                                       value="{{ old('title', $report->title) }}"
                                       required>
                                @error('title')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Schedule Selection -->
                            <div>
                                <label for="schedule_id" class="form-label">Schedule (Opsional)</label>
                                <select id="schedule_id" 
                                        name="schedule_id" 
                                        class="form-select w-full @error('schedule_id') border-red-500 @enderror">
                                    <option value="">Pilih Schedule (Opsional)</option>
                                    @foreach($schedules as $schedule)
                                        <option value="{{ $schedule->schedule_id }}" 
                                            {{ old('schedule_id', $report->schedule_id) == $schedule->schedule_id ? 'selected' : '' }}>
                                            {{ $schedule->schedule_name }} ({{ $schedule->frequency }} - {{ $schedule->report_type }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('schedule_id')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Status -->
                            <div>
                                <label for="status" class="form-label">Status <span class="text-red-500">*</span></label>
                                <select id="status" 
                                        name="status" 
                                        class="form-select w-full @error('status') border-red-500 @enderror" 
                                        required>
                                    <option value="draft" {{ old('status', $report->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="generated" {{ old('status', $report->status) == 'generated' ? 'selected' : '' }}>Generated</option>
                                    <option value="published" {{ old('status', $report->status) == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="archived" {{ old('status', $report->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                                @error('status')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Scope Information (Read-only if published) -->
                    @if(!$report->isPublished())
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">
                            <i data-feather="target" class="w-5 h-5 inline mr-2 text-green-500"></i>
                            Scope Laporan
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Organization -->
                            <div>
                                <label class="form-label">Organisasi</label>
                                <div class="font-medium">
                                    @if($report->organization)
                                        {{ $report->organization->organization_name }}
                                    @else
                                        <span class="text-gray-400">Semua Organisasi</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Project -->
                            <div>
                                <label class="form-label">Proyek</label>
                                <div class="font-medium">
                                    @if($report->project)
                                        {{ $report->project->pro_nama }}
                                    @else
                                        <span class="text-gray-400">Semua Proyek</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Specific Risk -->
                            <div>
                                <label class="form-label">Risiko Spesifik</label>
                                <div class="font-medium">
                                    @if($report->risk)
                                        {{ $report->risk->risk_code }} - {{ Str::limit($report->risk->risk_description, 40) }}
                                    @else
                                        <span class="text-gray-400">Semua Risiko</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Additional Settings -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">
                            <i data-feather="sliders" class="w-5 h-5 inline mr-2 text-orange-500"></i>
                            Pengaturan Tambahan
                        </h3>
                        
                        <div class="space-y-4">
                            <!-- Notes -->
                            <div>
                                <label for="notes" class="form-label">Catatan (Opsional)</label>
                                <textarea id="notes" 
                                          name="notes" 
                                          class="form-control w-full @error('notes') border-red-500 @enderror" 
                                          rows="3">{{ old('notes', $report->notes) }}</textarea>
                                @error('notes')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            @if($report->isGenerated() || $report->isPublished())
                            <!-- Approver -->
                            <div>
                                <label for="approved_by" class="form-label">Disetujui Oleh (Opsional)</label>
                                <select id="approved_by" 
                                        name="approved_by" 
                                        class="form-select w-full @error('approved_by') border-red-500 @enderror">
                                    <option value="">Pilih Approver</option>
                                    <!-- You'll need to pass users list to this view -->
                                    @if(isset($users))
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" 
                                                {{ old('approved_by', $report->approved_by) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('approved_by')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Approval Date -->
                            <div>
                                <label for="approval_date" class="form-label">Tanggal Persetujuan (Opsional)</label>
                                <input type="date" 
                                       id="approval_date" 
                                       name="approval_date" 
                                       class="form-control w-full @error('approval_date') border-red-500 @enderror" 
                                       value="{{ old('approval_date', $report->approval_date ? $report->approval_date->format('Y-m-d') : '') }}">
                                @error('approval_date')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- File Management -->
                    @if($report->hasFile())
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">
                            <i data-feather="file-text" class="w-5 h-5 inline mr-2 text-purple-500"></i>
                            Manajemen File
                        </h3>
                        
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i data-feather="file-text" class="w-8 h-8 text-blue-600 mr-3"></i>
                                    <div>
                                        <div class="font-medium">File PDF tersedia</div>
                                        <div class="text-sm text-gray-600">{{ $report->file_size }}</div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('reports.download', $report->report_id) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i data-feather="download" class="w-4 h-4 mr-1"></i> Download
                                    </a>
                                    <a href="{{ route('reports.generate.pdf', $report->report_id) }}" 
                                       class="btn btn-outline-secondary btn-sm">
                                        <i data-feather="refresh-cw" class="w-4 h-4 mr-1"></i> Regenerate
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Form Actions -->
                    <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
                        <div>
                            <a href="{{ route('reports.show', $report->report_id) }}" 
                               class="btn btn-outline-secondary w-32">
                                <i data-feather="x" class="w-4 h-4 mr-2"></i> Batal
                            </a>
                        </div>
                        <div class="flex space-x-3">
                            <button type="reset" class="btn btn-outline-secondary w-32">
                                <i data-feather="refresh-ccw" class="w-4 h-4 mr-2"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary w-32" id="submit-btn">
                                <i data-feather="save" class="w-4 h-4 mr-2"></i> Update
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Warning for Published Reports -->
        @if($report->isPublished())
        <div class="intro-y box mt-6 border-yellow-200">
            <div class="flex items-center p-5 border-b border-yellow-200 bg-yellow-50">
                <h2 class="font-medium text-base mr-auto text-yellow-700">
                    <i data-feather="alert-triangle" class="w-5 h-5 mr-2"></i>
                    Perhatian: Laporan Sudah Dipublikasikan
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    <p class="text-yellow-700">
                        <strong>Laporan ini sudah dalam status "Published".</strong> Perubahan hanya dapat dilakukan pada beberapa field tertentu.
                    </p>
                    <ul class="list-disc list-inside text-yellow-700 ml-4">
                        <li>Judul laporan dapat diubah</li>
                        <li>Catatan dapat ditambahkan/diubah</li>
                        <li>Informasi approver dapat diperbarui</li>
                        <li>Status dapat diubah menjadi "Archived"</li>
                    </ul>
                    <p class="text-gray-600 text-sm">
                        Data laporan yang sudah dipublikasikan tidak dapat diubah karena sudah menjadi bagian dari historis.
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- System Information -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="database" class="w-5 h-5 mr-2 text-gray-500"></i>
                    Informasi Sistem
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <div class="text-gray-600 text-sm mb-1">ID Laporan</div>
                        <div class="font-mono text-gray-700">{{ $report->report_id }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Dibuat Pada</div>
                        <div class="font-medium">{{ $report->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Diperbarui Pada</div>
                        <div class="font-medium">{{ $report->updated_at->format('d M Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Dibuat Oleh</div>
                        <div class="font-medium">{{ $report->generated_by_name }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Disetujui Oleh</div>
                        <div class="font-medium">{{ $report->approved_by_name ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Tanggal Persetujuan</div>
                        <div class="font-medium">{{ $report->formatted_approval_date ?? '-' }}</div>
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
    
    const form = document.getElementById('edit-report-form');
    const submitBtn = document.getElementById('submit-btn');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            // Show loading
            submitBtn.innerHTML = '<i data-feather="loader" class="w-4 h-4 animate-spin mr-2"></i> Processing...';
            submitBtn.disabled = true;
            feather.replace();
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