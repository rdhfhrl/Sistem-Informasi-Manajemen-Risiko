@extends('layouts.master')

@php
    // Statistik yang aman
    $stats = [
        'total' => \App\Models\RiskIndicator::count(),
        'exceeded' => \App\Models\RiskIndicator::has('measurements')
                       ->get()
                       ->filter(function($indicator) {
                           return $indicator->isExceeded();
                       })
                       ->count(),
        'akar_masalah' => \App\Models\RiskIndicator::where('indicator_type', 'akar_masalah')->count(),
        'penyebab' => \App\Models\RiskIndicator::where('indicator_type', 'penyebab')->count(),
        'dampak' => \App\Models\RiskIndicator::where('indicator_type', 'dampak')->count(),
        'normal' => \App\Models\RiskIndicator::has('measurements')
                       ->get()
                       ->filter(function($indicator) {
                           return !$indicator->isExceeded();
                       })
                       ->count(),
        'not_measured' => \App\Models\RiskIndicator::doesntHave('measurements')->count(),
    ];
@endphp

@section('title', 'Semua Indikator Risiko - SIMR')

@section('page-title', 'Semua Indikator Risiko')

@section('page-action')
<a href="{{ route('risk-indicators.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Pilih Risiko
</a>
<a href="{{ route('risk-indicators.exceeded-thresholds') }}" class="btn btn-danger shadow-md">
    <i data-feather="alert-triangle" class="w-4 h-4 mr-2"></i> Lihat yang Melebihi Batas
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
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-100">
                                <i data-feather="activity" class="w-6 h-6 text-blue-600"></i>
                            </div>
                            <div class="ml-auto">
                                <div class="text-3xl font-bold leading-8">{{ $stats['total'] }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Total Indikator</div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-red-100">
                                <i data-feather="alert-triangle" class="w-6 h-6 text-red-600"></i>
                            </div>
                            <div class="ml-auto">
                                <div class="text-3xl font-bold leading-8">{{ $stats['exceeded'] }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Melebihi Batas</div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-100">
                                <i data-feather="target" class="w-6 h-6 text-green-600"></i>
                            </div>
                            <div class="ml-auto">
                                <div class="text-3xl font-bold leading-8">{{ $stats['dampak'] }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Indikator Dampak</div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-purple-100">
                                <i data-feather="search" class="w-6 h-6 text-purple-600"></i>
                            </div>
                            <div class="ml-auto">
                                <div class="text-3xl font-bold leading-8">{{ $stats['penyebab'] }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Indikator Penyebab</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="intro-y box mb-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="filter" class="w-5 h-5 mr-2 text-purple-500"></i>
                    Filter Indikator
                </h2>
            </div>
            <div class="p-5">
                <form method="GET" action="{{ route('risk-indicators.all') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="indicator_type" class="form-label">Jenis Indikator</label>
                        <select id="indicator_type" name="indicator_type" class="form-select">
                            <option value="">Semua Jenis</option>
                            <option value="akar_masalah" {{ request('indicator_type') == 'akar_masalah' ? 'selected' : '' }}>Akar Masalah</option>
                            <option value="penyebab" {{ request('indicator_type') == 'penyebab' ? 'selected' : '' }}>Penyebab</option>
                            <option value="dampak" {{ request('indicator_type') == 'dampak' ? 'selected' : '' }}>Dampak</option>
                            <option value="lainnya" {{ request('indicator_type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="risk_level" class="form-label">Level Risiko</label>
                        <select id="risk_level" name="risk_level" class="form-select">
                            <option value="">Semua Level</option>
                            <option value="sangat_tinggi" {{ request('risk_level') == 'sangat_tinggi' ? 'selected' : '' }}>Sangat Tinggi</option>
                            <option value="tinggi" {{ request('risk_level') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                            <option value="sedang" {{ request('risk_level') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="rendah" {{ request('risk_level') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                            <option value="sangat_rendah" {{ request('risk_level') == 'sangat_rendah' ? 'selected' : '' }}>Sangat Rendah</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="exceeded" {{ request('status') == 'exceeded' ? 'selected' : '' }}>Melebihi Batas</option>
                            <option value="normal" {{ request('status') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="not_measured" {{ request('status') == 'not_measured' ? 'selected' : '' }}>Belum Diukur</option>
                        </select>
                    </div>
                    
                    <div class="md:col-span-4 flex justify-end space-x-3">
                        <button type="submit" class="btn btn-primary w-32">
                            <i data-feather="filter" class="w-4 h-4 mr-2"></i> Filter
                        </button>
                        <a href="{{ route('risk-indicators.all') }}" class="btn btn-outline-secondary w-32">
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
                    Daftar Semua Indikator
                    <span class="text-gray-500 text-sm ml-2">({{ $indicators->total() }} data)</span>
                </h2>
            </div>
            <div class="p-5">
                @if($indicators->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-report -mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">RISIKO</th>
                                    <th class="whitespace-nowrap">INDIKATOR</th>
                                    <th class="whitespace-nowrap">JENIS</th>
                                    <th class="whitespace-nowrap">AMBANG BATAS</th>
                                    <th class="whitespace-nowrap">NILAI SAAT INI</th>
                                    <th class="whitespace-nowrap">STATUS</th>
                                    <th class="whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($indicators as $indicator)
                                    @php
                                        $isExceeded = $indicator->current_value && $indicator->current_value > $indicator->threshold;
                                    @endphp
                                    
                                    <tr class="intro-x hover:bg-gray-50">
                                        <td>
                                            <div class="font-medium">
                                                {{ $indicator->risk->risk_code ?? 'N/A' }}
                                            </div>
                                            <div class="text-gray-500 text-xs mt-0.5">
                                                {{ Str::limit($indicator->risk->risk_description ?? 'Tidak ada deskripsi', 30) }}
                                            </div>
                                            @if($indicator->risk->project)
                                            <div class="text-xs text-gray-400">
                                                {{ $indicator->risk->project->project_name ?? '' }}
                                            </div>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <div class="font-medium">{{ $indicator->indicator_name }}</div>
                                            <div class="text-gray-500 text-xs mt-0.5">
                                                {{ Str::limit($indicator->indicator_description, 30) }}
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                                @if($indicator->indicator_type == 'akar_masalah') bg-blue-100 text-blue-800
                                                @elseif($indicator->indicator_type == 'penyebab') bg-orange-100 text-orange-800
                                                @elseif($indicator->indicator_type == 'dampak') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                @switch($indicator->indicator_type)
                                                    @case('akar_masalah') AM @break
                                                    @case('penyebab') P @break
                                                    @case('dampak') D @break
                                                    @case('lainnya') L @break
                                                @endswitch
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <div class="font-medium">{{ number_format($indicator->threshold, 2) }}</div>
                                            <div class="text-gray-500 text-xs">{{ $indicator->unit }}</div>
                                        </td>
                                        
                                        <td>
                                            @if($indicator->current_value)
                                                <div class="font-bold text-lg 
                                                    @if($isExceeded) text-red-600
                                                    @else text-green-600
                                                    @endif">
                                                    {{ number_format($indicator->current_value, 2) }}
                                                </div>
                                                <div class="text-gray-500 text-xs">
                                                    {{ $indicator->unit }}
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($indicator->current_value)
                                                @if($isExceeded)
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <i data-feather="alert-triangle" class="w-3 h-3 inline mr-1"></i>
                                                        Melebihi
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i data-feather="check-circle" class="w-3 h-3 inline mr-1"></i>
                                                        Normal
                                                    </span>
                                                @endif
                                            @else
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Belum Diukur
                                                </span>
                                            @endif
                                        </td>
                                        
                                        <td class="table-report__action">
                                            <div class="flex justify-center items-center space-x-2">
                                                <a class="flex items-center text-blue-600" 
                                                   href="{{ route('risk-indicators.show', [
                                                       'riskId' => $indicator->risk_indicator_risk_id,
                                                       'indicatorId' => $indicator->risk_indicator_id
                                                   ]) }}">
                                                    <i data-feather="eye" class="w-4 h-4"></i>
                                                </a>
                                                <a class="flex items-center text-yellow-600" 
                                                   href="{{ route('risk-indicators.edit', [
                                                       'riskId' => $indicator->risk_indicator_risk_id,
                                                       'indicatorId' => $indicator->risk_indicator_id
                                                   ]) }}">
                                                    <i data-feather="edit" class="w-4 h-4"></i>
                                                </a>
                                                <button type="button" 
                                                        class="flex items-center text-green-600 update-value-btn"
                                                        data-indicator-id="{{ $indicator->risk_indicator_id }}"
                                                        data-indicator-name="{{ $indicator->indicator_name }}"
                                                        data-current-value="{{ $indicator->current_value }}"
                                                        data-unit="{{ $indicator->unit }}">
                                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($indicators->hasPages())
                    <div class="flex flex-col sm:flex-row items-center p-5 border-t border-gray-200">
                        <div class="text-sm text-gray-500">
                            Menampilkan {{ $indicators->firstItem() }} - {{ $indicators->lastItem() }} dari {{ $indicators->total() }} indikator
                        </div>
                        <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                            {{ $indicators->appends(request()->query())->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="activity" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Tidak ada data indikator</h3>
                        <p class="text-gray-500 mb-6">Tidak ada data indikator yang ditemukan berdasarkan filter yang dipilih</p>
                        <a href="{{ route('risk-indicators.index') }}" class="btn btn-primary">
                            <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Pilih Risiko
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Value -->
<div class="modal" id="update-value-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Update Nilai Indikator</h2>
                <button data-dismiss="modal" class="btn btn-outline-secondary hidden sm:flex">
                    <i data-feather="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form id="update-value-form" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label">Nama Indikator</label>
                        <div class="font-medium" id="modal-indicator-name"></div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="current_value" class="form-label">Nilai Saat Ini <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" 
                                   id="current_value" 
                                   name="current_value" 
                                   class="form-control w-full" 
                                   step="0.01"
                                   required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500" id="modal-unit"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="measurement_date" class="form-label">Tanggal Pengukuran <span class="text-red-500">*</span></label>
                        <input type="date" 
                               id="measurement_date" 
                               name="measurement_date" 
                               class="form-control w-full" 
                               value="{{ date('Y-m-d') }}"
                               required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea id="notes" 
                                  name="notes" 
                                  class="form-control w-full" 
                                  rows="3"
                                  placeholder="Catatan mengenai pengukuran"></textarea>
                    </div>
                    
                    <input type="hidden" id="indicator_id" name="indicator_id">
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-outline-secondary w-20">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary w-20">
                        Simpan
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
    
    // Update value modal
    const updateValueButtons = document.querySelectorAll('.update-value-btn');
    const modal = document.getElementById('update-value-modal');
    const form = document.getElementById('update-value-form');
    
    updateValueButtons.forEach(button => {
        button.addEventListener('click', function() {
            const indicatorId = this.dataset.indicatorId;
            const indicatorName = this.dataset.indicatorName;
            const currentValue = this.dataset.currentValue;
            const unit = this.dataset.unit || '';
            
            // Set modal values
            document.getElementById('modal-indicator-name').textContent = indicatorName;
            document.getElementById('current_value').value = currentValue || '';
            document.getElementById('modal-unit').textContent = unit;
            document.getElementById('indicator_id').value = indicatorId;
            
            // Set form action
            form.action = `/risk-indicators/${indicatorId}/update-value`;
            
            // Show modal
            modal.classList.add('show');
            modal.style.display = 'block';
        });
    });
    
    // Close modal
    const closeButtons = document.querySelectorAll('[data-dismiss="modal"]');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            modal.classList.remove('show');
            modal.style.display = 'none';
        });
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const indicatorId = document.getElementById('indicator_id').value;
        
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
                modal.classList.remove('show');
                modal.style.display = 'none';
                
                // Reload page after 1 second
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Terjadi kesalahan saat mengupdate nilai');
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