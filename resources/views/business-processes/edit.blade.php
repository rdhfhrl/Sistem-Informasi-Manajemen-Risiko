@extends('layouts.master')

@section('title', 'Edit Proses Bisnis - SIMR')

@section('page-title', 'Edit Proses Bisnis')

@section('page-action')
<div class="w-full sm:w-auto flex">
    <a href="{{ route('business-processes.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
        <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
    </a>
</div>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 xl:col-span-8">
        <!-- Edit Form -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                    <i data-feather="edit-2" class="w-5 h-5 text-purple-600"></i>
                </div>
                <h2 class="font-medium text-base mr-auto">
                    Edit Proses Bisnis
                    <span class="text-gray-500 text-sm ml-2">
                        ID: BP-{{ str_pad($process->business_process_id, 4, '0', STR_PAD_LEFT) }}
                    </span>
                </h2>
                <div class="flex items-center">
                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded mr-2">
                        Dibuat: {{ \Carbon\Carbon::parse($process->created_at)->format('d/m/Y') }}
                    </span>
                    <span class="px-2 py-1 bg-purple-100 text-purple-600 text-xs rounded">
                        {{ $process->risks->count() }} Risiko
                    </span>
                </div>
            </div>
            <form method="POST" action="{{ route('business-processes.update', $process->business_process_id) }}" class="p-5">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-12 gap-6">
                    <!-- Organization -->
                    <div class="col-span-12">
                        <label for="business_process_organization_id" class="form-label">
                            Organisasi/UPTD <span class="text-danger">*</span>
                        </label>
                        <select id="business_process_organization_id" name="business_process_organization_id" 
                                class="form-select w-full @error('business_process_organization_id') border-danger @enderror" 
                                required>
                            <option value="">Pilih Organisasi...</option>
                            @foreach($organizations as $org)
                                <option value="{{ $org->organization_id }}" 
                                        {{ old('business_process_organization_id', $process->business_process_organization_id) == $org->organization_id ? 'selected' : '' }}>
                                    {{ $org->organization_code }} - {{ $org->organization_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('business_process_organization_id')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Process Name -->
                    <div class="col-span-12">
                        <label for="business_process_name" class="form-label">
                            Nama Proses Bisnis <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="business_process_name" name="business_process_name" 
                               class="form-control w-full @error('business_process_name') border-danger @enderror" 
                               placeholder="Contoh: Pengadaan Barang & Jasa"
                               value="{{ old('business_process_name', $process->business_process_name) }}"
                               required>
                        @error('business_process_name')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Process Description -->
                    <div class="col-span-12">
                        <label for="business_process_description" class="form-label">
                            Deskripsi Proses Bisnis <span class="text-danger">*</span>
                        </label>
                        <textarea id="business_process_description" name="business_process_description" 
                                  class="form-control w-full @error('business_process_description') border-danger @enderror" 
                                  rows="6"
                                  placeholder="Deskripsikan alur, tahapan, tujuan, dan pihak-pihak yang terlibat dalam proses ini..."
                                  required>{{ old('business_process_description', $process->business_process_description) }}</textarea>
                        <div class="text-xs text-gray-500 mt-1">
                            <span id="charCount">0</span> karakter (Rekomendasi: 100-500 karakter)
                        </div>
                        @error('business_process_description')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Impact Warning -->
                @if($process->risks->count() > 0)
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h4 class="font-bold text-yellow-800 mb-2 flex items-center">
                        <i data-feather="alert-triangle" class="w-5 h-5 mr-2"></i> Perhatian!
                    </h4>
                    <p class="text-yellow-700 text-sm">
                        Proses bisnis ini memiliki <strong>{{ $process->risks->count() }} risiko teridentifikasi</strong>. 
                        Perubahan pada proses bisnis mungkin mempengaruhi analisis risiko yang sudah ada. 
                        Pastikan untuk memperbarui data risiko jika diperlukan.
                    </p>
                </div>
                @endif
                
                <!-- Form Actions -->
                <div class="flex justify-end mt-8 pt-5 border-t border-gray-200">
                    <a href="{{ route('business-processes.show', $process->business_process_id) }}" 
                       class="btn btn-outline-secondary w-24 mr-3">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary w-32">
                        <i data-feather="save" class="w-4 h-4 mr-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-span-12 xl:col-span-4">
        <!-- Current Information -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="info" class="w-5 h-5 mr-2"></i> Informasi Saat Ini
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Nama Proses</div>
                        <div class="font-medium">{{ $process->business_process_name }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Organisasi</div>
                        <div class="font-medium">
                            @if($process->organization)
                                {{ $process->organization->organization_name }}
                            @else
                                <span class="text-danger">Belum ditetapkan</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Deskripsi</div>
                        <div class="text-sm text-gray-600 line-clamp-4">
                            {{ $process->business_process_description }}
                        </div>
                    </div>
                    <div class="pt-3 border-t border-gray-200">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="text-center p-3 bg-gray-50 rounded">
                                <div class="text-xs text-gray-500">Risiko Terkait</div>
                                <div class="text-xl font-bold">{{ $process->risks->count() }}</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded">
                                <div class="text-xs text-gray-500">Terakhir Diubah</div>
                                <div class="text-sm font-medium">{{ \Carbon\Carbon::parse($process->updated_at)->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Risks -->
        @if($process->risks->count() > 0)
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="alert-triangle" class="w-5 h-5 mr-2"></i> Risiko Terkait
                    <span class="text-gray-500 text-sm ml-2">({{ $process->risks->count() }})</span>
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3 max-h-80 overflow-y-auto pr-2">
                    @foreach($process->risks as $risk)
                    <a href="{{ route('risks.edit', $risk->risk_id) }}" 
                       class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                        <div class="w-8 h-8 rounded-full 
                            @if(strtolower($risk->risk_level ?? '') == 'tinggi' || strtolower($risk->risk_level ?? '') == 'sangat tinggi') bg-red-100 
                            @elseif(strtolower($risk->risk_level ?? '') == 'sedang') bg-orange-100 
                            @else bg-yellow-100 
                            @endif flex items-center justify-center mr-3">
                            <i data-feather="alert-triangle" class="w-3 h-3 
                                @if(strtolower($risk->risk_level ?? '') == 'tinggi' || strtolower($risk->risk_level ?? '') == 'sangat tinggi') text-red-600 
                                @elseif(strtolower($risk->risk_level ?? '') == 'sedang') text-orange-600 
                                @else text-yellow-600 
                                @endif"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-sm truncate">{{ $risk->risk_name }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $risk->risk_level ?? 'Belum ditetapkan' }}
                            </div>
                        </div>
                        <i data-feather="chevron-right" class="w-4 h-4 text-gray-400"></i>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        
        <!-- Edit Guidelines -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="help-circle" class="w-5 h-5 mr-2"></i> Panduan Edit
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3 text-sm">
                    <div class="flex items-start">
                        <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center mr-2 flex-shrink-0">
                            <i data-feather="check" class="w-3 h-3 text-purple-600"></i>
                        </div>
                        <span>Pastikan deskripsi tetap mencerminkan aktivitas aktual</span>
                    </div>
                    <div class="flex items-start">
                        <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center mr-2 flex-shrink-0">
                            <i data-feather="check" class="w-3 h-3 text-purple-600"></i>
                        </div>
                        <span>Perubahan organisasi akan mempengaruhi semua risiko terkait</span>
                    </div>
                    <div class="flex items-start">
                        <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center mr-2 flex-shrink-0">
                            <i data-feather="check" class="w-3 h-3 text-purple-600"></i>
                        </div>
                        <span>Periksa kembali risiko setelah mengubah proses</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.line-clamp-4 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 4;
    line-clamp: 4;
}

.max-h-80::-webkit-scrollbar {
    width: 4px;
}

.max-h-80::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

.max-h-80::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 2px;
}

.max-h-80::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Elements
    const descTextarea = document.getElementById('business_process_description');
    const charCount = document.getElementById('charCount');
    
    // Update character count
    function updateCharCount() {
        const count = descTextarea.value.length;
        charCount.textContent = count;
        
        // Change color based on character count
        if (count < 100) {
            charCount.className = 'text-yellow-600 font-medium';
        } else if (count <= 500) {
            charCount.className = 'text-green-600 font-medium';
        } else {
            charCount.className = 'text-red-600 font-bold';
        }
    }
    
    // Event listener for textarea
    descTextarea.addEventListener('input', updateCharCount);
    
    // Initial update
    updateCharCount();
    
    // Form submission validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const descLength = descTextarea.value.length;
        
        if (descLength < 10) {
            e.preventDefault();
            alert('Deskripsi terlalu pendek. Minimal 10 karakter.');
            descTextarea.focus();
            return false;
        }
        
        if (descLength > 1000) {
            if (!confirm('Deskripsi melebihi 1000 karakter. Lanjutkan?')) {
                e.preventDefault();
                return false;
            }
        }
        
        const orgSelect = document.getElementById('business_process_organization_id');
        if (!orgSelect.value) {
            e.preventDefault();
            alert('Pilih organisasi/UPTD terlebih dahulu.');
            orgSelect.focus();
            return false;
        }
    });
});
</script>
@endpush