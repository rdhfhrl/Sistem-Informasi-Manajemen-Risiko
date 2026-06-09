@extends('layouts.master')

@section('title', 'Edit Pemantauan Risiko - SIMR')

@section('page-title', 'Edit Pemantauan Risiko')

@section('breadcrumb')
@parent
<li class="breadcrumb-item"><a href="{{ route('risk-monitorings.index') }}">Pemantauan</a></li>
<li class="breadcrumb-item"><a href="{{ route('risk-monitorings.by-risk', $risk->risk_id) }}">{{ $risk->risk_code }}</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-action')
<a href="{{ route('risk-monitorings.show', [$risk->risk_id, $monitoring->risk_monitoring_id]) }}" class="btn btn-outline-secondary shadow-md mr-2">
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
                        Edit Pemantauan Risiko
                    </h2>
                    <div class="text-gray-600 text-sm mt-1">
                        Kode Risiko: <span class="font-medium">{{ $risk->risk_code }}</span> • 
                        ID Pemantauan: <span class="font-medium">{{ $monitoring->risk_monitoring_id }}</span>
                    </div>
                </div>
                <div class="mt-3 sm:mt-0">
                    <span class="px-4 py-2 rounded-full text-sm font-medium 
                        @if($monitoring->current_risk_level == 'sangat_rendah') bg-green-100 text-green-800
                        @elseif($monitoring->current_risk_level == 'rendah') bg-blue-100 text-blue-800
                        @elseif($monitoring->current_risk_level == 'sedang') bg-yellow-100 text-yellow-800
                        @elseif($monitoring->current_risk_level == 'tinggi') bg-orange-100 text-orange-800
                        @else bg-red-100 text-red-800
                        @endif">
                        @switch($monitoring->current_risk_level)
                            @case('sangat_rendah') Sangat Rendah @break
                            @case('rendah') Rendah @break
                            @case('sedang') Sedang @break
                            @case('tinggi') Tinggi @break
                            @case('sangat_tinggi') Sangat Tinggi @break
                        @endswitch
                    </span>
                </div>
            </div>
            
            <!-- Current Score -->
            <div class="p-5 bg-gradient-to-r from-gray-50 to-gray-100">
                <div class="text-center">
                    <div class="text-sm text-gray-600 mb-2">SKOR RISIKO SAAT INI</div>
                    <div class="text-4xl font-bold 
                        @if($monitoring->current_risk_level == 'sangat_tinggi') text-red-600
                        @elseif($monitoring->current_risk_level == 'tinggi') text-orange-600
                        @elseif($monitoring->current_risk_level == 'sedang') text-yellow-600
                        @elseif($monitoring->current_risk_level == 'rendah') text-blue-600
                        @else text-green-600
                        @endif">
                        {{ number_format($monitoring->current_risk_score, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="edit" class="w-5 h-5 mr-2 text-green-500"></i>
                    Form Edit Pemantauan
                </h2>
            </div>
            
            <form action="{{ route('risk-monitorings.update', [$risk->risk_id, $monitoring->risk_monitoring_id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Monitoring Date -->
                            <div>
                                <label for="monitoring_date" class="form-label">Tanggal Pemantauan <span class="text-red-500">*</span></label>
                                <input type="date" 
                                       id="monitoring_date" 
                                       name="monitoring_date" 
                                       class="form-control w-full @error('monitoring_date') border-red-500 @enderror" 
                                       value="{{ old('monitoring_date', $monitoring->monitoring_date->format('Y-m-d')) }}"
                                       required>
                                @error('monitoring_date')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Current Risk Score -->
                            <div>
                                <label for="current_risk_score" class="form-label">Skor Risiko Saat Ini <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" 
                                           id="current_risk_score" 
                                           name="current_risk_score" 
                                           class="form-control w-full @error('current_risk_score') border-red-500 @enderror" 
                                           value="{{ old('current_risk_score', $monitoring->current_risk_score) }}"
                                           min="1" 
                                           max="25" 
                                           step="0.01"
                                           required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-gray-500">/25</span>
                                    </div>
                                </div>
                                <div class="text-gray-500 text-xs mt-1">
                                    Skor risiko berdasarkan hasil pemantauan saat ini (1-25)
                                </div>
                                @error('current_risk_score')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Risk Level Preview -->
                            <div id="risk-level-preview" class="mt-4 p-4 bg-gray-50 rounded-lg">
                                <div class="form-label mb-2">Level Risiko Saat Ini:</div>
                                <div id="risk-level-display" class="inline-block">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium 
                                        @if($monitoring->current_risk_level == 'sangat_rendah') bg-green-100 text-green-800
                                        @elseif($monitoring->current_risk_level == 'rendah') bg-blue-100 text-blue-800
                                        @elseif($monitoring->current_risk_level == 'sedang') bg-yellow-100 text-yellow-800
                                        @elseif($monitoring->current_risk_level == 'tinggi') bg-orange-100 text-orange-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        @switch($monitoring->current_risk_level)
                                            @case('sangat_rendah') Sangat Rendah @break
                                            @case('rendah') Rendah @break
                                            @case('sedang') Sedang @break
                                            @case('tinggi') Tinggi @break
                                            @case('sangat_tinggi') Sangat Tinggi @break
                                        @endswitch
                                    </span>
                                </div>
                                <div class="text-sm text-gray-500 mt-2">
                                    Level akan diperbarui otomatis berdasarkan skor
                                </div>
                            </div>

                            <!-- Monitored By -->
                            <div>
                                <label for="monitored_by" class="form-label">Nama Pemantau <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       id="monitored_by" 
                                       name="monitored_by" 
                                       class="form-control w-full @error('monitored_by') border-red-500 @enderror" 
                                       value="{{ old('monitored_by', $monitoring->monitored_by) }}"
                                       required>
                                @error('monitored_by')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Effectiveness Rating -->
                            <div>
                                <label for="effectiveness_rating" class="form-label">Penilaian Efektivitas</label>
                                <div class="flex items-center space-x-4 mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <label class="cursor-pointer">
                                            <input type="radio" 
                                                   name="effectiveness_rating" 
                                                   value="{{ $i }}" 
                                                   class="hidden peer"
                                                   {{ old('effectiveness_rating', $monitoring->effectiveness_rating) == $i ? 'checked' : '' }}>
                                            <div class="w-10 h-10 flex items-center justify-center rounded-lg border-2 
                                                        peer-checked:border-teal-500 peer-checked:bg-teal-50 
                                                        hover:border-teal-300 transition-colors">
                                                <span class="text-lg">{{ $i }}</span>
                                            </div>
                                            <div class="text-xs text-center mt-1 text-gray-500">
                                                @switch($i)
                                                    @case(1) ST @break
                                                    @case(2) T @break
                                                    @case(3) C @break
                                                    @case(4) E @break
                                                    @case(5) SE @break
                                                @endswitch
                                            </div>
                                        </label>
                                    @endfor
                                </div>
                                <div class="text-gray-500 text-xs">
                                    ST: Sangat Tidak Efektif, T: Tidak Efektif, C: Cukup Efektif, E: Efektif, SE: Sangat Efektif
                                </div>
                                @error('effectiveness_rating')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Next Monitoring Date -->
                            <div>
                                <label for="next_monitoring_date" class="form-label">Jadwal Pemantauan Selanjutnya</label>
                                <input type="date" 
                                       id="next_monitoring_date" 
                                       name="next_monitoring_date" 
                                       class="form-control w-full @error('next_monitoring_date') border-red-500 @enderror" 
                                       value="{{ old('next_monitoring_date', $monitoring->next_monitoring_date ? \Carbon\Carbon::parse($monitoring->next_monitoring_date)->format('Y-m-d') : '') }}">
                                <div class="text-gray-500 text-xs mt-1">
                                    Kosongkan jika tidak perlu menjadwalkan pemantauan berikutnya
                                </div>
                                @error('next_monitoring_date')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Monitoring Result -->
                    <div class="mt-6">
                        <label for="monitoring_result" class="form-label">Hasil Pemantauan</label>
                        <textarea id="monitoring_result" 
                                  name="monitoring_result" 
                                  class="form-control w-full @error('monitoring_result') border-red-500 @enderror" 
                                  rows="4">{{ old('monitoring_result', $monitoring->monitoring_result) }}</textarea>
                        @error('monitoring_result')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Monitoring Report -->
                    <div class="mt-6">
                        <label for="monitoring_report" class="form-label">Laporan Pemantauan</label>
                        <textarea id="monitoring_report" 
                                  name="monitoring_report" 
                                  class="form-control w-full @error('monitoring_report') border-red-500 @enderror" 
                                  rows="4">{{ old('monitoring_report', $monitoring->monitoring_report) }}</textarea>
                        @error('monitoring_report')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Recommendations -->
                    <div class="mt-6">
                        <label for="recommendations" class="form-label">Rekomendasi</label>
                        <textarea id="recommendations" 
                                  name="recommendations" 
                                  class="form-control w-full @error('recommendations') border-red-500 @enderror" 
                                  rows="3">{{ old('recommendations', $monitoring->recommendations) }}</textarea>
                        @error('recommendations')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
                        <div>
                            <a href="{{ route('risk-monitorings.show', [$risk->risk_id, $monitoring->risk_monitoring_id]) }}" 
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
                        <div class="text-gray-600 text-sm mb-1">Dibuat Pada</div>
                        <div class="font-medium">{{ $monitoring->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Diperbarui Pada</div>
                        <div class="font-medium">{{ $monitoring->updated_at->format('d M Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">ID Pemantauan</div>
                        <div class="font-mono text-gray-700">{{ $monitoring->risk_monitoring_id }}</div>
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
    
    // Calculate risk level based on score
    function calculateRiskLevel(score) {
        if (score >= 20) return { level: 'sangat_tinggi', label: 'Sangat Tinggi', color: 'red' };
        if (score >= 15) return { level: 'tinggi', label: 'Tinggi', color: 'orange' };
        if (score >= 10) return { level: 'sedang', label: 'Sedang', color: 'yellow' };
        if (score >= 5) return { level: 'rendah', label: 'Rendah', color: 'blue' };
        return { level: 'sangat_rendah', label: 'Sangat Rendah', color: 'green' };
    }
    
    // Update risk level preview
    const scoreInput = document.getElementById('current_risk_score');
    const displayDiv = document.getElementById('risk-level-display');
    
    function updateRiskLevelPreview() {
        const score = parseFloat(scoreInput.value);
        if (!isNaN(score) && score >= 1 && score <= 25) {
            const riskLevel = calculateRiskLevel(score);
            
            // Update colors based on risk level
            let bgColor = '';
            let textColor = '';
            
            switch(riskLevel.color) {
                case 'red':
                    bgColor = 'bg-red-100';
                    textColor = 'text-red-800';
                    break;
                case 'orange':
                    bgColor = 'bg-orange-100';
                    textColor = 'text-orange-800';
                    break;
                case 'yellow':
                    bgColor = 'bg-yellow-100';
                    textColor = 'text-yellow-800';
                    break;
                case 'blue':
                    bgColor = 'bg-blue-100';
                    textColor = 'text-blue-800';
                    break;
                case 'green':
                    bgColor = 'bg-green-100';
                    textColor = 'text-green-800';
                    break;
            }
            
            displayDiv.innerHTML = `
                <span class="px-3 py-1 rounded-full text-sm font-medium ${bgColor} ${textColor}">
                    ${riskLevel.label}
                </span>
                <div class="text-xs text-gray-500 mt-1">
                    Skor: ${score.toFixed(2)}
                </div>
            `;
        }
    }
    
    // Add event listeners
    scoreInput.addEventListener('input', updateRiskLevelPreview);
    scoreInput.addEventListener('change', updateRiskLevelPreview);
    
    // Set min date for next monitoring date
    const monitoringDateInput = document.getElementById('monitoring_date');
    const nextMonitoringDateInput = document.getElementById('next_monitoring_date');
    
    monitoringDateInput.addEventListener('change', function() {
        const minDate = new Date(this.value);
        minDate.setDate(minDate.getDate() + 1);
        nextMonitoringDateInput.min = minDate.toISOString().split('T')[0];
    });
    
    // Initialize the min date
    const initialDate = new Date(monitoringDateInput.value);
    initialDate.setDate(initialDate.getDate() + 1);
    nextMonitoringDateInput.min = initialDate.toISOString().split('T')[0];
});
</script>
@endpush