@extends('layouts.master')

@section('title', 'Tambah Indikator Risiko - SIMR')

@section('page-title', 'Tambah Indikator Risiko')

@section('breadcrumb')
@parent
<li class="breadcrumb-item"><a href="{{ route('risks.index') }}">Risiko</a></li>
<li class="breadcrumb-item"><a href="{{ route('risk-indicators.index', $risk->risk_id) }}">Indikator</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endsection

@section('page-action')
<a href="{{ route('risk-indicators.index', $risk->risk_id) }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
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
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Kode Risiko</div>
                        <div class="font-medium">{{ $risk->risk_code }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Deskripsi Risiko</div>
                        <div class="font-medium">{{ Str::limit($risk->risk_description, 80) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicator Form -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="file-plus" class="w-5 h-5 mr-2 text-green-500"></i>
                    Form Tambah Indikator Risiko
                </h2>
            </div>
            <form action="{{ route('risk-indicators.store', $risk->risk_id) }}" method="POST">
                @csrf
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Indicator Type -->
                            <div>
                                <label for="indicator_type" class="form-label">Jenis Indikator <span class="text-red-500">*</span></label>
                                <select id="indicator_type" 
                                        name="indicator_type" 
                                        class="form-select w-full @error('indicator_type') border-red-500 @enderror" 
                                        required>
                                    <option value="">Pilih Jenis Indikator</option>
                                    @foreach($indicatorTypes as $value => $label)
                                        <option value="{{ $value }}" {{ old('indicator_type') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('indicator_type')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                                <div class="text-gray-500 text-xs mt-1">
                                    Pilih jenis indikator sesuai dengan fokus pengukuran
                                </div>
                            </div>

                            <!-- Indicator Name -->
                            <div>
                                <label for="indicator_name" class="form-label">Nama Indikator <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       id="indicator_name" 
                                       name="indicator_name" 
                                       class="form-control w-full @error('indicator_name') border-red-500 @enderror" 
                                       value="{{ old('indicator_name') }}"
                                       placeholder="Contoh: Tingkat Error Sistem"
                                       required>
                                @error('indicator_name')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Unit -->
                            <div>
                                <label for="unit" class="form-label">Satuan</label>
                                <input type="text" 
                                       id="unit" 
                                       name="unit" 
                                       class="form-control w-full @error('unit') border-red-500 @enderror" 
                                       value="{{ old('unit') }}"
                                       placeholder="Contoh: %, jumlah, jam, dll">
                                @error('unit')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                                <div class="text-gray-500 text-xs mt-1">
                                    Satuan pengukuran indikator
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Threshold -->
                            <div>
                                <label for="threshold" class="form-label">Ambang Batas <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" 
                                           id="threshold" 
                                           name="threshold" 
                                           class="form-control w-full @error('threshold') border-red-500 @enderror" 
                                           value="{{ old('threshold') }}"
                                           step="0.01"
                                           placeholder="0.00"
                                           required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-gray-500" id="threshold-unit"></span>
                                    </div>
                                </div>
                                @error('threshold')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                                <div class="text-gray-500 text-xs mt-1">
                                    Nilai ambang batas yang menjadi peringatan
                                </div>
                            </div>

                            <!-- Current Value -->
                            <div>
                                <label for="current_value" class="form-label">Nilai Saat Ini (Opsional)</label>
                                <div class="relative">
                                    <input type="number" 
                                           id="current_value" 
                                           name="current_value" 
                                           class="form-control w-full @error('current_value') border-red-500 @enderror" 
                                           value="{{ old('current_value') }}"
                                           step="0.01"
                                           placeholder="Kosongkan jika belum diukur">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-gray-500" id="current-value-unit"></span>
                                    </div>
                                </div>
                                @error('current_value')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Indicator Description -->
                    <div class="mt-6">
                        <label for="indicator_description" class="form-label">Deskripsi Indikator</label>
                        <textarea id="indicator_description" 
                                  name="indicator_description" 
                                  class="form-control w-full @error('indicator_description') border-red-500 @enderror" 
                                  rows="4"
                                  placeholder="Deskripsi detail tentang indikator, cara pengukuran, dan interpretasi hasil">{{ old('indicator_description') }}</textarea>
                        @error('indicator_description')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('risk-indicators.index', $risk->risk_id) }}" 
                           class="btn btn-outline-secondary w-32 mr-3">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary w-32">
                            <i data-feather="save" class="w-4 h-4 mr-2"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Indicator Type Guide -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="help-circle" class="w-5 h-5 mr-2 text-purple-500"></i>
                    Panduan Jenis Indikator
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i data-feather="root" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <h3 class="font-medium text-blue-700">Akar Masalah</h3>
                        </div>
                        <p class="text-sm text-gray-600">
                            Indikator yang mengukur penyebab mendasar dari risiko. Contoh: Budaya organisasi, struktur tim, kebijakan.
                        </p>
                    </div>
                    
                    <div class="bg-orange-50 p-4 rounded-lg border border-orange-100">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center mr-3">
                                <i data-feather="search" class="w-5 h-5 text-orange-600"></i>
                            </div>
                            <h3 class="font-medium text-orange-700">Penyebab</h3>
                        </div>
                        <p class="text-sm text-gray-600">
                            Indikator yang mengukur faktor-faktor penyebab langsung risiko. Contoh: Volume transaksi, tingkat kompleksitas.
                        </p>
                    </div>
                    
                    <div class="bg-red-50 p-4 rounded-lg border border-red-100">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                <i data-feather="target" class="w-5 h-5 text-red-600"></i>
                            </div>
                            <h3 class="font-medium text-red-700">Dampak</h3>
                        </div>
                        <p class="text-sm text-gray-600">
                            Indikator yang mengukur konsekuensi atau dampak dari risiko. Contoh: Kerugian finansial, penuruan produktivitas.
                        </p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                <i data-feather="more-horizontal" class="w-5 h-5 text-gray-600"></i>
                            </div>
                            <h3 class="font-medium text-gray-700">Lainnya</h3>
                        </div>
                        <p class="text-sm text-gray-600">
                            Indikator lain yang relevan tetapi tidak termasuk dalam kategori di atas. Contoh: Indikator kontrol, indikator kinerja.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Sync unit displays
    const unitInput = document.getElementById('unit');
    const thresholdUnit = document.getElementById('threshold-unit');
    const currentValueUnit = document.getElementById('current-value-unit');
    
    function updateUnitDisplays() {
        const unit = unitInput.value ? unitInput.value : '';
        thresholdUnit.textContent = unit;
        currentValueUnit.textContent = unit;
    }
    
    unitInput.addEventListener('input', updateUnitDisplays);
    
    // Initialize unit displays
    updateUnitDisplays();
});
</script>
@endpush