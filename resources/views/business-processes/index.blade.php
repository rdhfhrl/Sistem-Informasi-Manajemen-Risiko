@extends('layouts.master')

@section('title', 'Proses Bisnis - SIMR')

@section('page-title', 'Manajemen Proses Bisnis')

@section('page-action')
<a href="{{ route('business-processes.create') }}" class="btn btn-primary shadow-md mr-2">
    <i data-feather="plus-circle" class="w-4 h-4 mr-2"></i> Tambah Proses
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Stats Cards -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-purple-100">
                                <i data-feather="briefcase" class="w-6 h-6 text-purple-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <div class="text-3xl font-bold leading-8">{{ $processes->count() }}</div>
                            <div class="text-base text-gray-600 mt-1">Total Proses</div>
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
                                $totalRisks = $processes->sum(function($process) {
                                    return $process->risks_count ?? 0;
                                });
                            @endphp
                            <div class="text-3xl font-bold leading-8">{{ $totalRisks }}</div>
                            <div class="text-base text-gray-600 mt-1">Risiko Teridentifikasi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Processes List -->
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Daftar Proses Bisnis
                    <span class="text-gray-500 text-sm ml-2">({{ $processes->count() }} data)</span>
                </h2>
                <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                    <div class="dropdown">
                        <div class="dropdown-menu w-40">
                            <div class="dropdown-content">
                                <a href="{{ route('business-processes.index', ['sort' => 'name_asc']) }}" 
                                   class="dropdown-item {{ request('sort') == 'name_asc' ? 'active' : '' }}">
                                    Nama A-Z
                                </a>
                                <a href="{{ route('business-processes.index', ['sort' => 'name_desc']) }}" 
                                   class="dropdown-item {{ request('sort') == 'name_desc' ? 'active' : '' }}">
                                    Nama Z-A
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('business-processes.index', ['sort' => 'risk_asc']) }}" 
                                   class="dropdown-item {{ request('sort') == 'risk_asc' ? 'active' : '' }}">
                                    Risiko Terbanyak
                                </a>
                                <a href="{{ route('business-processes.index', ['sort' => 'risk_desc']) }}" 
                                   class="dropdown-item {{ request('sort') == 'risk_desc' ? 'active' : '' }}">
                                    Risiko Terkecil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-5">
                @if($processes->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-report -mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">PROSES BISNIS</th>
                                    <th class="whitespace-nowrap">DESKRIPSI</th>
                                    <th class="whitespace-nowrap">RISIKO</th>
                                    <th class="whitespace-nowrap">DIBUAT</th>
                                    <th class="whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($processes as $process)
                                    <tr class="intro-x hover:bg-gray-50">
                                        <td>
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                                    <i data-feather="briefcase" class="w-5 h-5 text-purple-600"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <a href="{{ route('business-processes.show', $process->business_process_id) }}" 
                                                       class="font-medium hover:text-purple-600 block truncate">
                                                        {{ $process->business_process_name }}
                                                    </a>
                                                    <div class="text-gray-500 text-xs mt-0.5 truncate">
                                                        ID: BP-{{ str_pad($process->business_process_id, 4, '0', STR_PAD_LEFT) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="max-w-xs">
                                                <div class="text-sm text-gray-600 line-clamp-2">
                                                    {{ $process->business_process_description }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex items-center">
                                                @if(($process->risks_count ?? 0) > 0)
                                                    <div class="w-8 h-8 rounded-full 
                                                        @if(($process->risks_count ?? 0) >= 5) bg-red-100 
                                                        @elseif(($process->risks_count ?? 0) >= 3) bg-orange-100 
                                                        @else bg-yellow-100 
                                                        @endif 
                                                        flex items-center justify-center mr-2">
                                                        <i data-feather="alert-triangle" class="w-4 h-4 
                                                            @if(($process->risks_count ?? 0) >= 5) text-red-600 
                                                            @elseif(($process->risks_count ?? 0) >= 3) text-orange-600 
                                                            @else text-yellow-600 
                                                            @endif">
                                                        </i>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium">
                                                            {{ $process->risks_count ?? 0 }}
                                                        </div>
                                                        <div class="text-xs 
                                                            @if(($process->risks_count ?? 0) >= 5) text-red-500 
                                                            @elseif(($process->risks_count ?? 0) >= 3) text-orange-500 
                                                            @else text-yellow-500 
                                                            @endif">
                                                            risiko
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center mr-2">
                                                        <i data-feather="check" class="w-4 h-4 text-green-600"></i>
                                                    </div>
                                                    <div class="text-gray-400 text-sm">Tidak ada</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-sm text-gray-600">
                                                {{ \Carbon\Carbon::parse($process->created_at)->format('d/m/Y') }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($process->created_at)->format('H:i') }}
                                            </div>
                                        </td>
                                        <td class="table-report__action w-56">
                                            <div class="flex justify-center items-center">
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('business-processes.show', $process->business_process_id) }}">
                                                    <i data-feather="eye" class="w-4 h-4 mr-1"></i> Detail
                                                </a>
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('business-processes.edit', $process->business_process_id) }}">
                                                    <i data-feather="edit" class="w-4 h-4 mr-1"></i> Edit
                                                </a>
                                                <form method="POST" 
                                                      action="{{ route('business-processes.destroy', $process->business_process_id) }}"
                                                      class="delete-form inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex items-center text-danger" 
                                                            onclick="return confirm('Hapus proses bisnis ini? Data risiko yang terkait akan tetap ada.')">
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
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="briefcase" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        @if(request()->has('search') || request()->has('organization'))
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Tidak ditemukan</h3>
                            <p class="text-gray-500 mb-6">Tidak ada proses bisnis yang sesuai dengan filter yang dipilih</p>
                            <a href="{{ route('business-processes.index') }}" 
                               class="btn btn-secondary mr-2">
                                <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i> Reset Filter
                            </a>
                        @else
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada proses bisnis</h3>
                            <p class="text-gray-500 mb-6">Proses bisnis merupakan aktivitas terstruktur yang akan menjadi dasar identifikasi risiko</p>
                        @endif
                        <a href="{{ route('business-processes.create') }}" 
                           class="btn btn-primary">
                            <i data-feather="plus-circle" class="w-4 h-4 mr-2"></i> Tambah Proses Bisnis Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .line-clamp-2 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        line-clamp: 2;
    }
    
    .table-report td {
        vertical-align: middle;
    }
</style>
@endpush

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
    
    // Quick search on enter
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });
    }
});

// Quick delete with confirmation
function deleteProcess(id, name) {
    if (confirm(`Apakah Anda yakin ingin menghapus proses bisnis "${name}"?`)) {
        const form = document.getElementById('delete-form-' + id);
        if (form) form.submit();
    }
}
</script>
@endpush