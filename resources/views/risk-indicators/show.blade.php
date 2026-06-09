@extends('layouts.master')

@section('title', 'Detail Indikator Risiko - SIMR')

@section('page-title', 'Detail Indikator Risiko')

@section('breadcrumb')
@parent
<li class="breadcrumb-item"><a href="{{ route('risks.index') }}">Risiko</a></li>
<li class="breadcrumb-item"><a href="{{ route('risk-indicators.index', $risk->risk_id) }}">Indikator</a></li>
<li class="breadcrumb-item active">Detail</li>
@endsection

@section('page-action')
<a href="{{ route('risk-indicators.index', $risk->risk_id) }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
</a>
<div class="flex items-center space-x-2">
    <a href="{{ route('risk-indicators.edit', [$risk->risk_id, $indicator->risk_indicator_id]) }}" 
       class="btn btn-primary shadow-md">
        <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit
    </a>
    <form action="{{ route('risk-indicators.destroy', [$risk->risk_id, $indicator->risk_indicator_id]) }}" 
          method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus indikator ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger shadow-md">
            <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus
        </button>
    </form>
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
                        <i data-feather="activity" class="w-5 h-5 inline mr-2 text-blue-500"></i>
                        Detail Indikator Risiko
                    </h2>
                    <div class="text-gray-600 text-sm mt-1">
                        Kode Risiko: <span class="font-medium">{{ $risk->risk_code }}</span> • 
                        Jenis: <span class="font-medium">
                            @switch($indicator->indicator_type)
                                @case('akar_masalah') Akar Masalah @break
                                @case('penyebab') Penyebab @break
                                @case('dampak') Dampak @break
                                @case('lainnya') Lainnya @break
                            @endswitch
                        </span>
                    </div>
                </div>
                <div class="mt-3 sm:mt-0">
                    @php
                        $isExceeded = $indicator->current_value && $indicator->current_value > $indicator->threshold;
                    @endphp
                    @if($indicator->current_value)
                        @if($isExceeded)
                            <span class="px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i data-feather="alert-triangle" class="w-4 h-4 inline mr-2"></i>
                                Melebihi Ambang Batas
                            </span>
                        @else
                            <span class="px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i data-feather="check-circle" class="w-4 h-4 inline mr-2"></i>
                                Normal
                            </span>
                        @endif
                    @else
                        <span class="px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            Belum Diukur
                        </span>
                    @endif
                </div>
            </div>
            
            <!-- Value Comparison -->
            <div class="p-5 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                    <div>
                        <div class="text-sm text-gray-600 mb-2">NILAI TERAKHIR</div>
                        @if($indicator->current_value)
                            @php
                                $isExceeded = $indicator->isExceeded();
                            @endphp
                            <div class="text-4xl font-bold 
                                @if($isExceeded) text-red-600
                                @else text-green-600
                                @endif">
                                {{ number_format($indicator->current_value, 2) }}
                            </div>
                            <div class="text-gray-500">{{ $indicator->unit }}</div>
                            @if($indicator->last_measurement_date)
                                <div class="text-xs text-gray-400 mt-1">
                                    {{ \Carbon\Carbon::parse($indicator->last_measurement_date)->format('d M Y') }}
                                </div>
                            @endif
                        @else
                            <div class="text-2xl font-bold text-gray-400">-</div>
                            <div class="text-gray-400">Belum diukur</div>
                        @endif
                    </div>
                    
                    <div class="flex items-center justify-center">
                        <div class="relative">
                            <div class="text-sm text-gray-600 mb-2">STATUS</div>
                            @if($indicator->current_value)
                                @if($isExceeded)
                                    <div class="text-3xl font-bold text-red-600">
                                        ↑ {{ number_format($indicator->current_value - $indicator->threshold, 2) }}
                                    </div>
                                    <div class="text-sm text-red-500">Di atas ambang batas</div>
                                @else
                                    <div class="text-3xl font-bold text-green-600">
                                        ↓ {{ number_format($indicator->threshold - $indicator->current_value, 2) }}
                                    </div>
                                    <div class="text-sm text-green-500">Di bawah ambang batas</div>
                                @endif
                            @else
                                <div class="text-2xl font-bold text-gray-400">-</div>
                                <div class="text-gray-400">Tidak tersedia</div>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-600 mb-2">AMBANG BATAS</div>
                        <div class="text-4xl font-bold text-blue-600">
                            {{ number_format($indicator->threshold, 2) }}
                        </div>
                        <div class="text-gray-500">{{ $indicator->unit }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Information Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Indicator Details -->
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="info" class="w-5 h-5 mr-2 text-teal-500"></i>
                            Informasi Indikator
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="form-label">Nama Indikator</label>
                                <div class="font-medium text-lg">{{ $indicator->indicator_name }}</div>
                            </div>
                            
                            <div>
                                <label class="form-label">Jenis Indikator</label>
                                <div class="font-medium text-lg">
                                    @switch($indicator->indicator_type)
                                        @case('akar_masalah') Akar Masalah @break
                                        @case('penyebab') Penyebab @break
                                        @case('dampak') Dampak @break
                                        @case('lainnya') Lainnya @break
                                    @endswitch
                                </div>
                            </div>
                            
                            <div>
                                <label class="form-label">Satuan</label>
                                <div class="font-medium">
                                    @if($indicator->unit)
                                        <span class="px-2 py-1 bg-gray-100 rounded">{{ $indicator->unit }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div>
                                <label class="form-label">Frekuensi Pengukuran</label>
                                <div class="font-medium">
                                    @if($indicator->measurement_frequency)
                                        @switch($indicator->measurement_frequency)
                                            @case('realtime') Realtime @break
                                            @case('harian') Harian @break
                                            @case('mingguan') Mingguan @break
                                            @case('bulanan') Bulanan @break
                                            @case('triwulan') Triwulan @break
                                            @case('semester') Semester @break
                                            @case('tahunan') Tahunan @break
                                            @default {{ $indicator->measurement_frequency }}
                                        @endswitch
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </div>
                            
                            @if($indicator->data_source)
                            <div>
                                <label class="form-label">Sumber Data</label>
                                <div class="font-medium">{{ $indicator->data_source }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Indicator Description -->
                @if($indicator->indicator_description)
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="file-text" class="w-5 h-5 mr-2 text-blue-500"></i>
                            Deskripsi Indikator
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="prose max-w-none">
                            {!! nl2br(e($indicator->indicator_description)) !!}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Measurement History -->
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="history" class="w-5 h-5 mr-2 text-purple-500"></i>
                            Riwayat Pengukuran
                        </h3>
                        <button id="load-measurements" class="btn btn-outline-secondary btn-sm ml-auto">
                            <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i> Muat Ulang
                        </button>
                    </div>
                    <div class="p-5">
                        <div id="measurements-container" class="space-y-3">
                            <!-- Measurements will be loaded here -->
                            <div class="text-center py-8">
                                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                                    <i data-feather="loader" class="w-6 h-6 text-gray-400 animate-spin"></i>
                                </div>
                                <p class="text-gray-500">Memuat riwayat pengukuran...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Risk Info -->
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="alert-triangle" class="w-5 h-5 mr-2 text-red-500"></i>
                            Informasi Risiko
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="space-y-4">
                            <div>
                                <label class="form-label">Kode Risiko</label>
                                <div class="font-medium">{{ $risk->risk_code }}</div>
                            </div>
                            
                            <div>
                                <label class="form-label">Deskripsi Risiko</label>
                                <div class="font-medium">{{ Str::limit($risk->risk_description, 60) }}</div>
                            </div>
                            
                            <div>
                                <label class="form-label">Level Risiko</label>
                                @if($risk->risk_level)
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        @if($risk->risk_level == 'sangat_rendah') bg-green-100 text-green-800
                                        @elseif($risk->risk_level == 'rendah') bg-blue-100 text-blue-800
                                        @elseif($risk->risk_level == 'sedang') bg-yellow-100 text-yellow-800
                                        @elseif($risk->risk_level == 'tinggi') bg-orange-100 text-orange-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ $risk->risk_level_label }}
                                    </span>
                                @else
                                    <span class="text-gray-400">Belum dianalisis</span>
                                @endif
                            </div>
                            
                            <div>
                                <label class="form-label">Skor Risiko</label>
                                <div class="font-medium">
                                    @if($risk->risk_score)
                                        <span class="text-lg font-bold 
                                            @if($risk->risk_level == 'sangat_tinggi') text-red-600
                                            @elseif($risk->risk_level == 'tinggi') text-orange-600
                                            @elseif($risk->risk_level == 'sedang') text-yellow-600
                                            @elseif($risk->risk_level == 'rendah') text-blue-600
                                            @else text-green-600
                                            @endif">
                                            {{ $risk->risk_score }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <a href="{{ route('risks.show', $risk->risk_id) }}" 
                           class="btn btn-outline-primary w-full mt-6">
                            <i data-feather="external-link" class="w-4 h-4 mr-2"></i> Lihat Detail Risiko
                        </a>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="zap" class="w-5 h-5 mr-2 text-yellow-500"></i>
                            Aksi Cepat
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="space-y-3">
                            <button type="button" 
                                    class="btn btn-primary w-full update-value-btn"
                                    data-indicator-id="{{ $indicator->risk_indicator_id }}"
                                    data-indicator-name="{{ $indicator->indicator_name }}"
                                    data-current-value="{{ $indicator->current_value }}"
                                    data-unit="{{ $indicator->unit }}">
                                <i data-feather="edit-2" class="w-4 h-4 mr-2"></i> Update Nilai
                            </button>
                            
                            <a href="{{ route('risk-indicators.edit', [$risk->risk_id, $indicator->risk_indicator_id]) }}" 
                               class="btn btn-outline-secondary w-full">
                                <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit Indikator
                            </a>
                            
                            <form action="{{ route('risk-indicators.destroy', [$risk->risk_id, $indicator->risk_indicator_id]) }}" 
                                  method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus indikator ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-full">
                                    <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus Indikator
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- System Information -->
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="database" class="w-5 h-5 mr-2 text-gray-500"></i>
                            Informasi Sistem
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="space-y-3">
                            <div>
                                <label class="form-label">ID Indikator</label>
                                <div class="font-mono text-sm text-gray-600">{{ $indicator->risk_indicator_id }}</div>
                            </div>
                            
                            <div>
                                <label class="form-label">Dibuat Pada</label>
                                <div class="text-sm text-gray-600">
                                    {{ $indicator->created_at->format('d M Y H:i') }}
                                </div>
                            </div>
                            
                            <div>
                                <label class="form-label">Diperbarui Pada</label>
                                <div class="text-sm text-gray-600">
                                    {{ $indicator->updated_at->format('d M Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
    
    // Load measurements
    function loadMeasurements() {
        const container = document.getElementById('measurements-container');
        const indicatorId = {{ $indicator->risk_indicator_id }};
        
        container.innerHTML = `
            <div class="text-center py-8">
                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <i data-feather="loader" class="w-6 h-6 text-gray-400 animate-spin"></i>
                </div>
                <p class="text-gray-500">Memuat riwayat pengukuran...</p>
            </div>
        `;
        feather.replace();
        
        fetch(`/risk-indicators/${indicatorId}/measurements`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    let html = '';
                    data.forEach(measurement => {
                        const date = new Date(measurement.measurement_date);
                        const formattedDate = date.toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        });
                        
                        html += `
                            <div class="flex items-center p-3 bg-white rounded-lg border">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <i data-feather="bar-chart" class="w-5 h-5 text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium">{{ number_format($indicator->threshold, 2) }} {{ $indicator->unit }}</div>
                                    <div class="text-sm text-gray-600">
                                        ${measurement.measured_value} {{ $indicator->unit }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        ${formattedDate}
                                        ${measurement.notes ? ' • ' + measurement.notes : ''}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold ${measurement.measured_value > {{ $indicator->threshold }} ? 'text-red-600' : 'text-green-600'}">
                                        ${measurement.measured_value > {{ $indicator->threshold }} ? '+' : ''}${(measurement.measured_value - {{ $indicator->threshold }}).toFixed(2)}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $indicator->unit }}</div>
                                </div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="text-center py-8">
                            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                                <i data-feather="bar-chart" class="w-8 h-8 text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada riwayat pengukuran</h3>
                            <p class="text-gray-500">Update nilai indikator untuk melihat riwayat pengukuran</p>
                        </div>
                    `;
                }
                feather.replace();
            })
            .catch(error => {
                console.error('Error loading measurements:', error);
                container.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                            <i data-feather="alert-circle" class="w-8 h-8 text-red-600"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Gagal memuat data</h3>
                        <p class="text-gray-500">Terjadi kesalahan saat memuat riwayat pengukuran</p>
                    </div>
                `;
                feather.replace();
            });
    }
    
    // Load measurements on page load
    loadMeasurements();
    
    // Reload measurements button
    document.getElementById('load-measurements').addEventListener('click', loadMeasurements);
    
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