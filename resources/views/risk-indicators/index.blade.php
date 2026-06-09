@extends('layouts.master')

@section('title', 'Indikator Risiko - SIMR')

@section('page-title', 'Indikator Risiko')

@section('breadcrumb')
@parent
<li class="breadcrumb-item"><a href="{{ route('risks.index') }}">Risiko</a></li>
<li class="breadcrumb-item active">Indikator</li>
@endsection

@section('page-action')
<a href="{{ route('risks.show', $risk->risk_id) }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Risiko
</a>
<a href="{{ route('risk-indicators.create', $risk->risk_id) }}" class="btn btn-primary shadow-md">
    <i data-feather="plus" class="w-4 h-4 mr-2"></i> Tambah Indikator
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
                    {{ $risk->risk_level_label }}
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
                        <div class="font-medium">{{ Str::limit($risk->risk_description, 60) }}</div>
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
            @php
                $indicatorCounts = [
                    'akar_masalah' => $indicators->where('indicator_type', 'akar_masalah')->count(),
                    'penyebab' => $indicators->where('indicator_type', 'penyebab')->count(),
                    'dampak' => $indicators->where('indicator_type', 'dampak')->count(),
                    'lainnya' => $indicators->where('indicator_type', 'lainnya')->count(),
                ];
                
                $exceededCount = $indicators->filter(function($indicator) {
                        return $indicator->isExceeded();
                    })->count();            
            @endphp
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-100">
                                <i data-feather="activity" class="w-6 h-6 text-blue-600"></i>
                            </div>
                            <div class="ml-auto">
                                <div class="text-3xl font-bold leading-8">{{ $indicators->total() }}</div>
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
                                <div class="text-3xl font-bold leading-8">{{ $exceededCount }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Melebihi Ambang Batas</div>
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
                                <div class="text-3xl font-bold leading-8">{{ $indicatorCounts['dampak'] }}</div>
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
                                <div class="text-3xl font-bold leading-8">{{ $indicatorCounts['penyebab'] }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Indikator Penyebab</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicator Type Summary -->
        <div class="intro-y box mb-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="pie-chart" class="w-5 h-5 mr-2 text-teal-500"></i>
                    Ringkasan Jenis Indikator
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @foreach($indicatorCounts as $type => $count)
                        @php
                            $typeConfig = [
                                'akar_masalah' => ['label' => 'Akar Masalah', 'color' => 'blue', 'icon' => 'root'],
                                'penyebab' => ['label' => 'Penyebab', 'color' => 'orange', 'icon' => 'search'],
                                'dampak' => ['label' => 'Dampak', 'color' => 'red', 'icon' => 'target'],
                                'lainnya' => ['label' => 'Lainnya', 'color' => 'gray', 'icon' => 'more-horizontal'],
                            ][$type];
                        @endphp
                        
                        <div class="bg-white border rounded-lg p-4 text-center hover:shadow-md transition-shadow">
                            <div class="w-12 h-12 rounded-full bg-{{ $typeConfig['color'] }}-100 flex items-center justify-center mx-auto mb-3">
                                <i data-feather="{{ $typeConfig['icon'] }}" class="w-6 h-6 text-{{ $typeConfig['color'] }}-600"></i>
                            </div>
                            <div class="text-2xl font-bold text-{{ $typeConfig['color'] }}-600">{{ $count }}</div>
                            <div class="text-gray-600">{{ $typeConfig['label'] }}</div>
                            @if($indicators->total() > 0)
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ round(($count / $indicators->total()) * 100, 1) }}%
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Daftar Indikator Risiko
                    <span class="text-gray-500 text-sm ml-2">({{ $indicators->total() }} data)</span>
                </h2>
                
                @if($indicators->count() > 0)
                <div class="flex items-center space-x-2 mt-3 sm:mt-0">
                    <span class="text-sm text-gray-600">Filter:</span>
                    <select id="filter-indicator-type" class="form-select w-40">
                        <option value="">Semua Jenis</option>
                        <option value="akar_masalah">Akar Masalah</option>
                        <option value="penyebab">Penyebab</option>
                        <option value="dampak">Dampak</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                @endif
            </div>
            <div class="p-5">
                @if($indicators->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-report -mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">JENIS INDIKATOR</th>
                                    <th class="whitespace-nowrap">NAMA INDIKATOR</th>
                                    <th class="whitespace-nowrap">AMBANG BATAS</th>
                                    <th class="whitespace-nowrap">NILAI SAAT INI</th>
                                    <th class="whitespace-nowrap">STATUS</th>
                                    <th class="whitespace-nowrap">SATUAN</th>
                                    <th class="whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($indicators as $indicator)
                                    @php
                                        $isExceeded = $indicator->current_value && $indicator->current_value > $indicator->threshold;
                                    @endphp
                                    
                                    <tr class="intro-x hover:bg-gray-50" data-indicator-type="{{ $indicator->indicator_type }}">
                                        <td>
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full 
                                                    @if($indicator->indicator_type == 'akar_masalah') bg-blue-100
                                                    @elseif($indicator->indicator_type == 'penyebab') bg-orange-100
                                                    @elseif($indicator->indicator_type == 'dampak') bg-red-100
                                                    @else bg-gray-100
                                                    @endif flex items-center justify-center mr-3">
                                                    <i data-feather="
                                                        @if($indicator->indicator_type == 'akar_masalah') root
                                                        @elseif($indicator->indicator_type == 'penyebab') search
                                                        @elseif($indicator->indicator_type == 'dampak') target
                                                        @else more-horizontal
                                                        @endif
                                                    " class="w-5 h-5 
                                                        @if($indicator->indicator_type == 'akar_masalah') text-blue-600
                                                        @elseif($indicator->indicator_type == 'penyebab') text-orange-600
                                                        @elseif($indicator->indicator_type == 'dampak') text-red-600
                                                        @else text-gray-600
                                                        @endif">
                                                    </i>
                                                </div>
                                                <div>
                                                    <div class="font-medium">
                                                        @switch($indicator->indicator_type)
                                                            @case('akar_masalah') Akar Masalah @break
                                                            @case('penyebab') Penyebab @break
                                                            @case('dampak') Dampak @break
                                                            @case('lainnya') Lainnya @break
                                                        @endswitch
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="font-medium">{{ $indicator->indicator_name }}</div>
                                            <div class="text-gray-500 text-xs mt-0.5">
                                                {{ Str::limit($indicator->indicator_description, 40) }}
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="font-medium">{{ number_format($indicator->threshold, 2) }}</div>
                                            <div class="text-gray-500 text-xs">{{ $indicator->unit }}</div>
                                        </td>
                                        
                                        <td>
                                            @if($indicator->current_value)
                                                @if($isExceeded)
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <i data-feather="alert-triangle" class="w-3 h-3 inline mr-1"></i>
                                                        Melebihi Batas
                                                    </span>
                                                    <div class="text-red-500 text-xs mt-1">
                                                        +{{ number_format($indicator->current_value - $indicator->threshold, 2) }}
                                                    </div>
                                                @else
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i data-feather="check-circle" class="w-3 h-3 inline mr-1"></i>
                                                        Normal
                                                    </span>
                                                    <div class="text-green-500 text-xs mt-1">
                                                        -{{ number_format($indicator->threshold - $indicator->current_value, 2) }}
                                                    </div>
                                                @endif
                                            @else
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Belum Diukur
                                                </span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <span class="px-2 py-1 bg-gray-100 rounded text-sm">{{ $indicator->unit ?? '-' }}</span>
                                        </td>
                                        
                                        <td class="table-report__action w-56">
                                            <div class="flex justify-center items-center">
                                                <button type="button" 
                                                        class="flex items-center mr-3 text-blue-600 update-value-btn"
                                                        data-indicator-id="{{ $indicator->risk_indicator_id }}"
                                                        data-indicator-name="{{ $indicator->indicator_name }}"
                                                        data-current-value="{{ $indicator->current_value }}"
                                                        data-unit="{{ $indicator->unit }}">
                                                    <i data-feather="edit-2" class="w-4 h-4 mr-1"></i> Update Nilai
                                                </button>
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('risk-indicators.show', [$risk->risk_id, $indicator->risk_indicator_id]) }}">
                                                    <i data-feather="eye" class="w-4 h-4 mr-1"></i> Detail
                                                </a>
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('risk-indicators.edit', [$risk->risk_id, $indicator->risk_indicator_id]) }}">
                                                    <i data-feather="edit" class="w-4 h-4 mr-1"></i> Edit
                                                </a>
                                                <form action="{{ route('risk-indicators.destroy', [$risk->risk_id, $indicator->risk_indicator_id]) }}" 
                                                      method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus indikator ini?')">
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
                    @if($indicators->hasPages())
                    <div class="flex flex-col sm:flex-row items-center p-5 border-t border-gray-200">
                        <div class="text-sm text-gray-500">
                            Menampilkan {{ $indicators->firstItem() }} - {{ $indicators->lastItem() }} dari {{ $indicators->total() }} indikator
                        </div>
                        <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                            {{ $indicators->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="activity" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada indikator risiko</h3>
                        <p class="text-gray-500 mb-6">Indikator membantu memantau dan mengukur faktor-faktor yang mempengaruhi risiko</p>
                        <a href="{{ route('risk-indicators.create', $risk->risk_id) }}" 
                           class="btn btn-primary">
                            <i data-feather="plus" class="w-4 h-4 mr-2"></i> Tambah Indikator Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Exceeded Thresholds Alert -->
        @if($exceededCount > 0)
        <div class="intro-y box mt-6 border-red-200">
            <div class="flex items-center p-5 border-b border-red-200 bg-red-50">
                <h2 class="font-medium text-base mr-auto text-red-700">
                    <i data-feather="alert-triangle" class="w-5 h-5 mr-2"></i>
                    Peringatan: Indikator Melebihi Ambang Batas
                </h2>
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    {{ $exceededCount }} indikator
                </span>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    @foreach($indicators as $indicator)
                        @if($indicator->current_value && $indicator->current_value > $indicator->threshold)
                            <div class="flex items-center p-3 bg-red-50 rounded-lg border border-red-100">
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                    <i data-feather="alert-triangle" class="w-5 h-5 text-red-600"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium">{{ $indicator->indicator_name }}</div>
                                    <div class="text-sm text-gray-600">
                                        Melebihi ambang batas: {{ number_format($indicator->current_value, 2) }} > {{ number_format($indicator->threshold, 2) }} {{ $indicator->unit }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-red-600">
                                        +{{ number_format($indicator->current_value - $indicator->threshold, 2) }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $indicator->unit }}</div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif
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
    
    // Filter by indicator type
    const filterSelect = document.getElementById('filter-indicator-type');
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const selectedType = this.value;
            const rows = document.querySelectorAll('tbody tr[data-indicator-type]');
            
            rows.forEach(row => {
                if (!selectedType || row.dataset.indicatorType === selectedType) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
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