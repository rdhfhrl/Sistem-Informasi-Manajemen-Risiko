@extends('layouts.master')

@section('title', 'Laporan Manajemen Risiko - SIMR')

@section('page-title', 'Manajemen Laporan')

@section('page-action')
<a href="{{ route('reports.create') }}" class="btn btn-primary shadow-md">
    <i data-feather="plus" class="w-4 h-4 mr-2"></i> Buat Laporan
</a>
<button type="button" class="btn btn-outline-secondary shadow-md ml-2" data-toggle="modal" data-target="#bulkGenerateModal">
    <i data-feather="download-cloud" class="w-4 h-4 mr-2"></i> Bulk Generate
</button>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Stats Cards -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-100">
                                <i data-feather="file-text" class="w-6 h-6 text-blue-600"></i>
                            </div>
                            <div class="ml-auto">
                                <div class="text-3xl font-bold leading-8" id="total-reports">0</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Total Laporan</div>
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
                            <div class="ml-auto">
                                <div class="text-3xl font-bold leading-8" id="published-reports">0</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Published</div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-orange-100">
                                <i data-feather="clock" class="w-6 h-6 text-orange-600"></i>
                            </div>
                            <div class="ml-auto">
                                <div class="text-3xl font-bold leading-8" id="draft-reports">0</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Draft</div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-purple-100">
                                <i data-feather="calendar" class="w-6 h-6 text-purple-600"></i>
                            </div>
                            <div class="ml-auto">
                                <div class="text-3xl font-bold leading-8" id="scheduled-reports">0</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Dari Schedule</div>
                    </div>
                </div>
            </div>
        </div>

        

        <!-- Data Table -->
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Daftar Laporan
                    <span class="text-gray-500 text-sm ml-2">({{ $reports->total() }} data)</span>
                </h2>
            </div>
            <div class="p-5">
                @if($reports->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-report -mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">JUDUL</th>
                                    <th class="whitespace-nowrap">JENIS</th>
                                    <th class="whitespace-nowrap">TANGGAL</th>
                                    <th class="whitespace-nowrap">PERIODE</th>
                                    <th class="whitespace-nowrap">STATUS</th>
                                    <th class="whitespace-nowrap">FILE</th>
                                    <th class="whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports as $report)
                                    <tr class="intro-x hover:bg-gray-50">
                                        <td>
                                            <div class="font-medium">{{ $report->title }}</div>
                                            <div class="text-gray-500 text-xs mt-0.5">
                                                {{ Str::limit($report->notes, 40) }}
                                            </div>
                                            @if($report->organization)
                                            <div class="text-xs text-gray-400 mt-1">
                                                {{ $report->organization->organization_name }}
                                            </div>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                                @if($report->report_type == 'monitoring') bg-blue-100 text-blue-800
                                                @elseif($report->report_type == 'risk_profile') bg-green-100 text-green-800
                                                @elseif($report->report_type == 'executive_summary') bg-purple-100 text-purple-800
                                                @elseif($report->report_type == 'mitigation_effectiveness') bg-orange-100 text-orange-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ $reportTypes[$report->report_type] ?? $report->report_type }}
                                            </span>
                                            @if($report->schedule)
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $report->schedule->schedule_name }}
                                            </div>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <div class="font-medium">
                                                {{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}
                                            </div>
                                            <div class="text-gray-500 text-xs mt-0.5">
                                                {{ \Carbon\Carbon::parse($report->report_date)->diffForHumans() }}
                                            </div>
                                        </td>
                                        
                                        <td>
                                            @if($report->period)
                                                <span class="px-2 py-1 bg-gray-100 rounded text-sm">
                                                    @switch($report->period)
                                                        @case('bulanan') Bulanan @break
                                                        @case('triwulan') Triwulan @break
                                                        @case('tahunan') Tahunan @break
                                                        @default {{ $report->period }}
                                                    @endswitch
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium 
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
                                            @if($report->approved_by)
                                            <div class="text-xs text-gray-500 mt-1">
                                                Approved by: {{ $report->approved_by_name }}
                                            </div>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($report->hasFile())
                                                <span class="flex items-center text-green-600">
                                                    <i data-feather="file-text" class="w-4 h-4 mr-1"></i>
                                                    {{ $report->file_size }}
                                                </span>
                                                <a href="{{ route('reports.download', $report->report_id) }}" 
                                                   class="text-xs text-blue-500 hover:underline">
                                                    Download
                                                </a>
                                            @else
                                                <span class="text-gray-400 text-sm">
                                                    <i data-feather="file" class="w-4 h-4 inline mr-1"></i>
                                                    No file
                                                </span>
                                            @endif
                                        </td>
                                        
                                        <td class="table-report__action w-56">
                                            <div class="flex justify-center items-center space-x-2">
                                                <a class="flex items-center text-blue-600" 
                                                   href="{{ route('reports.show', $report->report_id) }}">
                                                    <i data-feather="eye" class="w-4 h-4"></i>
                                                </a>
                                                
                                                @if($report->canEdit())
                                                <a class="flex items-center text-yellow-600" 
                                                   href="{{ route('reports.edit', $report->report_id) }}">
                                                    <i data-feather="edit" class="w-4 h-4"></i>
                                                </a>
                                                @endif
                                                
                                                @if($report->canApprove() && auth()->check())
                                                <button type="button" 
                                                        class="flex items-center text-green-600 approve-btn"
                                                        data-report-id="{{ $report->report_id }}"
                                                        data-report-title="{{ $report->title }}">
                                                    <i data-feather="check-circle" class="w-4 h-4"></i>
                                                </button>
                                                @endif
                                                
                                                @if($report->canDelete())
                                                <form action="{{ route('reports.destroy', $report->report_id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex items-center text-red-600">
                                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                                @endif
                                                
                                                @if($report->hasFile())
                                                <a href="{{ route('reports.generate.pdf', $report->report_id) }}" 
                                                   class="flex items-center text-purple-600"
                                                   target="_blank">
                                                    <i data-feather="download" class="w-4 h-4"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($reports->hasPages())
                    <div class="flex flex-col sm:flex-row items-center p-5 border-t border-gray-200">
                        <div class="text-sm text-gray-500">
                            Menampilkan {{ $reports->firstItem() }} - {{ $reports->lastItem() }} dari {{ $reports->total() }} laporan
                        </div>
                        <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                            {{ $reports->appends(request()->query())->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="file-text" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada laporan</h3>
                        <p class="text-gray-500 mb-6">Buat laporan pertama untuk memulai</p>
                        <a href="{{ route('reports.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="w-4 h-4 mr-2"></i> Buat Laporan Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Bulk Generate Modal -->
<div class="modal" id="bulkGenerateModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Bulk Generate Reports</h2>
                <button data-dismiss="modal" class="btn btn-outline-secondary hidden sm:flex">
                    <i data-feather="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form id="bulk-generate-form" method="POST" action="{{ route('reports.bulk.generate') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label">Pilih Schedules</label>
                        <div class="space-y-2 max-h-60 overflow-y-auto">
                            @foreach($schedules as $schedule)
                                @if($schedule->is_active)
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="schedule_{{ $schedule->schedule_id }}" 
                                           name="schedule_ids[]" 
                                           value="{{ $schedule->schedule_id }}"
                                           class="form-check-input">
                                    <label for="schedule_{{ $schedule->schedule_id }}" class="ml-2">
                                        {{ $schedule->schedule_name }}
                                        <span class="text-xs text-gray-500">
                                            ({{ $schedule->frequency }} - {{ $schedule->report_type }})
                                        </span>
                                    </label>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="bulk_report_date" class="form-label">Tanggal Laporan</label>
                        <input type="date" 
                               id="bulk_report_date" 
                               name="report_date" 
                               class="form-control w-full" 
                               value="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                        <div class="flex items-center">
                            <i data-feather="alert-triangle" class="w-5 h-5 text-yellow-600 mr-2"></i>
                            <div class="text-sm text-yellow-700">
                                <strong>Perhatian:</strong> Proses ini mungkin memakan waktu beberapa menit tergantung jumlah schedules.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-outline-secondary w-20">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary w-20" id="bulk-generate-btn">
                        Generate
                    </button>
                </div>
            </form>
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

@push('styles')
<style>
.table-report__action {
    min-width: 160px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Load statistics
    fetch('/reports/statistics')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-reports').textContent = data.total_reports;
            document.getElementById('published-reports').textContent = data.reports_by_status.find(item => item.status === 'published')?.count || 0;
            document.getElementById('draft-reports').textContent = data.reports_by_status.find(item => item.status === 'draft')?.count || 0;
            document.getElementById('scheduled-reports').textContent = data.reports_by_schedule?.reduce((sum, item) => sum + (item.count || 0), 0) || 0;
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
        });
    
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
    
    // Bulk generate form
    const bulkGenerateForm = document.getElementById('bulk-generate-form');
    const bulkGenerateBtn = document.getElementById('bulk-generate-btn');
    
    bulkGenerateForm.addEventListener('submit', function(e) {
        const checkedSchedules = this.querySelectorAll('input[name="schedule_ids[]"]:checked');
        
        if (checkedSchedules.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu schedule untuk digenerate!');
            return;
        }
        
        bulkGenerateBtn.innerHTML = '<i data-feather="loader" class="w-4 h-4 animate-spin mr-2"></i> Processing...';
        bulkGenerateBtn.disabled = true;
        feather.replace();
    });
    
    // Close modals
    const closeButtons = document.querySelectorAll('[data-dismiss="modal"]');
    const modals = document.querySelectorAll('.modal');
    
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            modal.classList.remove('show');
            modal.style.display = 'none';
        });
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        modals.forEach(modal => {
            if (e.target === modal) {
                modal.classList.remove('show');
                modal.style.display = 'none';
            }
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