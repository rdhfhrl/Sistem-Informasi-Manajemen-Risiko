@extends('layouts.master')

@section('title', 'Buat Rencana Mitigasi Baru - SIMR')

@section('page-title', 'Buat Rencana Mitigasi Baru')

@section('page-action')
<a href="{{ route('risk-mitigations.by-risk', $risk->risk_id) }}" class="btn btn-outline-secondary shadow-md mr-2">
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
                    Form Rencana Mitigasi Baru
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
                            <div class="text-blue-600 text-sm mb-1">Skor Risiko</div>
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

                <form action="{{ route('risk-mitigations.store', $risk->risk_id) }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div>
                            <!-- Mitigation Plan -->
                            <div class="mb-6">
                                <label for="mitigation_plan" class="form-label">Rencana Mitigasi <span class="text-red-500">*</span></label>
                                <textarea id="mitigation_plan" 
                                          name="mitigation_plan" 
                                          class="form-control w-full @error('mitigation_plan') border-red-500 @enderror"
                                          rows="6"
                                          placeholder="Jelaskan rencana mitigasi yang akan dilaksanakan..."
                                          required>{{ old('mitigation_plan') }}</textarea>
                                <div class="text-gray-500 text-xs mt-1">
                                    Rencana detail untuk mengurangi atau menghilangkan risiko.
                                </div>
                                @error('mitigation_plan')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Responsible Party -->
                            <div class="mb-6">
                                <label for="responsible_party" class="form-label">Penanggung Jawab <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       id="responsible_party" 
                                       name="responsible_party" 
                                       class="form-control w-full @error('responsible_party') border-red-500 @enderror"
                                       value="{{ old('responsible_party') }}"
                                       placeholder="Masukkan nama penanggung jawab..."
                                       required>
                                @error('responsible_party')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Deadline -->
                            <div class="mb-6">
                                <label for="deadline" class="form-label">Deadline <span class="text-red-500">*</span></label>
                                <input type="date" 
                                       id="deadline" 
                                       name="deadline" 
                                       class="form-control w-full @error('deadline') border-red-500 @enderror"
                                       value="{{ old('deadline') }}"
                                       required>
                                @error('deadline')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div>
                            <!-- Status -->
                            <div class="mb-6">
                                <label for="status" class="form-label">Status <span class="text-red-500">*</span></label>
                                <select id="status" 
                                        name="status" 
                                        class="form-select w-full @error('status') border-red-500 @enderror"
                                        required>
                                    <option value="">Pilih Status</option>
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}" {{ old('status') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Budget -->
                            <div class="mb-6">
                                <label for="budget" class="form-label">Anggaran (Rp)</label>
                                <div class="relative">
                                    <div class="absolute left-3 top-2.5 text-gray-500">Rp</div>
                                    <input type="number" 
                                           id="budget" 
                                           name="budget" 
                                           class="form-control w-full pl-10 @error('budget') border-red-500 @enderror"
                                           value="{{ old('budget') }}"
                                           placeholder="0"
                                           min="0"
                                           step="1000">
                                </div>
                                @error('budget')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Resources -->
                            <div class="mb-6">
                                <label for="resources" class="form-label">Sumber Daya</label>
                                <textarea id="resources" 
                                          name="resources" 
                                          class="form-control w-full @error('resources') border-red-500 @enderror"
                                          rows="3"
                                          placeholder="Sebutkan sumber daya yang dibutuhkan...">{{ old('resources') }}</textarea>
                                @error('resources')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Success Criteria -->
                            <div class="mb-6">
                                <label for="success_criteria" class="form-label">Kriteria Keberhasilan</label>
                                <textarea id="success_criteria" 
                                          name="success_criteria" 
                                          class="form-control w-full @error('success_criteria') border-red-500 @enderror"
                                          rows="3"
                                          placeholder="Tentukan kriteria keberhasilan mitigasi...">{{ old('success_criteria') }}</textarea>
                                @error('success_criteria')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="mb-6">
                                <label for="notes" class="form-label">Catatan</label>
                                <textarea id="notes" 
                                          name="notes" 
                                          class="form-control w-full @error('notes') border-red-500 @enderror"
                                          rows="3"
                                          placeholder="Tambahkan catatan atau keterangan tambahan...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Deadline Warning -->
                    <div class="mt-8 mb-6 bg-yellow-50 p-4 rounded-lg">
                        <div class="flex items-start">
                            <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center mr-3 mt-0.5">
                                <i data-feather="alert-triangle" class="w-5 h-5 text-yellow-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-yellow-800 mb-1">Perhatian Deadline</h4>
                                <p class="text-yellow-700 text-sm">
                                    Pastikan deadline yang ditetapkan realistis dan memperhitungkan waktu yang dibutuhkan untuk implementasi.
                                    Sistem akan memberikan notifikasi ketika deadline mendekati atau terlambat.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Status Guide -->
                    <div class="mt-6 mb-8">
                        <h4 class="font-medium text-gray-700 mb-4">Panduan Status Mitigasi</h4>
                        <div class="bg-white border rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="p-2 border">Status</th>
                                        <th class="p-2 border">Warna</th>
                                        <th class="p-2 border">Deskripsi</th>
                                        <th class="p-2 border">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="p-2 border">
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Selesai</span>
                                        </td>
                                        <td class="p-2 border">
                                            <div class="w-6 h-6 bg-green-500 rounded mx-auto"></div>
                                        </td>
                                        <td class="p-2 border text-sm">Mitigasi telah selesai dilaksanakan</td>
                                        <td class="p-2 border text-sm">Lengkapi dokumentasi dan evaluasi hasil</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 border">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Dalam Proses</span>
                                        </td>
                                        <td class="p-2 border">
                                            <div class="w-6 h-6 bg-blue-500 rounded mx-auto"></div>
                                        </td>
                                        <td class="p-2 border text-sm">Sedang dalam tahap pelaksanaan</td>
                                        <td class="p-2 border text-sm">Monitoring dan update progress berkala</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 border">
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Belum Dimulai</span>
                                        </td>
                                        <td class="p-2 border">
                                            <div class="w-6 h-6 bg-gray-500 rounded mx-auto"></div>
                                        </td>
                                        <td class="p-2 border text-sm">Rencana sudah dibuat, belum dimulai</td>
                                        <td class="p-2 border text-sm">Persiapan sumber daya dan jadwal</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 border">
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Ditunda</span>
                                        </td>
                                        <td class="p-2 border">
                                            <div class="w-6 h-6 bg-yellow-500 rounded mx-auto"></div>
                                        </td>
                                        <td class="p-2 border text-sm">Ditunda karena alasan tertentu</td>
                                        <td class="p-2 border text-sm">Evaluasi alasan penundaan dan rencana ulang</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 border">
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Dibatalkan</span>
                                        </td>
                                        <td class="p-2 border">
                                            <div class="w-6 h-6 bg-red-500 rounded mx-auto"></div>
                                        </td>
                                        <td class="p-2 border text-sm">Dibatalkan karena pertimbangan tertentu</td>
                                        <td class="p-2 border text-sm">Dokumentasi alasan pembatalan</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end pt-5 border-t">
                        <a href="{{ route('risk-mitigations.by-risk', $risk->risk_id) }}" class="btn btn-outline-secondary w-24 mr-3">
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
    
    // Set minimum date to today for deadline
    const deadlineInput = document.getElementById('deadline');
    if (deadlineInput) {
        const today = new Date().toISOString().split('T')[0];
        deadlineInput.min = today;
        
        // If no value, set default to 30 days from now
        if (!deadlineInput.value) {
            const futureDate = new Date();
            futureDate.setDate(futureDate.getDate() + 30);
            deadlineInput.value = futureDate.toISOString().split('T')[0];
        }
    }
    
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