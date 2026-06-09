@extends('layouts.master')

@section('title', 'Proyek - SIMR')

@section('page-title', 'Manajemen Proyek Konstruksi')

@section('page-action')
<a href="{{ route('projects.create') }}" class="btn btn-primary shadow-md mr-2">
    <i data-feather="plus-circle" class="w-4 h-4 mr-2"></i> Tambah Proyek
</a>
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
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-theme-1/10">
                                <i data-feather="briefcase" class="w-6 h-6 text-theme-1"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold leading-8 mt-6">{{ $totalProjects }}</div>
                        <div class="text-base text-gray-600 mt-1">Total Proyek</div>
                    </div>
                </div>
            </div>
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-100">
                                <i data-feather="alert-triangle" class="w-6 h-6 text-blue-600"></i>
                            </div>
                        </div>
                        @php
                            $totalRisks = $projects->sum(function($project) {
                                return $project->risks_count ?? 0;
                            });
                        @endphp
                        <div class="text-3xl font-bold leading-8 mt-6">{{ $totalRisks }}</div>
                        <div class="text-base text-gray-600 mt-1">Total Risiko</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Projects List -->
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Daftar Proyek
                    <span class="text-gray-500 text-sm ml-2">({{ $projects->total() }} data)</span>
                </h2>
                <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                    <div class="dropdown">
                        <div class="dropdown-menu w-40">
                            <div class="dropdown-content">
                                <div class="p-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="col-status" checked>
                                        <label class="form-check-label" for="col-status">Status</label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" class="form-check-input" id="col-risks" checked>
                                        <label class="form-check-label" for="col-risks">Risiko</label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" class="form-check-input" id="col-dates" checked>
                                        <label class="form-check-label" for="col-dates">Tanggal</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-5">
                <div class="overflow-x-auto">
                    <table class="table table-report -mt-2">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap">NAMA PROYEK</th>
                                <th class="whitespace-nowrap">LOKASI</th>
                                <th class="whitespace-nowrap">RISIKO</th>
                                <th class="whitespace-nowrap">JADWAL</th>
                                <th class="whitespace-nowrap">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projects as $project)
                                <tr class="intro-x hover:bg-gray-50">
                                    <td>
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-theme-1/10 flex items-center justify-center mr-3">
                                                <i data-feather="briefcase" class="w-5 h-5 text-theme-1"></i>
                                            </div>
                                            <div>
                                                <a href="{{ route('projects.show', $project->pro_id) }}" 
                                                   class="font-medium hover:text-theme-1">
                                                    {{ $project->pro_nama }}
                                                </a>
                                                @if($project->pro_deskripsi)
                                                <div class="text-gray-500 text-xs mt-0.5 truncate max-w-xs">
                                                    {{ $project->pro_deskripsi }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-gray-600">
                                            {{ Str::limit($project->pro_lokasi, 30) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            @if($project->risks_count > 0)
                                                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center mr-2">
                                                    <i data-feather="alert-triangle" class="w-4 h-4 text-red-600"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium">{{ $project->risks_count }}</div>
                                                    <div class="text-xs text-gray-500">risiko</div>
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm">
                                            <div class="text-gray-600">Mulai: {{ \Carbon\Carbon::parse($project->pro_tanggal_mulai)->format('d/m/Y') }}</div>
                                            <div class="text-gray-600">Selesai: {{ \Carbon\Carbon::parse($project->pro_tanggal_selesai)->format('d/m/Y') }}</div>
                                            @php
                                                $daysLeft = floor(now()->diffInDays(\Carbon\Carbon::parse($project->pro_tanggal_selesai), false));
                                            @endphp
                                            @if($project->pro_status == 'Aktif')
                                                <div class="text-xs mt-1 {{ $daysLeft < 0 ? 'text-red-600' : ($daysLeft < 30 ? 'text-yellow-600' : 'text-green-600') }}">
                                                    @if($daysLeft < 0)
                                                        Terlambat {{ abs($daysLeft) }} hari
                                                    @else
                                                        {{ $daysLeft }} hari lagi
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="table-report__action w-56">
                                        <div class="flex justify-center items-center">
                                            <a class="flex items-center mr-3" 
                                               href="{{ route('projects.show', $project->pro_id) }}">
                                                <i data-feather="eye" class="w-4 h-4 mr-1"></i> Detail
                                            </a>
                                            <a class="flex items-center mr-3" 
                                               href="{{ route('projects.edit', $project->pro_id) }}">
                                                <i data-feather="edit" class="w-4 h-4 mr-1"></i> Edit
                                            </a>
                                            <a class="flex items-center mr-3" 
                                               href="{{ route('risks.create', ['pro_id' => $project->pro_id]) }}">
                                                <i data-feather="plus" class="w-4 h-4 mr-1"></i> Risiko
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8">
                                        <div class="flex flex-col items-center">
                                            <i data-feather="briefcase" class="w-16 h-16 text-gray-300 mb-4"></i>
                                            <p class="text-lg font-medium text-gray-500">Belum ada data proyek</p>
                                            <p class="text-gray-500 mt-1">Mulai dengan menambahkan proyek pertama</p>
                                            <a href="{{ route('projects.create') }}" 
                                               class="btn btn-primary mt-4">
                                                <i data-feather="plus-circle" class="w-4 h-4 mr-2"></i> Tambah Proyek
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($projects->hasPages())
                <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center mt-6">
                    {{ $projects->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
#projects-calendar {
    position: relative;
}

.calendar-day {
    border: 1px solid #e5e7eb;
    padding: 8px;
    min-height: 80px;
}

.calendar-day-header {
    font-weight: 600;
    margin-bottom: 4px;
}

.calendar-event {
    background: #3b82f6;
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 11px;
    margin-bottom: 2px;
    cursor: pointer;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.calendar-event.ended {
    background: #10b981;
}

.calendar-event.delayed {
    background: #ef4444;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Calendar functionality
    let currentDate = new Date();
    updateCalendar();
    
    // Auto-hide alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) closeBtn.click();
        });
    }, 5000);
});

function prevMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    updateCalendar();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    updateCalendar();
}

function updateCalendar() {
    const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                       "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    
    document.getElementById('current-month').textContent = 
        monthNames[currentDate.getMonth()] + ' ' + currentDate.getFullYear();
    
    // Here you would fetch and display calendar events
    // This is a simplified version
    const calendarEl = document.getElementById('projects-calendar');
    if (calendarEl) {
        // In real implementation, you would fetch project data for this month
        // and display as calendar events
        calendarEl.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i data-feather="calendar" class="w-12 h-12 mx-auto mb-4"></i>
                <p>Kalender proyek akan menampilkan timeline proyek</p>
                <p class="text-sm mt-2">Implementasi penuh membutuhkan library kalender seperti FullCalendar.js</p>
            </div>
        `;
        feather.replace();
    }
}

// Column toggle functionality
document.querySelectorAll('.form-check-input').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const columnId = this.id.replace('col-', '');
        const columnIndex = {
            'status': 2,
            'risks': 3,
            'dates': 4
        }[columnId];
        
        if (columnIndex) {
            const table = document.querySelector('.table-report');
            const rows = table.querySelectorAll('tr');
            
            rows.forEach(row => {
                const cell = row.cells[columnIndex];
                if (cell) {
                    cell.style.display = this.checked ? '' : 'none';
                }
            });
        }
    });
});
</script>
@endpush