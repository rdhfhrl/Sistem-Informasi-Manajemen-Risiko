@extends('layouts.master')

@section('title', 'Edit Analisis Risiko - SIMR')

@section('page-title', 'Edit Analisis Risiko')

@section('page-action')
<a href="{{ route('risk-analyses.index', $risk->risk_id) }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="edit-2" class="w-5 h-5 mr-2"></i>
                    Edit Analisis Risiko
                </h2>
                @if($analysis->isLatestAnalysis())
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                    Analisis Terbaru
                </span>
                @endif
            </div>
            <div class="p-5">
                <!-- Risk Info -->
                <div class="mb-8 bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-medium text-blue-800 mb-2">Informasi Risiko</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <div class="text-blue-600 text-sm mb-1">Kode Risiko</div>
                            <div class="font-medium">{{ $risk->risk_code }}</div>
                        </div>
                        <div>
                            <div class="text-blue-600 text-sm mb-1">Deskripsi Risiko</div>
                            <div class="font-medium">{{ $risk->risk_description }}</div>
                        </div>
                        <div>
                            <div class="text-blue-600 text-sm mb-1">Tanggal Analisis</div>
                            <div class="font-medium">{{ $analysis->analysis_date->format('d F Y') }}</div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('risk-analyses.update', [$risk->risk_id, $analysis->risk_analysis_id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div>
                            <!-- Analysis Date -->
                            <div class="mb-6">
                                <label for="analysis_date" class="form-label">Tanggal Analisis <span class="text-red-500">*</span></label>
                                <input type="date" 
                                       id="analysis_date" 
                                       name="analysis_date" 
                                       class="form-control w-full @error('analysis_date') border-red-500 @enderror"
                                       value="{{ old('analysis_date', $analysis->analysis_date->format('Y-m-d')) }}"
                                       required>
                                @error('analysis_date')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Likelihood Level -->
                            <div class="mb-6">
                                <label class="form-label">Tingkat Kemungkinan Terjadi (Likelihood) <span class="text-red-500">*</span></label>
                                <div class="likelihood-scale mt-2">
                                    @for($i = 5; $i >= 1; $i--)
                                        <div class="flex items-center mb-3">
                                            <input type="radio" 
                                                   id="likelihood_{{ $i }}" 
                                                   name="likelihood_level" 
                                                   value="{{ $i }}"
                                                   class="form-radio likelihood-radio" 
                                                   {{ old('likelihood_level', $analysis->likelihood_level) == $i ? 'checked' : '' }}
                                                   required>
                                            <label for="likelihood_{{ $i }}" class="ml-3 flex-1 cursor-pointer">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <span class="font-medium">Level {{ $i }}: </span>
                                                        @php
                                                            $likelihoodText = [
                                                                1 => 'Sangat Rendah - Jarang sekali terjadi',
                                                                2 => 'Rendah - Jarang terjadi',
                                                                3 => 'Sedang - Mungkin terjadi',
                                                                4 => 'Tinggi - Sering terjadi',
                                                                5 => 'Sangat Tinggi - Hampir pasti terjadi'
                                                            ][$i];
                                                        @endphp
                                                        <span class="text-gray-600">{{ $likelihoodText }}</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        @for($j = 1; $j <= $i; $j++)
                                                            <div class="w-6 h-2 bg-blue-500 mx-0.5 rounded"></div>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                                @error('likelihood_level')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div>
                            <!-- Impact Level -->
                            <div class="mb-6">
                                <label class="form-label">Tingkat Dampak (Impact) <span class="text-red-500">*</span></label>
                                <div class="impact-scale mt-2">
                                    @for($i = 5; $i >= 1; $i--)
                                        <div class="flex items-center mb-3">
                                            <input type="radio" 
                                                   id="impact_{{ $i }}" 
                                                   name="impact_level" 
                                                   value="{{ $i }}"
                                                   class="form-radio impact-radio" 
                                                   {{ old('impact_level', $analysis->impact_level) == $i ? 'checked' : '' }}
                                                   required>
                                            <label for="impact_{{ $i }}" class="ml-3 flex-1 cursor-pointer">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <span class="font-medium">Level {{ $i }}: </span>
                                                        @php
                                                            $impactText = [
                                                                1 => 'Sangat Kecil - Dampak minimal, mudah diatasi',
                                                                2 => 'Kecil - Dampak terbatas, dapat dikendalikan',
                                                                3 => 'Sedang - Dampak signifikan, perlu perhatian',
                                                                4 => 'Besar - Dampak serius, mengganggu operasional',
                                                                5 => 'Sangat Besar - Dampak kritis, mengancam keberlangsungan'
                                                            ][$i];
                                                        @endphp
                                                        <span class="text-gray-600">{{ $impactText }}</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        @for($j = 1; $j <= $i; $j++)
                                                            <div class="w-6 h-2 bg-red-500 mx-0.5 rounded"></div>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                                @error('impact_level')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Analysis Notes -->
                            <div class="mb-6">
                                <label for="analysis_notes" class="form-label">Catatan Analisis</label>
                                <textarea id="analysis_notes" 
                                          name="analysis_notes" 
                                          class="form-control w-full @error('analysis_notes') border-red-500 @enderror"
                                          rows="4"
                                          placeholder="Tambahkan catatan atau penjelasan terkait analisis ini...">{{ old('analysis_notes') }}</textarea>
                                @error('analysis_notes')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Current Analysis Summary -->
                    <div class="mt-8 mb-6 bg-gray-50 p-6 rounded-lg">
                        <h4 class="font-medium text-gray-700 mb-4">Ringkasan Analisis Saat Ini</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="text-gray-600 text-sm mb-2">Skor Risiko</div>
                                <div class="text-4xl font-bold 
                                    @if($analysis->risk_level == 'sangat_tinggi') text-red-600
                                    @elseif($analysis->risk_level == 'tinggi') text-orange-600
                                    @elseif($analysis->risk_level == 'sedang') text-yellow-600
                                    @elseif($analysis->risk_level == 'rendah') text-blue-600
                                    @else text-green-600
                                    @endif">
                                    {{ $analysis->risk_score }}
                                </div>
                                <div class="text-sm text-gray-500 mt-1">
                                    ({{ $analysis->likelihood_level }} × {{ $analysis->impact_level }})
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-600 text-sm mb-2">Level Risiko</div>
                                <div class="text-2xl font-bold">
                                    <span class="px-4 py-2 rounded-full 
                                        @if($analysis->risk_level == 'sangat_rendah') bg-green-100 text-green-800
                                        @elseif($analysis->risk_level == 'rendah') bg-blue-100 text-blue-800
                                        @elseif($analysis->risk_level == 'sedang') bg-yellow-100 text-yellow-800
                                        @elseif($analysis->risk_level == 'tinggi') bg-orange-100 text-orange-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        @switch($analysis->risk_level)
                                            @case('sangat_rendah') Sangat Rendah @break
                                            @case('rendah') Rendah @break
                                            @case('sedang') Sedang @break
                                            @case('tinggi') Tinggi @break
                                            @case('sangat_tinggi') Sangat Tinggi @break
                                        @endswitch
                                    </span>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-600 text-sm mb-2">Posisi Matriks</div>
                                <div class="text-lg font-medium text-gray-700">
                                    Posisi: ({{ $analysis->likelihood_level }}, {{ $analysis->impact_level }})
                                </div>
                                <div class="text-sm text-gray-500 mt-1">
                                    Likelihood: {{ $analysis->likelihood_level }}/5, Impact: {{ $analysis->impact_level }}/5
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Updated Risk Preview -->
                    <div class="mt-8 mb-6 bg-blue-50 p-6 rounded-lg">
                        <h4 class="font-medium text-blue-800 mb-4">Pratinjau Hasil Update</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="text-blue-600 text-sm mb-2">Skor Risiko Baru</div>
                                <div id="updatedRiskScorePreview" class="text-4xl font-bold text-gray-400">-</div>
                                <div id="updatedRiskCalculation" class="text-sm text-gray-500 mt-1">(Likelihood × Impact)</div>
                            </div>
                            <div class="text-center">
                                <div class="text-blue-600 text-sm mb-2">Level Risiko Baru</div>
                                <div id="updatedRiskLevelPreview" class="text-2xl font-bold">
                                    <span class="px-4 py-2 rounded-full bg-gray-200 text-gray-700">-</span>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-blue-600 text-sm mb-2">Perubahan</div>
                                <div id="changeIndicator" class="text-lg font-medium text-gray-700">-</div>
                                <div class="text-sm text-gray-500 mt-1" id="changeDetails">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Trend Analysis -->
                    @if($analysis->trend !== 'new')
                    <div class="mt-6 mb-6 bg-yellow-50 p-4 rounded-lg">
                        <h4 class="font-medium text-yellow-800 mb-2">Analisis Trend</h4>
                        <div class="flex items-center">
                            <i data-feather="{{ $analysis->trend_icon }}" 
                               class="w-5 h-5 mr-2 text-{{ $analysis->trend_color }}-600"></i>
                            <div>
                                @if($analysis->trend === 'increase')
                                    <span class="text-yellow-700">Skor risiko meningkat dari analisis sebelumnya</span>
                                @elseif($analysis->trend === 'decrease')
                                    <span class="text-green-700">Skor risiko menurun dari analisis sebelumnya</span>
                                @else
                                    <span class="text-blue-700">Skor risiko stabil dari analisis sebelumnya</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Form Actions -->
                    <div class="flex justify-between items-center pt-5 border-t">
                        <div>
                            <form action="{{ route('risk-analyses.destroy', [$risk->risk_id, $analysis->risk_analysis_id]) }}" 
                                  method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus analisis ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus
                                </button>
                            </form>
                        </div>
                        <div class="flex">
                            <a href="{{ route('risk-analyses.index', $risk->risk_id) }}" class="btn btn-outline-secondary w-24 mr-3">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary w-24">
                                <i data-feather="save" class="w-4 h-4 mr-2"></i> Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    const currentScore = {{ $analysis->risk_score }};
    const currentLikelihood = {{ $analysis->likelihood_level }};
    const currentImpact = {{ $analysis->impact_level }};
    
    const likelihoodRadios = document.querySelectorAll('.likelihood-radio');
    const impactRadios = document.querySelectorAll('.impact-radio');
    const updatedRiskScorePreview = document.getElementById('updatedRiskScorePreview');
    const updatedRiskLevelPreview = document.getElementById('updatedRiskLevelPreview');
    const updatedRiskCalculation = document.getElementById('updatedRiskCalculation');
    const changeIndicator = document.getElementById('changeIndicator');
    const changeDetails = document.getElementById('changeDetails');
    
    function updateRiskPreview() {
        const likelihood = document.querySelector('input[name="likelihood_level"]:checked');
        const impact = document.querySelector('input[name="impact_level"]:checked');
        
        if (likelihood && impact) {
            const likelihoodValue = parseInt(likelihood.value);
            const impactValue = parseInt(impact.value);
            const riskScore = likelihoodValue * impactValue;
            
            // Update risk score preview
            updatedRiskScorePreview.textContent = riskScore;
            updatedRiskCalculation.textContent = `(${likelihoodValue} × ${impactValue})`;
            
            // Update risk level
            let riskLevel, riskLevelClass, riskLevelText;
            
            if (riskScore >= 20) {
                riskLevel = 'sangat_tinggi';
                riskLevelClass = 'bg-red-100 text-red-800';
                riskLevelText = 'Sangat Tinggi';
            } else if (riskScore >= 15) {
                riskLevel = 'tinggi';
                riskLevelClass = 'bg-orange-100 text-orange-800';
                riskLevelText = 'Tinggi';
            } else if (riskScore >= 10) {
                riskLevel = 'sedang';
                riskLevelClass = 'bg-yellow-100 text-yellow-800';
                riskLevelText = 'Sedang';
            } else if (riskScore >= 5) {
                riskLevel = 'rendah';
                riskLevelClass = 'bg-blue-100 text-blue-800';
                riskLevelText = 'Rendah';
            } else {
                riskLevel = 'sangat_rendah';
                riskLevelClass = 'bg-green-100 text-green-800';
                riskLevelText = 'Sangat Rendah';
            }
            
            updatedRiskLevelPreview.innerHTML = `<span class="px-4 py-2 rounded-full ${riskLevelClass}">${riskLevelText}</span>`;
            
            // Update score color
            updatedRiskScorePreview.className = 'text-4xl font-bold';
            if (riskScore >= 20) updatedRiskScorePreview.classList.add('text-red-600');
            else if (riskScore >= 15) updatedRiskScorePreview.classList.add('text-orange-600');
            else if (riskScore >= 10) updatedRiskScorePreview.classList.add('text-yellow-600');
            else if (riskScore >= 5) updatedRiskScorePreview.classList.add('text-blue-600');
            else updatedRiskScorePreview.classList.add('text-green-600');
            
            // Calculate change
            const scoreChange = riskScore - currentScore;
            const likelihoodChange = likelihoodValue - currentLikelihood;
            const impactChange = impactValue - currentImpact;
            
            // Update change indicator
            if (scoreChange > 0) {
                changeIndicator.innerHTML = '<span class="text-red-600">Meningkat ' + scoreChange + ' poin</span>';
                changeIndicator.className = 'text-lg font-medium text-red-600';
                changeDetails.textContent = `Likelihood ${likelihoodChange >= 0 ? '+' : ''}${likelihoodChange}, Impact ${impactChange >= 0 ? '+' : ''}${impactChange}`;
            } else if (scoreChange < 0) {
                changeIndicator.innerHTML = '<span class="text-green-600">Menurun ' + Math.abs(scoreChange) + ' poin</span>';
                changeIndicator.className = 'text-lg font-medium text-green-600';
                changeDetails.textContent = `Likelihood ${likelihoodChange >= 0 ? '+' : ''}${likelihoodChange}, Impact ${impactChange >= 0 ? '+' : ''}${impactChange}`;
            } else {
                changeIndicator.innerHTML = '<span class="text-blue-600">Tidak berubah</span>';
                changeIndicator.className = 'text-lg font-medium text-blue-600';
                changeDetails.textContent = `Likelihood dan Impact tetap sama`;
            }
        } else {
            updatedRiskScorePreview.textContent = '-';
            updatedRiskScorePreview.className = 'text-4xl font-bold text-gray-400';
            updatedRiskCalculation.textContent = '(Likelihood × Impact)';
            updatedRiskLevelPreview.innerHTML = '<span class="px-4 py-2 rounded-full bg-gray-200 text-gray-700">-</span>';
            changeIndicator.textContent = '-';
            changeDetails.textContent = '-';
        }
    }
    
    // Add event listeners to all radio buttons
    likelihoodRadios.forEach(radio => {
        radio.addEventListener('change', updateRiskPreview);
    });
    
    impactRadios.forEach(radio => {
        radio.addEventListener('change', updateRiskPreview);
    });
    
    // Initialize preview
    updateRiskPreview();
    
    // Auto-hide alerts
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