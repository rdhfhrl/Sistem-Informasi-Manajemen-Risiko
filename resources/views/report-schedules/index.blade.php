@extends('layouts.master')

@section('title', 'Jadwal Laporan - SIMR')

@section('page-title', 'Manajemen Jadwal Laporan')

@section('page-action')
<a href="{{ route('report-schedules.create') }}" class="btn btn-primary shadow-md">
    <i data-feather="plus" class="w-4 h-4 mr-2"></i> Buat Jadwal
</a>
<button type="button" class="btn btn-outline-secondary shadow-md ml-2" onclick="checkDueSchedules()">
    <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i> Cek Jadwal Due
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
                            <i data-feather="calendar" class="w-6 h-6 text-blue-600"></i>
                        </div>
                        <div class="ml-auto">
                            <div class="text-3xl font-bold leading-8">{{ $totalCount ?? $schedules->total() }}</div>
                        </div>
                    </div>
                    <div class="text-base text-gray-600 mt-1">Total Jadwal</div>
                </div>
            </div>
        </div>
        
        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
            <div class="report-box zoom-in">
                <div class="box p-5">
                    <div class="flex">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-100">
                            <i data-feather="play-circle" class="w-6 h-6 text-green-600"></i>
                        </div>
                        <div class="ml-auto">
                            <div class="text-3xl font-bold leading-8">{{ $activeCount ?? $schedules->where('is_active', true)->count() }}</div>
                        </div>
                    </div>
                    <div class="text-base text-gray-600 mt-1">Aktif</div>
                </div>
            </div>
        </div>
        
        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
            <div class="report-box zoom-in">
                <div class="box p-5">
                    <div class="flex">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-orange-100">
                            <i data-feather="zap" class="w-6 h-6 text-orange-600"></i>
                        </div>
                        <div class="ml-auto">
                            <div class="text-3xl font-bold leading-8">{{ $autoGenerateCount ?? $schedules->where('auto_generate', true)->count() }}</div>
                        </div>
                    </div>
                    <div class="text-base text-gray-600 mt-1">Auto Generate</div>
                </div>
            </div>
        </div>
    </div>

        <!-- Filter Section -->
        <div class="intro-y box mb-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="filter" class="w-5 h-5 mr-2 text-purple-500"></i>
                    Filter Jadwal
                </h2>
            </div>
            <div class="p-5">
                <form method="GET" action="{{ route('report-schedules.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="report_type" class="form-label">Jenis Laporan</label>
                        <select id="report_type" name="report_type" class="form-select">
                            <option value="">Semua Jenis</option>
                            <option value="monitoring" {{ request('report_type') == 'monitoring' ? 'selected' : '' }}>Monitoring</option>
                            <option value="risk_profile" {{ request('report_type') == 'risk_profile' ? 'selected' : '' }}>Profil Risiko</option>
                            <option value="executive_summary" {{ request('report_type') == 'executive_summary' ? 'selected' : '' }}>Ringkasan Eksekutif</option>
                            <option value="mitigation_effectiveness" {{ request('report_type') == 'mitigation_effectiveness' ? 'selected' : '' }}>Efektivitas Mitigasi</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="frequency" class="form-label">Frekuensi</label>
                        <select id="frequency" name="frequency" class="form-select">
                            <option value="">Semua Frekuensi</option>
                            <option value="daily" {{ request('frequency') == 'daily' ? 'selected' : '' }}>Harian</option>
                            <option value="weekly" {{ request('frequency') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                            <option value="monthly" {{ request('frequency') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                            <option value="quarterly" {{ request('frequency') == 'quarterly' ? 'selected' : '' }}>Triwulan</option>
                            <option value="yearly" {{ request('frequency') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="is_active" class="form-label">Status</label>
                        <select id="is_active" name="is_active" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="auto_generate" class="form-label">Auto Generate</label>
                        <select id="auto_generate" name="auto_generate" class="form-select">
                            <option value="">Semua</option>
                            <option value="1" {{ request('auto_generate') == '1' ? 'selected' : '' }}>Ya</option>
                            <option value="0" {{ request('auto_generate') == '0' ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>
                    
                    <div class="md:col-span-4 flex justify-end space-x-3">
                        <button type="submit" class="btn btn-primary w-32">
                            <i data-feather="filter" class="w-4 h-4 mr-2"></i> Filter
                        </button>
                        <a href="{{ route('report-schedules.index') }}" class="btn btn-outline-secondary w-32">
                            <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Daftar Jadwal Laporan
                    <span class="text-gray-500 text-sm ml-2">({{ $schedules->total() }} data)</span>
                </h2>
            </div>
            <div class="p-5">
                @if($schedules->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-report -mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">NAMA JADWAL</th>
                                    <th class="whitespace-nowrap">JENIS LAPORAN</th>
                                    <th class="whitespace-nowrap">FREKUENSI</th>
                                    <th class="whitespace-nowrap">STATUS</th>
                                    <th class="whitespace-nowrap">RUN BERIKUTNYA</th>
                                    <th class="whitespace-nowrap">HISTORI</th>
                                    <th class="whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedules as $schedule)
                                    <tr class="intro-x hover:bg-gray-50">
                                        <td>
                                            <div class="font-medium">{{ $schedule->schedule_name }}</div>
                                            <div class="text-gray-500 text-xs mt-0.5">
                                                Dibuat oleh: {{ $schedule->creator->name ?? 'System' }}
                                            </div>
                                            @if($schedule->generation_time)
                                            <div class="text-xs text-gray-400 mt-1">
                                                Jam: {{ $schedule->generation_time }}
                                            </div>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                                @if($schedule->report_type == 'monitoring') bg-blue-100 text-blue-800
                                                @elseif($schedule->report_type == 'risk_profile') bg-green-100 text-green-800
                                                @elseif($schedule->report_type == 'executive_summary') bg-purple-100 text-purple-800
                                                @elseif($schedule->report_type == 'mitigation_effectiveness') bg-orange-100 text-orange-800
                                                @endif">
                                                {{ $schedule->report_type_label }}
                                            </span>
                                            @if($schedule->auto_generate)
                                            <div class="text-xs text-green-500 mt-1">
                                                <i data-feather="zap" class="w-3 h-3 inline mr-1"></i> Auto Generate
                                            </div>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <div class="font-medium">{{ $schedule->frequency_label }}</div>
                                            <div class="text-gray-500 text-xs">
                                                @if($schedule->frequency == 'monthly' && $schedule->day_of_month)
                                                    Tanggal: {{ $schedule->day_of_month }}
                                                @endif
                                                @if($schedule->frequency == 'yearly' && $schedule->month_of_year)
                                                    Bulan: {{ $schedule->month_of_year }}
                                                @endif
                                            </div>
                                        </td>
                                        
                                        <td>
                                            @if($schedule->is_active)
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i data-feather="check-circle" class="w-3 h-3 inline mr-1"></i>
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i data-feather="x-circle" class="w-3 h-3 inline mr-1"></i>
                                                    Nonaktif
                                                </span>
                                            @endif
                                            @if($schedule->auto_send_email)
                                            <div class="text-xs text-blue-500 mt-1">
                                                <i data-feather="mail" class="w-3 h-3 inline mr-1"></i> Auto Email
                                            </div>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($schedule->is_active)
                                                <div class="font-medium">
                                                    {{ $schedule->next_run_date?->format('d M Y H:i') ?? '-' }}
                                                </div>
                                                <div class="text-gray-500 text-xs">
                                                    @if($schedule->next_run_date)
                                                        {{ \Carbon\Carbon::parse($schedule->next_run_date)->diffForHumans() }}
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <div class="font-medium">{{ $schedule->reports_count ?? $schedule->reports()->count() }}</div>
                                            <div class="text-gray-500 text-xs">Total laporan</div>
                                            @if($schedule->reports()->count() > 0)
                                            <div class="text-xs text-blue-500">
                                                Terakhir: {{ $schedule->reports()->latest()->first()->created_at->format('d M') }}
                                            </div>
                                            @endif
                                        </td>
                                        
                                        <td class="table-report__action w-56">
                                            <div class="flex justify-center items-center space-x-2">
                                                <a class="flex items-center text-blue-600" 
                                                   href="{{ route('report-schedules.show', $schedule->schedule_id) }}">
                                                    <i data-feather="eye" class="w-4 h-4"></i>
                                                </a>
                                                
                                                <a class="flex items-center text-yellow-600" 
                                                   href="{{ route('report-schedules.edit', $schedule->schedule_id) }}">
                                                    <i data-feather="edit" class="w-4 h-4"></i>
                                                </a>
                                                
                                                <button type="button" 
                                                        class="flex items-center text-green-600 generate-btn"
                                                        data-schedule-id="{{ $schedule->schedule_id }}"
                                                        data-schedule-name="{{ $schedule->schedule_name }}">
                                                    <i data-feather="play-circle" class="w-4 h-4"></i>
                                                </button>
                                                
                                                <form action="{{ route('report-schedules.toggle-active', $schedule->schedule_id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Apakah Anda yakin ingin mengubah status jadwal ini?')">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="flex items-center 
                                                        @if($schedule->is_active) text-orange-600
                                                        @else text-green-600
                                                        @endif">
                                                        @if($schedule->is_active)
                                                            <i data-feather="pause-circle" class="w-4 h-4"></i>
                                                        @else
                                                            <i data-feather="play-circle" class="w-4 h-4"></i>
                                                        @endif
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('report-schedules.destroy', $schedule->schedule_id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex items-center text-red-600">
                                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($schedules->hasPages())
                    <div class="flex flex-col sm:flex-row items-center p-5 border-t border-gray-200">
                        <div class="text-sm text-gray-500">
                            Menampilkan {{ $schedules->firstItem() }} - {{ $schedules->lastItem() }} dari {{ $schedules->total() }} jadwal
                        </div>
                        <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                            {{ $schedules->appends(request()->query())->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="calendar" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada jadwal laporan</h3>
                        <p class="text-gray-500 mb-6">Buat jadwal pertama untuk mengatur laporan otomatis</p>
                        <a href="{{ route('report-schedules.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="w-4 h-4 mr-2"></i> Buat Jadwal Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Due Schedules Alert -->
        <div class="intro-y box mt-6 border-yellow-200 hidden" id="due-schedules-alert">
            <div class="flex items-center p-5 border-b border-yellow-200 bg-yellow-50">
                <h2 class="font-medium text-base mr-auto text-yellow-700">
                    <i data-feather="alert-triangle" class="w-5 h-5 mr-2"></i>
                    Jadwal yang Perlu Dijalankan
                </h2>
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800" id="due-count">0 jadwal</span>
            </div>
            <div class="p-5">
                <div class="space-y-3" id="due-schedules-list">
                    <!-- Due schedules will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Modal -->
<div class="modal" id="generateModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Generate Laporan dari Jadwal</h2>
                <button data-dismiss="modal" class="btn btn-outline-secondary hidden sm:flex">
                    <i data-feather="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form id="generate-form" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label">Nama Jadwal</label>
                        <div class="font-medium" id="modal-schedule-name"></div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="report_date" class="form-label">Tanggal Laporan</label>
                        <input type="date" 
                               id="report_date" 
                               name="report_date" 
                               class="form-control w-full" 
                               value="{{ date('Y-m-d') }}">
                        <div class="text-gray-500 text-xs mt-1">
                            Kosongkan untuk menggunakan tanggal saat ini
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <div class="flex items-center">
                            <i data-feather="info" class="w-5 h-5 text-blue-600 mr-2"></i>
                            <div class="text-sm text-blue-700">
                                Laporan akan dibuat berdasarkan parameter yang telah ditentukan di jadwal.
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" id="schedule_id" name="schedule_id">
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-outline-secondary w-20">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary w-20">
                        Generate
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
    
    // Generate modal
    const generateButtons = document.querySelectorAll('.generate-btn');
    const generateModal = document.getElementById('generateModal');
    const generateForm = document.getElementById('generate-form');
    
    generateButtons.forEach(button => {
        button.addEventListener('click', function() {
            const scheduleId = this.dataset.scheduleId;
            const scheduleName = this.dataset.scheduleName;
            
            // Set modal values
            document.getElementById('modal-schedule-name').textContent = scheduleName;
            document.getElementById('schedule_id').value = scheduleId;
            
            // Set form action
            generateForm.action = `/reports/generate-from-schedule/${scheduleId}`;
            
            // Show modal
            generateModal.classList.add('show');
            generateModal.style.display = 'block';
        });
    });
    
    // Check due schedules
    window.checkDueSchedules = function() {
        fetch('/report-schedules/api/due-schedules')
            .then(response => response.json())
            .then(data => {
                const alertDiv = document.getElementById('due-schedules-alert');
                const dueList = document.getElementById('due-schedules-list');
                const dueCount = document.getElementById('due-count');
                
                if (data.length > 0) {
                    dueCount.textContent = `${data.length} jadwal`;
                    
                    let html = '';
                    data.forEach(schedule => {
                        html += `
                            <div class="flex items-center p-3 bg-white rounded-lg border border-yellow-100">
                                <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center mr-3">
                                    <i data-feather="alert-triangle" class="w-5 h-5 text-yellow-600"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium">${schedule.schedule_name}</div>
                                    <div class="text-sm text-gray-600">
                                        ${schedule.report_type_label} • ${schedule.frequency_label}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Seharusnya dijalankan: ${schedule.next_run_date}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="button" 
                                            class="btn btn-primary btn-sm generate-btn"
                                            data-schedule-id="${schedule.schedule_id}"
                                            data-schedule-name="${schedule.schedule_name}">
                                        <i data-feather="play-circle" class="w-4 h-4 mr-1"></i> Jalankan
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                    
                    dueList.innerHTML = html;
                    alertDiv.classList.remove('hidden');
                    
                    // Reattach event listeners to new generate buttons
                    document.querySelectorAll('.generate-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const scheduleId = this.dataset.scheduleId;
                            const scheduleName = this.dataset.scheduleName;
                            
                            document.getElementById('modal-schedule-name').textContent = scheduleName;
                            document.getElementById('schedule_id').value = scheduleId;
                            generateForm.action = `/reports/generate-from-schedule/${scheduleId}`;
                            
                            generateModal.classList.add('show');
                            generateModal.style.display = 'block';
                        });
                    });
                    
                    feather.replace();
                } else {
                    alertDiv.classList.add('hidden');
                    showToast('info', 'Tidak ada jadwal yang perlu dijalankan saat ini.');
                }
            })
            .catch(error => {
                console.error('Error checking due schedules:', error);
                showToast('error', 'Gagal memeriksa jadwal yang perlu dijalankan.');
            });
    };
    
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
    generateForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const scheduleId = document.getElementById('schedule_id').value;
        
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
                generateModal.classList.remove('show');
                generateModal.style.display = 'none';
                
                // Reload page after 1 second
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Terjadi kesalahan saat menggenerate laporan');
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