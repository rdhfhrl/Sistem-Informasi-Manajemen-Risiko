@extends('layouts.master')

@section('title', 'Buat Evaluasi Risiko Baru - SIMR')

@section('page-title', 'Buat Evaluasi Risiko Baru')

@section('page-action')
<a href="{{ route('risk-evaluations.by-risk', $risk->risk_id) }}" class="btn btn-outline-secondary shadow-md mr-2">
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
                    Form Evaluasi Risiko Baru
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
                        @if($risk->risk_score)
                        <div>
                            <div class="text-blue-600 text-sm mb-1">Skor Risiko Terakhir</div>
                            <div class="font-medium text-lg 
                                @if($risk->risk_level == 'sangat_tinggi') text-red-600
                                @elseif($risk->risk_level == 'tinggi') text-orange-600
                                @elseif($risk->risk_level == 'sedang') text-yellow-600
                                @elseif($risk->risk_level == 'rendah') text-blue-600
                                @else text-green-600
                                @endif">
                                {{ $risk->risk_score }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Latest Analysis Info -->
                @if($latestAnalysis)
                <div class="mb-8 bg-yellow-50 p-4 rounded-lg">
                    <h4 class="font-medium text-yellow-800 mb-2">Informasi Analisis Terakhir</h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <div class="text-yellow-600 text-sm mb-1">Tanggal Analisis</div>
                            <div class="font-medium">{{ $latestAnalysis->analysis_date->format('d F Y') }}</div>
                        </div>
                        <div>
                            <div class="text-yellow-600 text-sm mb-1">Skor Risiko</div>
                            <div class="font-medium">{{ $latestAnalysis->risk_score }}</div>
                        </div>
                        <div>
                            <div class="text-yellow-600 text-sm mb-1">Level Risiko</div>
                            <div class="font-medium">{{ $latestAnalysis->risk_level_indo ?? $latestAnalysis->risk_level }}</div>
                        </div>
                        <div>
                            <div class="text-yellow-600 text-sm mb-1">Likelihood × Impact</div>
                            <div class="font-medium">{{ $latestAnalysis->likelihood_level }} × {{ $latestAnalysis->impact_level }}</div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Latest Evaluation Info -->
                @if($latestEvaluation)
                <div class="mb-8 bg-green-50 p-4 rounded-lg">
                    <h4 class="font-medium text-green-800 mb-2">Evaluasi Terakhir</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <div class="text-green-600 text-sm mb-1">Tanggal Evaluasi</div>
                            <div class="font-medium">{{ $latestEvaluation->evaluation_date->format('d F Y') }}</div>
                        </div>
                        <div>
                            <div class="text-green-600 text-sm mb-1">Prioritas</div>
                            <div class="font-medium">{{ ucwords($latestEvaluation->risk_evaluation_priority) }}</div>
                        </div>
                        <div>
                            <div class="text-green-600 text-sm mb-1">Proyeksi Skor</div>
                            <div class="font-medium">{{ $latestEvaluation->projected_risk_score ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                @endif

                <form action="{{ route('risk-evaluations.store', $risk->risk_id) }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div>
                            <!-- Evaluation Date -->
                            <div class="mb-6">
                                <label for="evaluation_date" class="form-label">Tanggal Evaluasi <span class="text-red-500">*</span></label>
                                <input type="date" 
                                       id="evaluation_date" 
                                       name="evaluation_date" 
                                       class="form-control w-full @error('evaluation_date') border-red-500 @enderror"
                                       value="{{ old('evaluation_date', date('Y-m-d')) }}"
                                       required>
                                @error('evaluation_date')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Risk Evaluation Priority -->
                            <div class="mb-6">
                                <label class="form-label">Prioritas Penanganan Risiko <span class="text-red-500">*</span></label>
                                <div class="priority-scale mt-2">
                                    @foreach(['sangat tinggi', 'tinggi', 'sedang', 'rendah'] as $priority)
                                        <div class="flex items-center mb-3">
                                            <input type="radio" 
                                                   id="priority_{{ str_replace(' ', '_', $priority) }}" 
                                                   name="risk_evaluation_priority" 
                                                   value="{{ $priority }}"
                                                   class="form-radio priority-radio" 
                                                   {{ old('risk_evaluation_priority') == $priority ? 'checked' : '' }}
                                                   required>
                                            <label for="priority_{{ str_replace(' ', '_', $priority) }}" class="ml-3 flex-1 cursor-pointer">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <span class="font-medium">{{ ucwords($priority) }}: </span>
                                                        @php
                                                            $priorityDesc = match($priority) {
                                                                'rendah' => 'Dapat ditunda, dampak minimal',
                                                                'sedang' => 'Perlu penanganan dalam waktu menengah',
                                                                'tinggi' => 'Perlu penanganan segera',
                                                                'sangat tinggi' => 'Harus ditangani segera, dampak kritis'
                                                            };
                                                        @endphp
                                                        <span class="text-gray-600">{{ $priorityDesc }}</span>
                                                    </div>
                                                    <div class="w-6 h-6 rounded-full 
                                                        @if($priority == 'rendah') bg-green-100 border-2 border-green-500
                                                        @elseif($priority == 'sedang') bg-yellow-100 border-2 border-yellow-500
                                                        @elseif($priority == 'tinggi') bg-orange-100 border-2 border-orange-500
                                                        @else bg-red-100 border-2 border-red-500
                                                        @endif"></div>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('risk_evaluation_priority')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Projected Risk Score -->
                            <div class="mb-6">
                                <label for="projected_risk_score" class="form-label">Proyeksi Skor Risiko</label>
                                <div class="relative">
                                    <input type="number" 
                                           id="projected_risk_score" 
                                           name="projected_risk_score" 
                                           class="form-control w-full @error('projected_risk_score') border-red-500 @enderror"
                                           value="{{ old('projected_risk_score') }}"
                                           min="1" 
                                           max="25" 
                                           step="0.01"
                                           placeholder="Masukkan proyeksi skor (1-25)">
                                    <div class="absolute right-3 top-2.5 text-gray-400">
                                        /25
                                    </div>
                                </div>
                                <div class="text-gray-500 text-xs mt-1">
                                    Proyeksi besaran risiko akhir periode. Kosongkan jika belum ada proyeksi.
                                </div>
                                @error('projected_risk_score')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div>
                            <!-- Mitigation Decision -->
                            <div class="mb-6">
                                <label for="mitigation_decision" class="form-label">Keputusan Mitigasi <span class="text-red-500">*</span></label>
                                <textarea id="mitigation_decision" 
                                          name="mitigation_decision" 
                                          class="form-control w-full @error('mitigation_decision') border-red-500 @enderror"
                                          rows="8"
                                          placeholder="Jelaskan keputusan mitigasi yang akan diambil..."
                                          required>{{ old('mitigation_decision') }}</textarea>
                                <div class="text-gray-500 text-xs mt-1">
                                    Jelaskan apa yang diputuskan untuk menangani risiko ini.
                                </div>
                                @error('mitigation_decision')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Evaluation Notes -->
                            <div class="mb-6">
                                <label for="evaluation_notes" class="form-label">Catatan Evaluasi</label>
                                <textarea id="evaluation_notes" 
                                          name="evaluation_notes" 
                                          class="form-control w-full @error('evaluation_notes') border-red-500 @enderror"
                                          rows="4"
                                          placeholder="Tambahkan catatan atau penjelasan tambahan terkait evaluasi ini...">{{ old('evaluation_notes') }}</textarea>
                                @error('evaluation_notes')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Score Comparison Preview -->
                    <div class="mt-8 mb-6 bg-gray-50 p-6 rounded-lg">
                        <h4 class="font-medium text-gray-700 mb-4">Perbandingan Skor</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="text-gray-600 text-sm mb-2">Skor Saat Ini</div>
                                @if($risk->risk_score)
                                    <div class="text-4xl font-bold 
                                        @if($risk->risk_level == 'sangat_tinggi') text-red-600
                                        @elseif($risk->risk_level == 'tinggi') text-orange-600
                                        @elseif($risk->risk_level == 'sedang') text-yellow-600
                                        @elseif($risk->risk_level == 'rendah') text-blue-600
                                        @else text-green-600
                                        @endif">
                                        {{ $risk->risk_score }}
                                    </div>
                                @else
                                    <div class="text-4xl font-bold text-gray-400">-</div>
                                @endif
                                <div class="text-sm text-gray-500 mt-1">Berdasarkan analisis terakhir</div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-600 text-sm mb-2">Proyeksi Skor</div>
                                <div id="projectedScorePreview" class="text-4xl font-bold text-gray-400">-</div>
                                <div class="text-sm text-gray-500 mt-1">Proyeksi akhir periode</div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-600 text-sm mb-2">Perubahan</div>
                                <div id="scoreChangeIndicator" class="text-lg font-medium text-gray-700">-</div>
                                <div id="scoreChangeDetails" class="text-sm text-gray-500 mt-1">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Priority Guide -->
                    <div class="mt-6 mb-8">
                        <h4 class="font-medium text-gray-700 mb-4">Panduan Prioritas Penanganan</h4>
                        <div class="bg-white border rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="p-2 border">Prioritas</th>
                                        <th class="p-2 border">Warna</th>
                                        <th class="p-2 border">Waktu Penanganan</th>
                                        <th class="p-2 border">Tindakan yang Disarankan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="p-2 border">
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Sangat Tinggi</span>
                                        </td>
                                        <td class="p-2 border">
                                            <div class="w-6 h-6 bg-red-500 rounded mx-auto"></div>
                                        </td>
                                        <td class="p-2 border text-sm">Segera (≤ 24 jam)</td>
                                        <td class="p-2 border text-sm">Tindakan darurat, alokasi sumber daya maksimal</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 border">
                                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs">Tinggi</span>
                                        </td>
                                        <td class="p-2 border">
                                            <div class="w-6 h-6 bg-orange-500 rounded mx-auto"></div>
                                        </td>
                                        <td class="p-2 border text-sm">Cepat (≤ 3 hari)</td>
                                        <td class="p-2 border text-sm">Perlu tindakan segera, monitoring intensif</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 border">
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Sedang</span>
                                        </td>
                                        <td class="p-2 border">
                                            <div class="w-6 h-6 bg-yellow-500 rounded mx-auto"></div>
                                        </td>
                                        <td class="p-2 border text-sm">Menengah (≤ 2 minggu)</td>
                                        <td class="p-2 border text-sm">Perencanaan matang, tindakan terstruktur</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 border">
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Rendah</span>
                                        </td>
                                        <td class="p-2 border">
                                            <div class="w-6 h-6 bg-green-500 rounded mx-auto"></div>
                                        </td>
                                        <td class="p-2 border text-sm">Bisa ditunda (≤ 1 bulan)</td>
                                        <td class="p-2 border text-sm">Tindakan rutin, monitoring berkala</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end pt-5 border-t">
                        <a href="{{ route('risk-evaluations.by-risk', $risk->risk_id) }}" class="btn btn-outline-secondary w-24 mr-3">
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
    
    const currentScore = {{ $risk->risk_score ?? 0 }};
    const projectedScoreInput = document.getElementById('projected_risk_score');
    const projectedScorePreview = document.getElementById('projectedScorePreview');
    const scoreChangeIndicator = document.getElementById('scoreChangeIndicator');
    const scoreChangeDetails = document.getElementById('scoreChangeDetails');
    
    function updateScorePreview() {
        const projectedScore = parseFloat(projectedScoreInput.value);
        
        if (projectedScore && !isNaN(projectedScore)) {
            // Update projected score preview
            projectedScorePreview.textContent = projectedScore.toFixed(2);
            
            // Update score color
            projectedScorePreview.className = 'text-4xl font-bold';
            if (projectedScore >= 20) projectedScorePreview.classList.add('text-red-600');
            else if (projectedScore >= 15) projectedScorePreview.classList.add('text-orange-600');
            else if (projectedScore >= 10) projectedScorePreview.classList.add('text-yellow-600');
            else if (projectedScore >= 5) projectedScorePreview.classList.add('text-blue-600');
            else projectedScorePreview.classList.add('text-green-600');
            
            // Calculate change
            if (currentScore > 0) {
                const change = projectedScore - currentScore;
                const percentageChange = (change / currentScore) * 100;
                
                if (change > 0) {
                    scoreChangeIndicator.innerHTML = `<span class="text-red-600">Meningkat ${change.toFixed(2)} poin</span>`;
                    scoreChangeIndicator.className = 'text-lg font-medium text-red-600';
                    scoreChangeDetails.textContent = `(${percentageChange.toFixed(1)}% dari skor saat ini)`;
                } else if (change < 0) {
                    scoreChangeIndicator.innerHTML = `<span class="text-green-600">Menurun ${Math.abs(change).toFixed(2)} poin</span>`;
                    scoreChangeIndicator.className = 'text-lg font-medium text-green-600';
                    scoreChangeDetails.textContent = `(${Math.abs(percentageChange).toFixed(1)}% dari skor saat ini)`;
                } else {
                    scoreChangeIndicator.innerHTML = `<span class="text-blue-600">Tidak berubah</span>`;
                    scoreChangeIndicator.className = 'text-lg font-medium text-blue-600';
                    scoreChangeDetails.textContent = `(Skor tetap sama)`;
                }
            } else {
                scoreChangeIndicator.textContent = 'Tidak ada skor saat ini';
                scoreChangeIndicator.className = 'text-lg font-medium text-gray-600';
                scoreChangeDetails.textContent = 'Buat analisis terlebih dahulu';
            }
        } else {
            projectedScorePreview.textContent = '-';
            projectedScorePreview.className = 'text-4xl font-bold text-gray-400';
            scoreChangeIndicator.textContent = '-';
            scoreChangeDetails.textContent = '-';
        }
    }
    
    // Add event listener to projected score input
    if (projectedScoreInput) {
        projectedScoreInput.addEventListener('input', updateScorePreview);
        projectedScoreInput.addEventListener('change', updateScorePreview);
    }
    
    // Initialize preview
    updateScorePreview();
    
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