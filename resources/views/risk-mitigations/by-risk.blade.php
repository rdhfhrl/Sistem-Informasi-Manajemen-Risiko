@extends('layouts.master')

@section('title', 'Mitigasi Risiko - SIMR')

@section('page-title', 'Mitigasi Risiko')

@section('page-action')
<a href="{{ route('risks.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Daftar Risiko
</a>
<a href="{{ route('risk-mitigations.create', $risk->risk_id) }}" class="btn btn-primary shadow-md mr-2">
    <i data-feather="plus" class="w-4 h-4 mr-2"></i> Rencana Mitigasi Baru
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Risk Info Card -->
        <div class="intro-y box mb-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="alert-triangle" class="w-5 h-5 mr-2 text-red-500"></i>
                    Informasi Risiko
                </h2>
                @if($risk->risk_level)
                <span class="px-3 py-1 rounded-full text-xs font-medium 
                    @if($risk->risk_level == 'sangat_rendah') bg-green-100 text-green-800
                    @elseif($risk->risk_level == 'rendah') bg-blue-100 text-blue-800
                    @elseif($risk->risk_level == 'sedang') bg-yellow-100 text-yellow-800
                    @elseif($risk->risk_level == 'tinggi') bg-orange-100 text-orange-800
                    @else bg-red-100 text-red-800
                    @endif">
                    @switch($risk->risk_level)
                        @case('sangat_rendah') Sangat Rendah @break
                        @case('rendah') Rendah @break
                        @case('sedang') Sedang @break
                        @case('tinggi') Tinggi @break
                        @case('sangat_tinggi') Sangat Tinggi @break
                    @endswitch
                </span>
                @endif
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Kode Risiko</div>
                        <div class="font-medium">{{ $risk->risk_code }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Deskripsi Risiko</div>
                        <div class="font-medium">{{ Str::limit($risk->risk_description, 50) }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Skor Risiko</div>
                        <div class="font-medium text-lg 
                            @if($risk->risk_level == 'sangat_tinggi') text-red-600
                            @elseif($risk->risk_level == 'tinggi') text-orange-600
                            @elseif($risk->risk_level == 'sedang') text-yellow-600
                            @elseif($risk->risk_level == 'rendah') text-blue-600
                            @else text-green-600
                            @endif">
                            {{ $risk->risk_score ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-indigo-100">
                                <i data-feather="shield" class="w-6 h-6 text-indigo-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <div class="text-3xl font-bold leading-8">{{ $mitigations->total() }}</div>
                            <div class="text-base text-gray-600 mt-1">Total Mitigasi</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-100">
                                <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            @php
                                $completedCount = collect($mitigations->items())->where('status', 'selesai')->count();
                            @endphp
                            <div class="text-3xl font-bold leading-8">{{ $completedCount }}</div>
                            <div class="text-base text-gray-600 mt-1">Selesai</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-yellow-100">
                                <i data-feather="activity" class="w-6 h-6 text-yellow-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            @php
                                $inProgressCount = collect($mitigations->items())->where('status', 'dalam proses')->count();
                            @endphp
                            <div class="text-3xl font-bold leading-8">{{ $inProgressCount }}</div>
                            <div class="text-base text-gray-600 mt-1">Dalam Proses</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-red-100">
                                <i data-feather="alert-triangle" class="w-6 h-6 text-red-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            @php
                                $overdueCount = collect($mitigations->items())->filter(function($item) {
                                    return \Carbon\Carbon::parse($item->deadline)->lt(now()) && 
                                           !in_array($item->status, ['selesai', 'dibatalkan']);
                                })->count();
                            @endphp
                            <div class="text-3xl font-bold leading-8">{{ $overdueCount }}</div>
                            <div class="text-base text-gray-600 mt-1">Terlambat</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Rencana Mitigasi Risiko
                    <span class="text-gray-500 text-sm ml-2">({{ $mitigations->total() }} data)</span>
                </h2>
                
                @if($mitigations->count() > 0)
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">Filter:</span>
                    <select id="filter-status" class="form-select w-40">
                        <option value="">Semua Status</option>
                        <option value="belum dimulai">Belum Dimulai</option>
                        <option value="dalam proses">Dalam Proses</option>
                        <option value="selesai">Selesai</option>
                        <option value="ditunda">Ditunda</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>
                @endif
            </div>
            <div class="p-5">
                @if($mitigations->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-report -mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">RENCANA MITIGASI</th>
                                    <th class="whitespace-nowrap">PENANGGUNG JAWAB</th>
                                    <th class="whitespace-nowrap">DEADLINE</th>
                                    <th class="whitespace-nowrap">STATUS</th>
                                    <th class="whitespace-nowrap">PROGRESS</th>
                                    <th class="whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mitigations as $mitigation)
                                    @php
                                        $isOverdue = \Carbon\Carbon::parse($mitigation->deadline)->lt(now()) && 
                                                    !in_array($mitigation->status, ['selesai', 'dibatalkan']);
                                        $daysRemaining = \Carbon\Carbon::parse($mitigation->deadline)->diffInDays(now(), false);
                                    @endphp
                                    <tr class="intro-x hover:bg-gray-50" data-status="{{ $mitigation->status }}">
                                        <td>
                                            <div class="max-w-xs">
                                                <div class="font-medium text-gray-800">
                                                    {{ Str::limit($mitigation->mitigation_plan, 50) }}
                                                </div>
                                                <div class="text-gray-500 text-xs mt-1">
                                                    Dibuat: {{ $mitigation->created_at->format('d/m/Y') }}
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                                    <i data-feather="user" class="w-4 h-4 text-blue-600"></i>
                                                </div>
                                                <div class="font-medium">{{ $mitigation->responsible_party }}</div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full {{ $isOverdue ? 'bg-red-100' : 'bg-gray-100' }} flex items-center justify-center mr-3">
                                                    <i data-feather="calendar" class="w-5 h-5 {{ $isOverdue ? 'text-red-600' : 'text-gray-600' }}"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium {{ $isOverdue ? 'text-red-600' : 'text-gray-800' }}">
                                                        {{ \Carbon\Carbon::parse($mitigation->deadline)->format('d M Y') }}
                                                    </div>
                                                    <div class="text-xs {{ $isOverdue ? 'text-red-500' : 'text-gray-500' }}">
                                                        @if($isOverdue)
                                                            Terlambat {{ abs($daysRemaining) }} hari
                                                        @elseif($daysRemaining < 0)
                                                            {{ abs($daysRemaining) }} hari lagi
                                                        @else
                                                            Hari ini
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <span class="px-3 py-1 rounded-full text-xs font-medium 
                                                @if($mitigation->status == 'selesai') bg-green-100 text-green-800
                                                @elseif($mitigation->status == 'dalam proses') bg-blue-100 text-blue-800
                                                @elseif($mitigation->status == 'belum dimulai') bg-gray-100 text-gray-800
                                                @elseif($mitigation->status == 'ditunda') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                @switch($mitigation->status)
                                                    @case('belum dimulai') Belum Dimulai @break
                                                    @case('dalam proses') Dalam Proses @break
                                                    @case('selesai') Selesai @break
                                                    @case('ditunda') Ditunda @break
                                                    @case('dibatalkan') Dibatalkan @break
                                                @endswitch
                                            </span>
                                        </td>
                                        
                                        <td>
                                            @if($mitigation->status == 'selesai')
                                                <div class="flex items-center">
                                                    <div class="w-20 bg-green-100 rounded-full h-2 mr-3">
                                                        <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
                                                    </div>
                                                    <span class="text-green-600 text-sm">100%</span>
                                                </div>
                                            @elseif($mitigation->status == 'dalam proses')
                                                <div class="flex items-center">
                                                    <div class="w-20 bg-blue-100 rounded-full h-2 mr-3">
                                                        <div class="bg-blue-500 h-2 rounded-full" style="width: 60%"></div>
                                                    </div>
                                                    <span class="text-blue-600 text-sm">60%</span>
                                                </div>
                                            @else
                                                <div class="flex items-center">
                                                    <div class="w-20 bg-gray-100 rounded-full h-2 mr-3">
                                                        <div class="bg-gray-300 h-2 rounded-full" style="width: 0%"></div>
                                                    </div>
                                                    <span class="text-gray-400 text-sm">0%</span>
                                                </div>
                                            @endif
                                        </td>
                                        
                                        <td class="table-report__action w-56">
                                            <div class="flex justify-center items-center">
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('risk-mitigations.show', [$risk->risk_id, $mitigation->risk_mitigation_id]) }}">
                                                    <i data-feather="eye" class="w-4 h-4 mr-1"></i> Detail
                                                </a>
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('risk-mitigations.edit', [$risk->risk_id, $mitigation->risk_mitigation_id]) }}">
                                                    <i data-feather="edit" class="w-4 h-4 mr-1"></i> Edit
                                                </a>
                                                <form action="{{ route('risk-mitigations.destroy', [$risk->risk_id, $mitigation->risk_mitigation_id]) }}" 
                                                      method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rencana mitigasi ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex items-center text-red-600 hover:text-red-800">
                                                        <i data-feather="trash-2" class="w-4 h-4 mr-1"></i> Hapus
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
                    @if($mitigations->hasPages())
                    <div class="flex flex-col sm:flex-row items-center p-5 border-t border-gray-200">
                        <div class="text-sm text-gray-500">
                            Menampilkan {{ $mitigations->firstItem() }} - {{ $mitigations->lastItem() }} dari {{ $mitigations->total() }} mitigasi
                        </div>
                        <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                            {{ $mitigations->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="shield" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada rencana mitigasi</h3>
                        <p class="text-gray-500 mb-6">Rencana mitigasi akan muncul setelah Anda membuat rencana untuk risiko ini</p>
                        <a href="{{ route('risk-mitigations.create', $risk->risk_id) }}" 
                           class="btn btn-primary">
                            <i data-feather="plus" class="w-4 h-4 mr-2"></i> Buat Rencana Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Filter by status
    const filterSelect = document.getElementById('filter-status');
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const selectedStatus = this.value;
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                if (!selectedStatus || row.dataset.status === selectedStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
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