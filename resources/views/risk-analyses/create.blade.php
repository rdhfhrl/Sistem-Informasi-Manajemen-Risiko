@extends('layouts.master')

@section('title', 'Buat Analisis Risiko Baru - SIMR')

@section('page-title', 'Buat Analisis Risiko Baru')

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
                    <i data-feather="edit" class="w-5 h-5 mr-2"></i>
                    Form Analisis Risiko Baru
                </h2>
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
                        @if($latestAnalysis)
                        <div>
                            <div class="text-blue-600 text-sm mb-1">Analisis Terakhir</div>
                            <div class="font-medium">{{ \Carbon\Carbon::parse($latestAnalysis->analysis_date)->format('d F Y') }}</div>
                            <div class="text-sm text-gray-600">
                                Skor: {{ $latestAnalysis->risk_score }} ({{ $latestAnalysis->likelihood_level }} × {{ $latestAnalysis->impact_level }})
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <form action="{{ route('risk-analyses.store', $risk->risk_id) }}" method="POST">
                    @csrf
                    
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
                                       value="{{ old('analysis_date', date('Y-m-d')) }}"
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
                                                   {{ old('likelihood_level') == $i ? 'checked' : '' }}
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
                                                   {{ old('impact_level') == $i ? 'checked' : '' }}
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

                    <!-- Risk Score Preview -->
                    <div class="mt-8 mb-6 bg-gray-50 p-6 rounded-lg">
                        <h4 class="font-medium text-gray-700 mb-4">Pratinjau Hasil Analisis</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="text-gray-600 text-sm mb-2">Skor Risiko</div>
                                <div id="riskScorePreview" class="text-4xl font-bold text-gray-400">-</div>
                                <div id="riskCalculation" class="text-sm text-gray-500 mt-1">(Likelihood × Impact)</div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-600 text-sm mb-2">Level Risiko</div>
                                <div id="riskLevelPreview" class="text-2xl font-bold">
                                    <span class="px-4 py-2 rounded-full bg-gray-200 text-gray-700">-</span>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-600 text-sm mb-2">Posisi Matriks</div>
                                <div id="matrixPosition" class="text-lg font-medium text-gray-700">-</div>
                                <div class="text-sm text-gray-500 mt-1">Likelihood: -/5, Impact: -/5</div>
                            </div>
                        </div>
                    </div>

                    <!-- Risk Matrix Guide -->
                    <div class="mt-6 mb-8">
                        <h4 class="font-medium text-gray-700 mb-4">Panduan Matriks Risiko</h4>
                        <div class="bg-white border rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="p-2 border">Skor</th>
                                        <th class="p-2 border">Level Risiko</th>
                                        <th class="p-2 border">Warna</th>
                                        <th class="p-2 border">Tindakan yang Disarankan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="p-2 border text-center">20-25</td>
                                        <td class="p-2 border">
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Sangat Tinggi</span>
                                        </td>
                                        <td class="p-2 border">
                                            <div class="w-6 h-6 bg-red-500 rounded mx-auto"></div>
                                        </td>
                                        <td class="p-2 border text-sm">Dihentikan segera, tindakan ekstrim diperlukan</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 border text-center">15-19</td>
                                        <td class="p-2 border">
                                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs">Tinggi</span>
                                        </td>
                                        <td class="p-2 border">
                                            <div class="w-6 h-6 bg-orange-500 rounded mx-auto"></div>
                                        </td>
                                        <td class="p-2 border text-sm">Diperlukan tindakan segera, monitoring intensif</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 border text-center">10-14</td>
                                        <td class="p-2 border">
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Sedang</span>
                                        </td>
                                        <td class="p-2 border">
                                            <div class="w-6 h-6 bg-yellow-500 rounded mx-auto"></div>
                                        </td>
                                        <td class="p-2 border text-sm">Perlu pengendalian, monitoring rutin</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 border text-center">5-9</td>
                                        <td class="p-2 border">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Rendah</span>
                                        </td>
                                        <td class="p-2 border">
                                            <div class="w-6 h-6 bg-blue-500 rounded mx-auto"></div>
                                        </td>
                                        <td class="p-2 border text-sm">Dapat ditoleransi, monitoring berkala</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 border text-center">1-4</td>
                                        <td class="p-2 border">
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Sangat Rendah</span>
                                        </td>
                                        <td class="p-2 border">
                                            <div class="w-6 h-6 bg-green-500 rounded mx-auto"></div>
                                        </td>
                                        <td class="p-2 border text-sm">Dapat diterima, monitoring minimal</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end pt-5 border-t">
                        <a href="{{ route('risk-analyses.index', $risk->risk_id) }}" class="btn btn-outline-secondary w-24 mr-3">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary w-24">
                            <i data-feather="save" class="w-4 h-4 mr-2"></i> Simpan
                        </button>
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
    
    const likelihoodRadios = document.querySelectorAll('.likelihood-radio');
    const impactRadios = document.querySelectorAll('.impact-radio');
    const riskScorePreview = document.getElementById('riskScorePreview');
    const riskLevelPreview = document.getElementById('riskLevelPreview');
    const riskCalculation = document.getElementById('riskCalculation');
    const matrixPosition = document.getElementById('matrixPosition');
    
    function updateRiskPreview() {
        const likelihood = document.querySelector('input[name="likelihood_level"]:checked');
        const impact = document.querySelector('input[name="impact_level"]:checked');
        
        if (likelihood && impact) {
            const likelihoodValue = parseInt(likelihood.value);
            const impactValue = parseInt(impact.value);
            const riskScore = likelihoodValue * impactValue;
            
            // Update risk score
            riskScorePreview.textContent = riskScore;
            riskCalculation.textContent = `(${likelihoodValue} × ${impactValue})`;
            
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
            
            riskLevelPreview.innerHTML = `<span class="px-4 py-2 rounded-full ${riskLevelClass}">${riskLevelText}</span>`;
            
            // Update matrix position
            matrixPosition.textContent = `Posisi: (${likelihoodValue}, ${impactValue})`;
            
            // Update score color
            riskScorePreview.className = 'text-4xl font-bold';
            if (riskScore >= 20) riskScorePreview.classList.add('text-red-600');
            else if (riskScore >= 15) riskScorePreview.classList.add('text-orange-600');
            else if (riskScore >= 10) riskScorePreview.classList.add('text-yellow-600');
            else if (riskScore >= 5) riskScorePreview.classList.add('text-blue-600');
            else riskScorePreview.classList.add('text-green-600');
        } else {
            riskScorePreview.textContent = '-';
            riskScorePreview.className = 'text-4xl font-bold text-gray-400';
            riskCalculation.textContent = '(Likelihood × Impact)';
            riskLevelPreview.innerHTML = '<span class="px-4 py-2 rounded-full bg-gray-200 text-gray-700">-</span>';
            matrixPosition.textContent = '-';
        }
    }
    
    // Add event listeners to all radio buttons
    likelihoodRadios.forEach(radio => {
        radio.addEventListener('change', updateRiskPreview);
    });
    
    impactRadios.forEach(radio => {
        radio.addEventListener('change', updateRiskPreview);
    });
    
    // Initialize preview if there are default values
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