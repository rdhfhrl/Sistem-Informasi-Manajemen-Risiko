@extends('layouts.master')

@section('title', 'Tambah Pemantauan Risiko - SIMR')

@section('page-title', 'Tambah Pemantauan Risiko')

@section('breadcrumb')
@parent
<li class="breadcrumb-item"><a href="{{ route('risk-monitorings.index') }}">Pemantauan</a></li>
<li class="breadcrumb-item"><a href="{{ route('risk-monitorings.by-risk', $risk->risk_id) }}">{{ $risk->risk_code }}</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endsection

@section('page-action')
<a href="{{ route('risk-monitorings.by-risk', $risk->risk_id) }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Header Info -->
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
                        <div class="font-medium">{{ Str::limit($risk->risk_description, 80) }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm mb-1">Skor Risiko Terakhir</div>
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
                @if($latestAnalysis)
                <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i data-feather="info" class="w-4 h-4 mr-2 text-blue-500"></i>
                        <span class="font-medium">Analisis Terakhir:</span>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <div class="text-gray-600 text-xs">Tanggal</div>
                            <div class="text-sm font-medium">{{ $latestAnalysis->analysis_date->format('d M Y') }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600 text-xs">Skor Risiko</div>
                            <div class="text-sm font-medium">{{ $latestAnalysis->risk_score ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600 text-xs">Skor Dampak</div>
                            <div class="text-sm font-medium">{{ $latestAnalysis->impact_score ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600 text-xs">Skor Kemungkinan</div>
                            <div class="text-sm font-medium">{{ $latestAnalysis->probability_score ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Monitoring Form -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="file-plus" class="w-5 h-5 mr-2 text-green-500"></i>
                    Form Pemantauan Risiko
                </h2>
            </div>
            <form action="{{ route('risk-monitorings.store', $risk->risk_id) }}" method="POST">
                @csrf
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
                                       value="{{ old('monitoring_date', date('Y-m-d')) }}"
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
                                           value="{{ old('current_risk_score', $risk->risk_score) }}"
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
                            <div id="risk-level-preview" class="hidden">
                                <div class="form-label">Level Risiko</div>
                                <div id="risk-level-display" class="px-4 py-3 rounded-lg border text-center font-medium"></div>
                            </div>

                            <!-- Monitored By -->
                            <div>
                                <label for="monitored_by" class="form-label">Nama Pemantau <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       id="monitored_by" 
                                       name="monitored_by" 
                                       class="form-control w-full @error('monitored_by') border-red-500 @enderror" 
                                       value="{{ old('monitored_by', auth()->user()->name ?? '') }}"
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
                                                   {{ old('effectiveness_rating') == $i ? 'checked' : '' }}>
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
                                       value="{{ old('next_monitoring_date') }}"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}">
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
                                  rows="4"
                                  placeholder="Hasil observasi dan temuan dari pemantauan">{{ old('monitoring_result') }}</textarea>
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
                                  rows="4"
                                  placeholder="Ringkasan laporan pemantauan">{{ old('monitoring_report') }}</textarea>
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
                                  rows="3"
                                  placeholder="Rekomendasi tindak lanjut berdasarkan hasil pemantauan">{{ old('recommendations') }}</textarea>
                        @error('recommendations')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('risk-monitorings.by-risk', $risk->risk_id) }}" 
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

        <!-- Risk Score Guide -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="help-circle" class="w-5 h-5 mr-2 text-purple-500"></i>
                    Panduan Penilaian Skor Risiko
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    @php
                        $riskLevels = [
                            ['min' => 20, 'max' => 25, 'level' => 'sangat_tinggi', 'label' => 'Sangat Tinggi', 'color' => 'red', 'desc' => 'Risiko kritis, memerlukan tindakan segera'],
                            ['min' => 15, 'max' => 19.99, 'level' => 'tinggi', 'label' => 'Tinggi', 'color' => 'orange', 'desc' => 'Risiko signifikan, perlu perhatian khusus'],
                            ['min' => 10, 'max' => 14.99, 'level' => 'sedang', 'label' => 'Sedang', 'color' => 'yellow', 'desc' => 'Risiko moderat, perlu pemantauan rutin'],
                            ['min' => 5, 'max' => 9.99, 'level' => 'rendah', 'label' => 'Rendah', 'color' => 'blue', 'desc' => 'Risiko rendah, dapat dikelola rutin'],
                            ['min' => 1, 'max' => 4.99, 'level' => 'sangat_rendah', 'label' => 'Sangat Rendah', 'color' => 'green', 'desc' => 'Risiko minimal, dapat diterima'],
                        ];
                    @endphp
                    
                    @foreach($riskLevels as $level)
                        <div class="text-center p-4 rounded-lg border 
                            @if($level['color'] == 'red') border-red-200 bg-red-50
                            @elseif($level['color'] == 'orange') border-orange-200 bg-orange-50
                            @elseif($level['color'] == 'yellow') border-yellow-200 bg-yellow-50
                            @elseif($level['color'] == 'blue') border-blue-200 bg-blue-50
                            @else border-green-200 bg-green-50
                            @endif">
                            <div class="text-2xl font-bold mb-2 
                                @if($level['color'] == 'red') text-red-600
                                @elseif($level['color'] == 'orange') text-orange-600
                                @elseif($level['color'] == 'yellow') text-yellow-600
                                @elseif($level['color'] == 'blue') text-blue-600
                                @else text-green-600
                                @endif">
                                {{ $level['min'] }} - {{ $level['max'] }}
                            </div>
                            <div class="mb-2">
                                <span class="px-3 py-1 rounded-full text-xs font-medium 
                                    @if($level['color'] == 'red') bg-red-100 text-red-800
                                    @elseif($level['color'] == 'orange') bg-orange-100 text-orange-800
                                    @elseif($level['color'] == 'yellow') bg-yellow-100 text-yellow-800
                                    @elseif($level['color'] == 'blue') bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ $level['label'] }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-600">
                                {{ $level['desc'] }}
                            </div>
                        </div>
                    @endforeach
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
    const previewDiv = document.getElementById('risk-level-preview');
    const displayDiv = document.getElementById('risk-level-display');
    
    function updateRiskLevelPreview() {
        const score = parseFloat(scoreInput.value);
        if (!isNaN(score) && score >= 1 && score <= 25) {
            const riskLevel = calculateRiskLevel(score);
            
            // Update colors based on risk level
            let bgColor = '';
            let textColor = '';
            let borderColor = '';
            
            switch(riskLevel.color) {
                case 'red':
                    bgColor = 'bg-red-50';
                    textColor = 'text-red-700';
                    borderColor = 'border-red-200';
                    break;
                case 'orange':
                    bgColor = 'bg-orange-50';
                    textColor = 'text-orange-700';
                    borderColor = 'border-orange-200';
                    break;
                case 'yellow':
                    bgColor = 'bg-yellow-50';
                    textColor = 'text-yellow-700';
                    borderColor = 'border-yellow-200';
                    break;
                case 'blue':
                    bgColor = 'bg-blue-50';
                    textColor = 'text-blue-700';
                    borderColor = 'border-blue-200';
                    break;
                case 'green':
                    bgColor = 'bg-green-50';
                    textColor = 'text-green-700';
                    borderColor = 'border-green-200';
                    break;
            }
            
            displayDiv.className = `px-4 py-3 rounded-lg border text-center font-medium ${bgColor} ${textColor} ${borderColor}`;
            displayDiv.innerHTML = `
                <div class="text-lg font-bold">${riskLevel.label}</div>
                <div class="text-sm mt-1">Skor: ${score.toFixed(2)}</div>
            `;
            
            previewDiv.classList.remove('hidden');
        } else {
            previewDiv.classList.add('hidden');
        }
    }
    
    // Add event listeners
    scoreInput.addEventListener('input', updateRiskLevelPreview);
    scoreInput.addEventListener('change', updateRiskLevelPreview);
    
    // Initialize preview if there's a value
    if (scoreInput.value) {
        updateRiskLevelPreview();
    }
    
    // Set min date for next monitoring date
    const monitoringDateInput = document.getElementById('monitoring_date');
    const nextMonitoringDateInput = document.getElementById('next_monitoring_date');
    
    monitoringDateInput.addEventListener('change', function() {
        const minDate = new Date(this.value);
        minDate.setDate(minDate.getDate() + 1);
        nextMonitoringDateInput.min = minDate.toISOString().split('T')[0];
    });
    
    // Auto-calculate next monitoring date (30 days later)
    monitoringDateInput.addEventListener('change', function() {
        if (!nextMonitoringDateInput.value && this.value) {
            const nextDate = new Date(this.value);
            nextDate.setDate(nextDate.getDate() + 30);
            nextMonitoringDateInput.value = nextDate.toISOString().split('T')[0];
        }
    });
});
</script>
@endpush