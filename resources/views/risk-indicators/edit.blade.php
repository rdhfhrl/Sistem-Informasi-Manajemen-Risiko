@extends('layouts.master')

@section('title', 'Edit Indikator Risiko - SIMR')

@section('page-title', 'Edit Indikator Risiko')

@section('breadcrumb')
@parent
<li class="breadcrumb-item"><a href="{{ route('risks.index') }}">Risiko</a></li>
<li class="breadcrumb-item"><a href="{{ route('risk-indicators.index', $risk->risk_id) }}">Indikator</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-action')
<a href="{{ route('risk-indicators.show', [$risk->risk_id, $indicator->risk_indicator_id]) }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Header Info -->
        <div class="intro-y box mb-6">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <div class="flex-1">
                    <h2 class="text-lg font-bold">
                        <i data-feather="edit-2" class="w-5 h-5 inline mr-2 text-yellow-500"></i>
                        Edit Indikator Risiko
                    </h2>
                    <div class="text-gray-600 text-sm mt-1">
                        Kode Risiko: <span class="font-medium">{{ $risk->risk_code }}</span> • 
                        Indikator: <span class="font-medium">{{ $indicator->indicator_name }}</span>
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
            
            <!-- Current Values -->
            <div class="p-5 bg-gradient-to-r from-gray-50 to-gray-100">
                <div class="text-center">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm text-gray-600 mb-2">NILAI SAAT INI</div>
                            @if($indicator->current_value)
                                <div class="text-3xl font-bold 
                                    @if($isExceeded) text-red-600
                                    @else text-green-600
                                    @endif">
                                    {{ number_format($indicator->current_value, 2) }}
                                </div>
                                <div class="text-gray-500">{{ $indicator->unit }}</div>
                            @else
                                <div class="text-2xl font-bold text-gray-400">-</div>
                                <div class="text-gray-400">Belum diukur</div>
                            @endif
                        </div>
                        
                        <div>
                            <div class="text-sm text-gray-600 mb-2">AMBANG BATAS</div>
                            <div class="text-3xl font-bold text-blue-600">
                                {{ number_format($indicator->threshold, 2) }}
                            </div>
                            <div class="text-gray-500">{{ $indicator->unit }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="edit" class="w-5 h-5 mr-2 text-green-500"></i>
                    Form Edit Indikator
                </h2>
            </div>
            
            <form action="{{ route('risk-indicators.update', [$risk->risk_id, $indicator->risk_indicator_id]) }}" method="POST">
                @csrf
                @method('PUT')
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
                                        <option value="{{ $value }}" {{ old('indicator_type', $indicator->indicator_type) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('indicator_type')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Indicator Name -->
                            <div>
                                <label for="indicator_name" class="form-label">Nama Indikator <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       id="indicator_name" 
                                       name="indicator_name" 
                                       class="form-control w-full @error('indicator_name') border-red-500 @enderror" 
                                       value="{{ old('indicator_name', $indicator->indicator_name) }}"
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
                                       value="{{ old('unit', $indicator->unit) }}">
                                @error('unit')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
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
                                           value="{{ old('threshold', $indicator->threshold) }}"
                                           step="0.01"
                                           required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-gray-500">{{ $indicator->unit }}</span>
                                    </div>
                                </div>
                                @error('threshold')
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
                                  rows="4">{{ old('indicator_description', $indicator->indicator_description) }}</textarea>
                        @error('indicator_description')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
                        <div>
                            <a href="{{ route('risk-indicators.show', [$risk->risk_id, $indicator->risk_indicator_id]) }}" 
                               class="btn btn-outline-secondary w-32">
                                <i data-feather="x" class="w-4 h-4 mr-2"></i> Batal
                            </a>
                        </div>
                        <div class="flex space-x-3">
                            <button type="reset" class="btn btn-outline-secondary w-32">
                                <i data-feather="refresh-ccw" class="w-4 h-4 mr-2"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary w-32">
                                <i data-feather="save" class="w-4 h-4 mr-2"></i> Update
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- System Info -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="info" class="w-5 h-5 mr-2 text-gray-500"></i>
                    Informasi Sistem
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <div class="text-gray-600 text-sm mb-1">ID Indikator</div>
                        <div class="font-mono text-gray-700">{{ $indicator->risk_indicator_id }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Dibuat Pada</div>
                        <div class="font-medium">{{ $indicator->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Diperbarui Pada</div>
                        <div class="font-medium">{{ $indicator->updated_at->format('d M Y H:i') }}</div>
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