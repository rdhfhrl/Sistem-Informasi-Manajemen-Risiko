@extends('layouts.master')

@section('title', $report->title . ' - SIMR')

@section('page-title', 'Detail Laporan')

@section('breadcrumb')
@parent
<li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Laporan</a></li>
<li class="breadcrumb-item active">Detail</li>
@endsection

@section('page-action')
<a href="{{ route('reports.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
</a>
<div class="flex items-center space-x-2">
    @if($report->canEdit())
    <a href="{{ route('reports.edit', $report->report_id) }}" 
       class="btn btn-primary shadow-md">
        <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit
    </a>
    @endif
    
    @if($report->canApprove() && auth()->check())
    <button type="button" 
            class="btn btn-success shadow-md approve-btn"
            data-report-id="{{ $report->report_id }}"
            data-report-title="{{ $report->title }}">
        <i data-feather="check-circle" class="w-4 h-4 mr-2"></i> Approve
    </button>
    @endif
    
    @if($report->canDelete())
    <form action="{{ route('reports.destroy', $report->report_id) }}" 
          method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger shadow-md">
            <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus
        </button>
    </form>
    @endif
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
                        <i data-feather="file-text" class="w-5 h-5 inline mr-2 text-blue-500"></i>
                        {{ $report->title }}
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
            <div class="p-5 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-center">
                    <div>
                        <div class="text-sm text-gray-600 mb-2">JENIS LAPORAN</div>
                        <div class="text-2xl font-bold text-blue-600">
                            {{ $report->report_type_label }}
                        </div>
                        <div class="text-gray-500 text-sm">
                            @if($report->period)
                                @switch($report->period)
                                    @case('bulanan') Bulanan @break
                                    @case('triwulan') Triwulan @break
                                    @case('tahunan') Tahunan @break
                                    @default {{ $report->period }}
                                @endswitch
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-600 mb-2">DIBUAT OLEH</div>
                        <div class="text-xl font-bold text-green-600">
                            {{ $report->generated_by_name }}
                        </div>
                        <div class="text-gray-500 text-sm">
                            {{ $report->created_at->format('d M Y H:i') }}
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-600 mb-2">DISETUJUI OLEH</div>
                        @if($report->approved_by_name)
                            <div class="text-xl font-bold text-purple-600">
                                {{ $report->approved_by_name }}
                            </div>
                            <div class="text-gray-500 text-sm">
                                {{ $report->formatted_approval_date }}
                            </div>
                        @else
                            <div class="text-xl font-bold text-gray-400">
                                Belum Disetujui
                            </div>
                        @endif
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-600 mb-2">FILE</div>
                        @if($report->hasFile())
                            <div class="flex items-center justify-center">
                                <i data-feather="file-text" class="w-8 h-8 text-green-500 mr-2"></i>
                                <div>
                                    <div class="font-bold">{{ $report->file_size }}</div>
                                    <a href="{{ route('reports.download', $report->report_id) }}" 
                                       class="text-blue-500 text-sm hover:underline">
                                        Download
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="text-xl font-bold text-gray-400">
                                Tidak ada file
                            </div>
                            <a href="{{ route('reports.generate.pdf', $report->report_id) }}" 
                               class="text-blue-500 text-sm hover:underline">
                                Generate PDF
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Scope Info -->
            @if($report->organization || $report->project || $report->risk || $report->schedule)
            <div class="p-5 border-b">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @if($report->organization)
                    <div>
                        <div class="text-gray-600 text-sm mb-1">ORGANISASI</div>
                        <div class="font-medium">{{ $report->organization->organization_name }}</div>
                    </div>
                    @endif
                    
                    @if($report->project)
                    <div>
                        <div class="text-gray-600 text-sm mb-1">PROYEK</div>
                        <div class="font-medium">{{ $report->project->pro_nama }}</div>
                    </div>
                    @endif
                    
                    @if($report->risk)
                    <div>
                        <div class="text-gray-600 text-sm mb-1">RISIKO SPESIFIK</div>
                        <div class="font-medium">{{ $report->risk->risk_code }}</div>
                        <div class="text-xs text-gray-500">{{ Str::limit($report->risk->risk_description, 30) }}</div>
                    </div>
                    @endif
                    
                    @if($report->schedule)
                    <div>
                        <div class="text-gray-600 text-sm mb-1">SCHEDULE</div>
                        <div class="font-medium">{{ $report->schedule->schedule_name }}</div>
                        <div class="text-xs text-gray-500">{{ $report->schedule->frequency }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Report Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Report Data Display -->
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base mr-auto">
                            <i data-feather="bar-chart" class="w-5 h-5 mr-2 text-teal-500"></i>
                            Isi Laporan
                        </h3>
                        <span class="text-sm text-gray-500">
                            Generated: {{ $report->created_at->format('d M Y H:i') }}
                        </span>
                    </div>
                    <div class="p-5">
                        <!-- Dynamic Report Content based on type -->
                        @include('reports.partials.content.' . $report->report_type, ['data' => $reportData])
                    </div>
                </div>

                <!-- Report Notes -->
                @if($report->notes)
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="message-square" class="w-5 h-5 mr-2 text-blue-500"></i>
                            Catatan
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="prose max-w-none">
                            {!! nl2br(e($report->notes)) !!}
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Actions Panel -->
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="zap" class="w-5 h-5 mr-2 text-yellow-500"></i>
                            Aksi
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="space-y-3">
                            @if($report->hasFile())
                            <a href="{{ route('reports.download', $report->report_id) }}" 
                               class="btn btn-primary w-full">
                                <i data-feather="download" class="w-4 h-4 mr-2"></i> Download PDF
                            </a>
                            @else
                            <a href="{{ route('reports.generate.pdf', $report->report_id) }}" 
                               class="btn btn-primary w-full">
                                <i data-feather="file-text" class="w-4 h-4 mr-2"></i> Generate PDF
                            </a>
                            @endif
                            
                            <a href="{{ route('reports.index') }}" 
                               class="btn btn-outline-secondary w-full">
                                <i data-feather="list" class="w-4 h-4 mr-2"></i> Daftar Laporan
                            </a>
                            
                            @if($report->canEdit())
                            <a href="{{ route('reports.edit', $report->report_id) }}" 
                               class="btn btn-outline-blue w-full">
                                <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit Laporan
                            </a>
                            @endif
                            
                            @if($report->canApprove() && auth()->check())
                            <button type="button" 
                                    class="btn btn-success w-full approve-btn"
                                    data-report-id="{{ $report->report_id }}"
                                    data-report-title="{{ $report->title }}">
                                <i data-feather="check-circle" class="w-4 h-4 mr-2"></i> Approve & Publish
                            </button>
                            @endif
                            
                            @if($report->canDelete())
                            <form action="{{ route('reports.destroy', $report->report_id) }}" 
                                  method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-full">
                                    <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus Laporan
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Report Statistics -->
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="pie-chart" class="w-5 h-5 mr-2 text-green-500"></i>
                            Statistik
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="space-y-4">
                            @php
                                $stats = $report->getStats();
                            @endphp
                            
                            @if(!empty($stats))
                                @foreach($stats as $key => $value)
                                    <div>
                                        <div class="text-gray-600 text-sm mb-1">
                                            @switch($key)
                                                @case('total_risks') Total Risiko @break
                                                @case('high_risk_count') Risiko Tinggi @break
                                                @case('average_risk_score') Skor Rata-rata @break
                                                @case('mitigation_completion_rate') Completion Rate @break
                                                @default {{ $key }}
                                            @endswitch
                                        </div>
                                        <div class="font-bold text-lg 
                                            @if($key == 'high_risk_count' && $value > 0) text-red-600
                                            @elseif($key == 'mitigation_completion_rate' && $value < 70) text-orange-600
                                            @elseif($key == 'mitigation_completion_rate' && $value >= 70) text-green-600
                                            @else text-blue-600
                                            @endif">
                                            @if(strpos($key, 'rate') !== false)
                                                {{ number_format($value, 1) }}%
                                            @else
                                                {{ number_format($value, 2) }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-gray-500 py-4">
                                    <i data-feather="bar-chart" class="w-8 h-8 mx-auto mb-2"></i>
                                    <p>Data statistik tidak tersedia</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- System Information -->
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="info" class="w-5 h-5 mr-2 text-gray-500"></i>
                            Informasi Sistem
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="space-y-3">
                            <div>
                                <label class="form-label">ID Laporan</label>
                                <div class="font-mono text-sm text-gray-600">{{ $report->report_id }}</div>
                            </div>
                            
                            <div>
                                <label class="form-label">Dibuat Pada</label>
                                <div class="text-sm text-gray-600">
                                    {{ $report->created_at->format('d M Y H:i') }}
                                </div>
                            </div>
                            
                            <div>
                                <label class="form-label">Diperbarui Pada</label>
                                <div class="text-sm text-gray-600">
                                    {{ $report->updated_at->format('d M Y H:i') }}
                                </div>
                            </div>
                            
                            @if($report->hasSchedule())
                            <div>
                                <label class="form-label">Scheduled Report</label>
                                <div class="text-sm text-gray-600">
                                    {{ $report->getScheduleName() }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal" id="approveModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Approve Laporan</h2>
                <button data-dismiss="modal" class="btn btn-outline-secondary hidden sm:flex">
                    <i data-feather="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form id="approve-form" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label">Judul Laporan</label>
                        <div class="font-medium" id="modal-report-title"></div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="approval_notes" class="form-label">Catatan Persetujuan (Opsional)</label>
                        <textarea id="approval_notes" 
                                  name="approval_notes" 
                                  class="form-control w-full" 
                                  rows="3"
                                  placeholder="Tambahkan catatan untuk laporan ini..."></textarea>
                    </div>
                    
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100 mb-4">
                        <div class="flex items-center">
                            <i data-feather="alert-triangle" class="w-5 h-5 text-yellow-600 mr-2"></i>
                            <div class="text-sm text-yellow-700">
                                <strong>Perhatian:</strong> Setelah disetujui, laporan akan dipublikasikan dan tidak dapat diedit.
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" id="report_id" name="report_id">
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-outline-secondary w-20">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary w-20">
                        Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Approve modal
    const approveButtons = document.querySelectorAll('.approve-btn');
    const approveModal = document.getElementById('approveModal');
    const approveForm = document.getElementById('approve-form');
    
    approveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const reportId = this.dataset.reportId;
            const reportTitle = this.dataset.reportTitle;
            
            // Set modal values
            document.getElementById('modal-report-title').textContent = reportTitle;
            document.getElementById('report_id').value = reportId;
            
            // Set form action
            approveForm.action = `/reports/${reportId}/approve`;
            
            // Show modal
            approveModal.classList.add('show');
            approveModal.style.display = 'block';
        });
    });
    
    // Close modal
    const closeButtons = document.querySelectorAll('[data-dismiss="modal"]');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            modal.classList.remove('show');
            modal.style.display = 'none';
        });
    });
    
    // Form submission
    approveForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const reportId = document.getElementById('report_id').value;
        
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showToast('success', data.message);
                
                // Close modal
                approveModal.classList.remove('show');
                approveModal.style.display = 'none';
                
                // Reload page after 1 second
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Terjadi kesalahan saat menyetujui laporan');
        });
    });
    
    // Toast notification function
    function showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = `toast toast--${type} show`;
        toast.innerHTML = `
            <div class="toast__icon">
                <i data-feather="${type === 'success' ? 'check-circle' : 'alert-circle'}"></i>
            </div>
            <div class="toast__content">${message}</div>
            <div class="toast__close">
                <i data-feather="x"></i>
            </div>
        `;
        
        document.body.appendChild(toast);
        feather.replace();
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
    
    // Close toast on click
    document.addEventListener('click', function(e) {
        if (e.target.closest('.toast__close')) {
            e.target.closest('.toast').remove();
        }
    });
});
</script>
@endpush