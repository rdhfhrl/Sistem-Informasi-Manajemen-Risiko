@extends('layouts.master')

@section('title', 'Edit Rencana Mitigasi - SIMR')

@section('page-title', 'Edit Rencana Mitigasi')

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
                    <i data-feather="edit-2" class="w-5 h-5 mr-2"></i>
                    Edit Rencana Mitigasi
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

                <!-- Current Mitigation Summary -->
                <div class="mb-8 bg-gray-50 p-6 rounded-lg">
                    <h4 class="font-medium text-gray-700 mb-4">Ringkasan Rencana Saat Ini</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-gray-600 text-sm mb-2">Status</div>
                            <div class="font-medium">
                                <span class="px-3 py-1 rounded-full text-sm 
                                    @if($mitigation->status == 'selesai') bg-green-100 text-green-800
                                    @elseif($mitigation->status == 'dalam proses') bg-blue-100 text-blue-800
                                    @elseif($mitigation->status == 'belum dimulai') bg-gray-100 text-gray-800
                                    @elseif($mitigation->status == 'ditunda') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    @switch($mitigation->status)
                                        @case('belum dimulai') Belum Dimulai @break
                                        @case('dalam proses') Dalam Proses @break
                                        @case('selesai') Selesai @break
                                        @case('ditunda') Ditunda @break
                                        @case('dibatalkan') Dibatalkan @break
                                    @endswitch
                                </span>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-gray-600 text-sm mb-2">Deadline</div>
                            <div class="font-medium text-lg {{ \Carbon\Carbon::parse($mitigation->deadline)->lt(now()) ? 'text-red-600' : 'text-gray-800' }}">
                                {{ $mitigation->deadline->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                @if(\Carbon\Carbon::parse($mitigation->deadline)->lt(now()))
                                    Terlambat {{ \Carbon\Carbon::parse($mitigation->deadline)->diffInDays(now()) }} hari
                                @else
                                    {{ \Carbon\Carbon::parse($mitigation->deadline)->diffInDays(now()) }} hari lagi
                                @endif
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-gray-600 text-sm mb-2">Penanggung Jawab</div>
                            <div class="font-medium">{{ $mitigation->responsible_party }}</div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('risk-mitigations.update', [$risk->risk_id, $mitigation->risk_mitigation_id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
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
                                          required>{{ old('mitigation_plan', $mitigation->mitigation_plan) }}</textarea>
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
                                       value="{{ old('responsible_party', $mitigation->responsible_party) }}"
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
                                       value="{{ old('deadline', $mitigation->deadline->format('Y-m-d')) }}"
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
                                        <option value="{{ $value }}" {{ old('status', $mitigation->status) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Additional Fields (only show for status 'selesai') -->
                            <div id="completionFields" style="{{ in_array(old('status', $mitigation->status), ['selesai']) ? '' : 'display: none;' }}">
                                <!-- Completion Date -->
                                <div class="mb-6">
                                    <label for="completion_date" class="form-label">Tanggal Penyelesaian</label>
                                    <input type="date" 
                                           id="completion_date" 
                                           name="completion_date" 
                                           class="form-control w-full @error('completion_date') border-red-500 @enderror"
                                           value="{{ old('completion_date') }}"
                                           max="{{ date('Y-m-d') }}">
                                    @error('completion_date')
                                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Actual Cost -->
                                <div class="mb-6">
                                    <label for="actual_cost" class="form-label">Biaya Aktual (Rp)</label>
                                    <div class="relative">
                                        <div class="absolute left-3 top-2.5 text-gray-500">Rp</div>
                                        <input type="number" 
                                               id="actual_cost" 
                                               name="actual_cost" 
                                               class="form-control w-full pl-10 @error('actual_cost') border-red-500 @enderror"
                                               value="{{ old('actual_cost') }}"
                                               placeholder="0"
                                               min="0"
                                               step="1000">
                                    </div>
                                    @error('actual_cost')
                                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
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
                                           value="{{ old('budget', $mitigation->budget) }}"
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
                                          placeholder="Sebutkan sumber daya yang dibutuhkan...">{{ old('resources', $mitigation->resources) }}</textarea>
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
                                          placeholder="Tentukan kriteria keberhasilan mitigasi...">{{ old('success_criteria', $mitigation->success_criteria) }}</textarea>
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
                                          placeholder="Tambahkan catatan atau keterangan tambahan...">{{ old('notes', $mitigation->notes) }}</textarea>
                                @error('notes')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Progress Notes -->
                    <div class="mt-6 mb-6">
                        <label for="progress_notes" class="form-label">Catatan Progress</label>
                        <textarea id="progress_notes" 
                                  name="progress_notes" 
                                  class="form-control w-full @error('progress_notes') border-red-500 @enderror"
                                  rows="4"
                                  placeholder="Tambahkan catatan perkembangan atau update terbaru...">{{ old('progress_notes') }}</textarea>
                        <div class="text-gray-500 text-xs mt-1">
                            Berguna untuk melacak perkembangan implementasi mitigasi.
                        </div>
                        @error('progress_notes')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Deadline Warning -->
                    @php
                        $isOverdue = \Carbon\Carbon::parse($mitigation->deadline)->lt(now());
                        $daysOverdue = $isOverdue ? \Carbon\Carbon::parse($mitigation->deadline)->diffInDays(now()) : 0;
                    @endphp
                    <div class="mt-8 mb-6 p-4 rounded-lg {{ $isOverdue ? 'bg-red-50' : 'bg-yellow-50' }}">
                        <div class="flex items-start">
                            <div class="w-8 h-8 rounded-full {{ $isOverdue ? 'bg-red-100' : 'bg-yellow-100' }} flex items-center justify-center mr-3 mt-0.5">
                                <i data-feather="alert-triangle" class="w-5 h-5 {{ $isOverdue ? 'text-red-600' : 'text-yellow-600' }}"></i>
                            </div>
                            <div>
                                <h4 class="font-medium {{ $isOverdue ? 'text-red-800' : 'text-yellow-800' }} mb-1">
                                    {{ $isOverdue ? '⚠️ MITIGASI TERLAMBAT' : 'Perhatian Deadline' }}
                                </h4>
                                <p class="{{ $isOverdue ? 'text-red-700' : 'text-yellow-700' }} text-sm">
                                    @if($isOverdue)
                                        Deadline telah terlambat {{ $daysOverdue }} hari. Segera evaluasi dan perbarui status atau deadline.
                                    @else
                                        Deadline: {{ $mitigation->deadline->format('d F Y') }} ({{ \Carbon\Carbon::parse($mitigation->deadline)->diffInDays(now()) }} hari lagi).
                                        Pastikan progress sesuai dengan timeline yang direncanakan.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-between items-center pt-5 border-t">
                        <div>
                            <form action="{{ route('risk-mitigations.destroy', [$risk->risk_id, $mitigation->risk_mitigation_id]) }}" 
                                  method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rencana mitigasi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus
                                </button>
                            </form>
                        </div>
                        <div class="flex">
                            <a href="{{ route('risk-mitigations.by-risk', $risk->risk_id) }}" class="btn btn-outline-secondary w-24 mr-3">
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
    
    // Show/hide completion fields based on status
    const statusSelect = document.getElementById('status');
    const completionFields = document.getElementById('completionFields');
    
    if (statusSelect && completionFields) {
        statusSelect.addEventListener('change', function() {
            if (this.value === 'selesai') {
                completionFields.style.display = 'block';
            } else {
                completionFields.style.display = 'none';
            }
        });
        
        // Initialize on page load
        if (statusSelect.value === 'selesai') {
            completionFields.style.display = 'block';
        }
    }
    
    // Set today's date as max for completion date
    const completionDateInput = document.getElementById('completion_date');
    if (completionDateInput) {
        const today = new Date().toISOString().split('T')[0];
        completionDateInput.max = today;
    }
    
    // Set minimum date to today for deadline
    const deadlineInput = document.getElementById('deadline');
    if (deadlineInput) {
        const today = new Date().toISOString().split('T')[0];
        deadlineInput.min = today;
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