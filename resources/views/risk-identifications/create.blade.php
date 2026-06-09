@extends('layouts.master')

@section('title', 'Identifikasi Risiko - SIMR')

@section('page-title', 'Identifikasi Risiko: ' . $risk->risk_code)

@section('page-action')
<div class="w-full sm:w-auto flex">
    <a href="{{ route('risks.show', $risk->risk_id) }}" class="btn btn-outline-secondary shadow-md mr-2">
        <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
    </a>
</div>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 xl:col-span-8">
        <!-- Form Identifikasi Risiko -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                    <i data-feather="search" class="w-5 h-5 text-blue-600"></i>
                </div>
                <h2 class="font-medium text-base mr-auto">
                    {{ $identification ? 'Edit' : 'Tambah' }} Identifikasi Risiko
                    <span class="text-gray-500 text-sm ml-2">{{ $risk->risk_code }}</span>
                </h2>
                <div class="flex items-center">
                    @php
                        $levelColors = [
                            'sangat_rendah' => 'bg-green-100 text-green-800',
                            'rendah' => 'bg-yellow-100 text-yellow-800',
                            'sedang' => 'bg-orange-100 text-orange-800',
                            'tinggi' => 'bg-red-100 text-red-800',
                            'sangat_tinggi' => 'bg-red-600 text-white'
                        ];
                        $color = $levelColors[$risk->risk_level] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="px-2 py-1 text-xs rounded font-medium {{ $color }}">
                        {{ ucfirst(str_replace('_', ' ', $risk->risk_level)) }}
                    </span>
                </div>
            </div>
            <form method="POST" action="{{ route('risk-identifications.store') }}" class="p-5">
                @csrf
                
                <!-- Hidden input untuk risk_id -->
                <input type="hidden" name="risk_id" value="{{ $risk->risk_id }}">
                
                @if($identification)
                    @method('PUT')
                @endif
                
                <div class="mb-6">
                    <h4 class="font-medium mb-3">Informasi Risiko</h4>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="font-medium">{{ $risk->risk_code }}</div>
                        <div class="text-sm text-gray-600 mt-1">{{ $risk->risk_description }}</div>
                        <div class="flex items-center mt-2">
                            <div class="text-xs text-gray-500 mr-3">
                                Proyek: <span class="font-medium">{{ $risk->project->pro_nama ?? 'N/A' }}</span>
                            </div>
                            <div class="text-xs text-gray-500">
                                Organisasi: <span class="font-medium">{{ $risk->organization->organization_code ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Jenis Kerugian -->
                <div class="mb-6">
                    <label class="form-label text-lg font-medium mb-3">
                        <i data-feather="dollar-sign" class="w-5 h-5 inline mr-2 text-red-600"></i>
                        Jenis Kerugian
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $lossTypes = [
                                [
                                    'value' => 'Reputasi',
                                    'icon' => 'thumbs-down',
                                    'color' => 'red',
                                    'title' => 'Kerugian Reputasi',
                                    'desc' => 'Dampak terhadap citra dan kepercayaan publik'
                                ],
                                [
                                    'value' => 'Operasional',
                                    'icon' => 'settings',
                                    'color' => 'orange',
                                    'title' => 'Kerugian Operasional',
                                    'desc' => 'Gangguan pada proses bisnis dan operasional'
                                ],
                                [
                                    'value' => 'Kepatuhan',
                                    'icon' => 'file-text',
                                    'color' => 'yellow',
                                    'title' => 'Kerugian Kepatuhan',
                                    'desc' => 'Pelanggaran regulasi dan ketentuan hukum'
                                ],
                                [
                                    'value' => 'Lainnya',
                                    'icon' => 'more-horizontal',
                                    'color' => 'gray',
                                    'title' => 'Lainnya',
                                    'desc' => 'Jenis kerugian lainnya yang tidak termasuk di atas'
                                ]
                            ];
                        @endphp
                        
                        @foreach($lossTypes as $type)
                            <div class="p-4 border rounded-lg hover:bg-gray-50 cursor-pointer loss-type-card"
                                 onclick="selectLossType('{{ $type['value'] }}')">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-{{ $type['color'] }}-100 flex items-center justify-center mr-3">
                                        <i data-feather="{{ $type['icon'] }}" class="w-5 h-5 text-{{ $type['color'] }}-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $type['title'] }}</div>
                                        <div class="text-sm text-gray-600">{{ $type['desc'] }}</div>
                                    </div>
                                    <input type="radio" id="loss_type_{{ strtolower($type['value']) }}" 
                                           name="loss_type" value="{{ $type['value'] }}" 
                                           class="form-radio" 
                                           {{ old('loss_type', $identification->loss_type ?? '') == $type['value'] ? 'checked' : '' }}>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('loss_type')
                        <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Jenis Pelanggaran -->
                <div class="mb-6">
                    <label class="form-label text-lg font-medium mb-3">
                        <i data-feather="alert-octagon" class="w-5 h-5 inline mr-2 text-orange-600"></i>
                        Jenis Pelanggaran
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $violationTypes = [
                                [
                                    'value' => 'Hukum',
                                    'icon' => 'scale',
                                    'color' => 'red',
                                    'title' => 'Pelanggaran Hukum',
                                    'desc' => 'Tidak mematuhi peraturan perundang-undangan'
                                ],
                                [
                                    'value' => 'SOP',
                                    'icon' => 'clipboard',
                                    'color' => 'blue',
                                    'title' => 'Pelanggaran SOP',
                                    'desc' => 'Tidak mengikuti prosedur operasional standar'
                                ],
                                [
                                    'value' => 'Kontrak',
                                    'icon' => 'file',
                                    'color' => 'purple',
                                    'title' => 'Pelanggaran Kontrak',
                                    'desc' => 'Wanprestasi dalam pelaksanaan kontrak'
                                ],
                                [
                                    'value' => 'Lainnya',
                                    'icon' => 'more-horizontal',
                                    'color' => 'gray',
                                    'title' => 'Lainnya',
                                    'desc' => 'Jenis pelanggaran lainnya'
                                ]
                            ];
                        @endphp
                        
                        @foreach($violationTypes as $type)
                            <div class="p-4 border rounded-lg hover:bg-gray-50 cursor-pointer violation-type-card"
                                 onclick="selectViolationType('{{ $type['value'] }}')">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-{{ $type['color'] }}-100 flex items-center justify-center mr-3">
                                        <i data-feather="{{ $type['icon'] }}" class="w-5 h-5 text-{{ $type['color'] }}-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $type['title'] }}</div>
                                        <div class="text-sm text-gray-600">{{ $type['desc'] }}</div>
                                    </div>
                                    <input type="radio" id="violation_type_{{ strtolower($type['value']) }}" 
                                           name="violation_type" value="{{ $type['value'] }}" 
                                           class="form-radio"
                                           {{ old('violation_type', $identification->violation_type ?? '') == $type['value'] ? 'checked' : '' }}>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('violation_type')
                        <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Jenis Kegagalan -->
                <div class="mb-6">
                    <label class="form-label text-lg font-medium mb-3">
                        <i data-feather="x-circle" class="w-5 h-5 inline mr-2 text-red-600"></i>
                        Jenis Kegagalan
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $failureTypes = [
                                [
                                    'value' => 'Manusia',
                                    'icon' => 'users',
                                    'color' => 'teal',
                                    'title' => 'Kegagalan Manusia',
                                    'desc' => 'Kesalahan atau kelalaian manusia dalam pekerjaan'
                                ],
                                [
                                    'value' => 'Proses',
                                    'icon' => 'repeat',
                                    'color' => 'indigo',
                                    'title' => 'Kegagalan Proses',
                                    'desc' => 'Prosedur atau alur kerja yang tidak efektif'
                                ],
                                [
                                    'value' => 'Sistem',
                                    'icon' => 'cpu',
                                    'color' => 'pink',
                                    'title' => 'Kegagalan Sistem',
                                    'desc' => 'Masalah teknis pada sistem atau peralatan'
                                ],
                                [
                                    'value' => 'Lainnya',
                                    'icon' => 'more-horizontal',
                                    'color' => 'gray',
                                    'title' => 'Lainnya',
                                    'desc' => 'Jenis kegagalan lainnya'
                                ]
                            ];
                        @endphp
                        
                        @foreach($failureTypes as $type)
                            <div class="p-4 border rounded-lg hover:bg-gray-50 cursor-pointer failure-type-card"
                                 onclick="selectFailureType('{{ $type['value'] }}')">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-{{ $type['color'] }}-100 flex items-center justify-center mr-3">
                                        <i data-feather="{{ $type['icon'] }}" class="w-5 h-5 text-{{ $type['color'] }}-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $type['title'] }}</div>
                                        <div class="text-sm text-gray-600">{{ $type['desc'] }}</div>
                                    </div>
                                    <input type="radio" id="failure_type_{{ strtolower($type['value']) }}" 
                                           name="failure_type" value="{{ $type['value'] }}" 
                                           class="form-radio"
                                           {{ old('failure_type', $identification->failure_type ?? '') == $type['value'] ? 'checked' : '' }}>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('failure_type')
                        <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Jenis Kesalahan -->
                <div class="mb-6">
                    <label class="form-label text-lg font-medium mb-3">
                        <i data-feather="alert-triangle" class="w-5 h-5 inline mr-2 text-yellow-600"></i>
                        Jenis Kesalahan
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $errorTypes = [
                                [
                                    'value' => 'Human Error',
                                    'icon' => 'user-x',
                                    'color' => 'amber',
                                    'title' => 'Human Error',
                                    'desc' => 'Kesalahan yang disebabkan oleh faktor manusia'
                                ],
                                [
                                    'value' => 'Technical Error',
                                    'icon' => 'tool',
                                    'color' => 'rose',
                                    'title' => 'Technical Error',
                                    'desc' => 'Kesalahan teknis pada sistem atau peralatan'
                                ],
                                [
                                    'value' => 'Lainnya',
                                    'icon' => 'more-horizontal',
                                    'color' => 'gray',
                                    'title' => 'Lainnya',
                                    'desc' => 'Jenis kesalahan lainnya'
                                ]
                            ];
                        @endphp
                        
                        @foreach($errorTypes as $type)
                            @php
                                $id = strtolower(str_replace(' ', '_', $type['value']));
                            @endphp
                            <div class="p-4 border rounded-lg hover:bg-gray-50 cursor-pointer error-type-card"
                                 onclick="selectErrorType('{{ $type['value'] }}')">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-{{ $type['color'] }}-100 flex items-center justify-center mr-3">
                                        <i data-feather="{{ $type['icon'] }}" class="w-5 h-5 text-{{ $type['color'] }}-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $type['title'] }}</div>
                                        <div class="text-sm text-gray-600">{{ $type['desc'] }}</div>
                                    </div>
                                    <input type="radio" id="error_type_{{ $id }}" 
                                           name="error_type" value="{{ $type['value'] }}" 
                                           class="form-radio"
                                           {{ old('error_type', $identification->error_type ?? '') == $type['value'] ? 'checked' : '' }}>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('error_type')
                        <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Summary -->
                <div class="p-4 bg-gray-50 rounded-lg mb-6">
                    <h4 class="font-medium mb-3">Ringkasan Identifikasi</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-gray-500">Jenis Kerugian:</div>
                            <div id="summaryLoss" class="font-medium text-lg">
                                {{ old('loss_type', $identification->loss_type ?? 'Belum dipilih') }}
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Jenis Pelanggaran:</div>
                            <div id="summaryViolation" class="font-medium text-lg">
                                {{ old('violation_type', $identification->violation_type ?? 'Belum dipilih') }}
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Jenis Kegagalan:</div>
                            <div id="summaryFailure" class="font-medium text-lg">
                                {{ old('failure_type', $identification->failure_type ?? 'Belum dipilih') }}
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Jenis Kesalahan:</div>
                            <div id="summaryError" class="font-medium text-lg">
                                {{ old('error_type', $identification->error_type ?? 'Belum dipilih') }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="flex justify-end pt-5 border-t border-gray-200">
                    <a href="{{ route('risks.show', $risk->risk_id) }}" 
                       class="btn btn-outline-secondary w-24 mr-3">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary w-32">
                        <i data-feather="save" class="w-4 h-4 mr-2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-span-12 xl:col-span-4">
        <!-- Panduan Identifikasi -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="help-circle" class="w-5 h-5 mr-2"></i> Panduan Identifikasi
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="alert-triangle" class="w-4 h-4 text-red-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Identifikasi yang Akurat</h4>
                            <p class="text-sm text-gray-600">Pilih kategori yang paling tepat menggambarkan risiko</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="check-square" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Bisa Dipilih Lebih dari Satu</h4>
                            <p class="text-sm text-gray-600">Setiap kategori independen, pilih yang relevan</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="target" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Fokus pada Akar Masalah</h4>
                            <p class="text-sm text-gray-600">Identifikasi penyebab utama, bukan gejala</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="refresh-cw" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Dapat Diperbarui</h4>
                            <p class="text-sm text-gray-600">Identifikasi dapat disesuaikan seiring perkembangan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistik Global -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="pie-chart" class="w-5 h-5 mr-2"></i> Statistik Identifikasi
                </h2>
                <button onclick="loadStatistics()" class="btn btn-sm btn-outline-secondary" title="Refresh">
                    <i data-feather="refresh-cw" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="p-5">
                <div id="identificationStats">
                    <div class="text-center py-4">
                        <div class="spinner mx-auto mb-3">
                            <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-sm">Memuat statistik...</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contoh Identifikasi -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="book-open" class="w-5 h-5 mr-2"></i> Contoh Identifikasi
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="text-xs text-gray-500 mb-1">Proyek Konstruksi Jalan</div>
                        <div class="text-sm font-medium">Keterlambatan penyelesaian proyek</div>
                        <div class="flex flex-wrap gap-1 mt-2">
                            <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded">
                                <i data-feather="dollar-sign" class="w-3 h-3 inline mr-1"></i> Kerugian Operasional
                            </span>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">
                                <i data-feather="file" class="w-3 h-3 inline mr-1"></i> Pelanggaran Kontrak
                            </span>
                            <span class="px-2 py-1 bg-teal-100 text-teal-800 text-xs rounded">
                                <i data-feather="users" class="w-3 h-3 inline mr-1"></i> Kegagalan Manusia
                            </span>
                        </div>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="text-xs text-gray-500 mb-1">Pengadaan Material</div>
                        <div class="text-sm font-medium">Material tidak sesuai spesifikasi</div>
                        <div class="flex flex-wrap gap-1 mt-2">
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">
                                <i data-feather="file-text" class="w-3 h-3 inline mr-1"></i> Kerugian Kepatuhan
                            </span>
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded">
                                <i data-feather="scale" class="w-3 h-3 inline mr-1"></i> Pelanggaran Hukum
                            </span>
                            <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs rounded">
                                <i data-feather="repeat" class="w-3 h-3 inline mr-1"></i> Kegagalan Proses
                            </span>
                        </div>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="text-xs text-gray-500 mb-1">Sistem Monitoring</div>
                        <div class="text-sm font-medium">Gangguan sistem monitoring proyek</div>
                        <div class="flex flex-wrap gap-1 mt-2">
                            <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded">
                                <i data-feather="settings" class="w-3 h-3 inline mr-1"></i> Kerugian Operasional
                            </span>
                            <span class="px-2 py-1 bg-pink-100 text-pink-800 text-xs rounded">
                                <i data-feather="cpu" class="w-3 h-3 inline mr-1"></i> Kegagalan Sistem
                            </span>
                            <span class="px-2 py-1 bg-rose-100 text-rose-800 text-xs rounded">
                                <i data-feather="tool" class="w-3 h-3 inline mr-1"></i> Technical Error
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .cursor-pointer {
        cursor: pointer;
    }
    
    .spinner {
        display: inline-block;
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Custom radio button styling */
    .form-radio:checked ~ div .border {
        border-color: #0ea5e9;
    }
    
    /* Highlight selected option */
    .selected-option {
        background-color: #f0f9ff;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Load statistics on page load
    loadStatistics();
    
    // Highlight already selected options
    highlightSelectedOptions();
});

// Function to select loss type
function selectLossType(type) {
    document.getElementById('loss_type_' + type.toLowerCase()).checked = true;
    updateSummary();
    highlightSelected('loss', type);
}

// Function to select violation type
function selectViolationType(type) {
    document.getElementById('violation_type_' + type.toLowerCase()).checked = true;
    updateSummary();
    highlightSelected('violation', type);
}

// Function to select failure type
function selectFailureType(type) {
    document.getElementById('failure_type_' + type.toLowerCase()).checked = true;
    updateSummary();
    highlightSelected('failure', type);
}

// Function to select error type
function selectErrorType(type) {
    // Replace space with underscore for ID
    const id = type.toLowerCase().replace(' ', '_');
    document.getElementById('error_type_' + id).checked = true;
    updateSummary();
    highlightSelected('error', type);
}

// Update summary display
function updateSummary() {
    const lossType = document.querySelector('input[name="loss_type"]:checked');
    const violationType = document.querySelector('input[name="violation_type"]:checked');
    const failureType = document.querySelector('input[name="failure_type"]:checked');
    const errorType = document.querySelector('input[name="error_type"]:checked');
    
    document.getElementById('summaryLoss').textContent = 
        lossType ? lossType.value : 'Belum dipilih';
    document.getElementById('summaryViolation').textContent = 
        violationType ? violationType.value : 'Belum dipilih';
    document.getElementById('summaryFailure').textContent = 
        failureType ? failureType.value : 'Belum dipilih';
    document.getElementById('summaryError').textContent = 
        errorType ? errorType.value : 'Belum dipilih';
}

// Highlight selected option
function highlightSelected(category, type) {
    // Remove highlight from all options in this category
    const allCards = document.querySelectorAll(`.${category}-type-card`);
    allCards.forEach(card => {
        card.classList.remove('selected-option');
        card.classList.remove('bg-blue-50', 'border-blue-300');
    });
    
    // Highlight selected option
    const selectedRadio = document.querySelector(`input[name="${category}_type"][value="${type}"]`);
    if (selectedRadio) {
        const selectedCard = selectedRadio.closest(`.${category}-type-card`);
        if (selectedCard) {
            selectedCard.classList.add('selected-option', 'bg-blue-50', 'border-blue-300');
        }
    }
}

// Highlight already selected options on page load
function highlightSelectedOptions() {
    // Loss Type
    const selectedLoss = document.querySelector('input[name="loss_type"]:checked');
    if (selectedLoss) {
        highlightSelected('loss', selectedLoss.value);
    }
    
    // Violation Type
    const selectedViolation = document.querySelector('input[name="violation_type"]:checked');
    if (selectedViolation) {
        highlightSelected('violation', selectedViolation.value);
    }
    
    // Failure Type
    const selectedFailure = document.querySelector('input[name="failure_type"]:checked');
    if (selectedFailure) {
        highlightSelected('failure', selectedFailure.value);
    }
    
    // Error Type
    const selectedError = document.querySelector('input[name="error_type"]:checked');
    if (selectedError) {
        highlightSelected('error', selectedError.value);
    }
}

// Load statistics
function loadStatistics() {
    const statsContainer = document.getElementById('identificationStats');
    
    fetch('{{ route("risk-identifications.statistics") }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            let html = '<div class="space-y-6">';
            
            // Total Statistics
            const totalLoss = data.loss_types.reduce((sum, item) => sum + item.count, 0);
            const totalViolation = data.violation_types.reduce((sum, item) => sum + item.count, 0);
            const totalFailure = data.failure_types.reduce((sum, item) => sum + item.count, 0);
            const totalError = data.error_types.reduce((sum, item) => sum + item.count, 0);
            
            html += `
                <div class="grid grid-cols-2 gap-3">
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <div class="text-xl font-bold text-blue-600">${totalLoss}</div>
                        <div class="text-xs text-gray-600">Total Kerugian</div>
                    </div>
                    <div class="text-center p-3 bg-purple-50 rounded-lg">
                        <div class="text-xl font-bold text-purple-600">${totalViolation}</div>
                        <div class="text-xs text-gray-600">Total Pelanggaran</div>
                    </div>
                    <div class="text-center p-3 bg-teal-50 rounded-lg">
                        <div class="text-xl font-bold text-teal-600">${totalFailure}</div>
                        <div class="text-xs text-gray-600">Total Kegagalan</div>
                    </div>
                    <div class="text-center p-3 bg-amber-50 rounded-lg">
                        <div class="text-xl font-bold text-amber-600">${totalError}</div>
                        <div class="text-xs text-gray-600">Total Kesalahan</div>
                    </div>
                </div>
            `;
            
            // Loss Types
            if (data.loss_types.length > 0) {
                html += '<div>';
                html += '<h4 class="font-medium mb-2 text-sm">Jenis Kerugian</h4>';
                html += '<div class="space-y-2">';
                data.loss_types.forEach(item => {
                    const percentage = Math.round((item.count / totalLoss) * 100);
                    html += `
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="flex items-center">
                                    <i data-feather="${getLossTypeIcon(item.loss_type)}" class="w-3 h-3 mr-1 text-red-500"></i>
                                    ${item.loss_type}
                                </span>
                                <span>${item.count} (${percentage}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-red-500 h-2 rounded-full" style="width: ${percentage}%"></div>
                            </div>
                        </div>
                    `;
                });
                html += '</div></div>';
            }
            
            // Violation Types
            if (data.violation_types.length > 0) {
                html += '<div>';
                html += '<h4 class="font-medium mb-2 text-sm">Jenis Pelanggaran</h4>';
                html += '<div class="space-y-2">';
                data.violation_types.forEach(item => {
                    const percentage = Math.round((item.count / totalViolation) * 100);
                    html += `
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="flex items-center">
                                    <i data-feather="${getViolationTypeIcon(item.violation_type)}" class="w-3 h-3 mr-1 text-blue-500"></i>
                                    ${item.violation_type}
                                </span>
                                <span>${item.count} (${percentage}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: ${percentage}%"></div>
                            </div>
                        </div>
                    `;
                });
                html += '</div></div>';
            }
            
            // Failure Types
            if (data.failure_types.length > 0) {
                html += '<div>';
                html += '<h4 class="font-medium mb-2 text-sm">Jenis Kegagalan</h4>';
                html += '<div class="space-y-2">';
                data.failure_types.forEach(item => {
                    const percentage = Math.round((item.count / totalFailure) * 100);
                    html += `
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="flex items-center">
                                    <i data-feather="${getFailureTypeIcon(item.failure_type)}" class="w-3 h-3 mr-1 text-teal-500"></i>
                                    ${item.failure_type}
                                </span>
                                <span>${item.count} (${percentage}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-teal-500 h-2 rounded-full" style="width: ${percentage}%"></div>
                            </div>
                        </div>
                    `;
                });
                html += '</div></div>';
            }
            
            // Error Types
            if (data.error_types.length > 0) {
                html += '<div>';
                html += '<h4 class="font-medium mb-2 text-sm">Jenis Kesalahan</h4>';
                html += '<div class="space-y-2">';
                data.error_types.forEach(item => {
                    const percentage = Math.round((item.count / totalError) * 100);
                    html += `
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="flex items-center">
                                    <i data-feather="${getErrorTypeIcon(item.error_type)}" class="w-3 h-3 mr-1 text-amber-500"></i>
                                    ${item.error_type}
                                </span>
                                <span>${item.count} (${percentage}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-amber-500 h-2 rounded-full" style="width: ${percentage}%"></div>
                            </div>
                        </div>
                    `;
                });
                html += '</div></div>';
            }
            
            html += '</div>';
            statsContainer.innerHTML = html;
            
            // Replace feather icons in the new content
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
            statsContainer.innerHTML = `
                <div class="text-center py-4 text-gray-500">
                    <i data-feather="alert-circle" class="w-8 h-8 mx-auto mb-2"></i>
                    <p class="text-sm">Gagal memuat statistik</p>
                    <button onclick="loadStatistics()" class="text-blue-600 text-xs mt-2 hover:underline">
                        Coba lagi
                    </button>
                </div>
            `;
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
}

// Helper functions to get icons for each type
function getLossTypeIcon(type) {
    switch(type) {
        case 'Reputasi': return 'thumbs-down';
        case 'Operasional': return 'settings';
        case 'Kepatuhan': return 'file-text';
        default: return 'dollar-sign';
    }
}

function getViolationTypeIcon(type) {
    switch(type) {
        case 'Hukum': return 'scale';
        case 'SOP': return 'clipboard';
        case 'Kontrak': return 'file';
        default: return 'alert-octagon';
    }
}

function getFailureTypeIcon(type) {
    switch(type) {
        case 'Manusia': return 'users';
        case 'Proses': return 'repeat';
        case 'Sistem': return 'cpu';
        default: return 'x-circle';
    }
}

function getErrorTypeIcon(type) {
    switch(type) {
        case 'Human Error': return 'user-x';
        case 'Technical Error': return 'tool';
        default: return 'alert-triangle';
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // All fields are optional, so no validation needed
            // You can add validation here if required
        });
    }
});

// Initial update
updateSummary();
</script>
@endpush