@extends('layouts.master')

@section('title', 'Tambah UPTD Baru - SIMR')

@section('page-title', 'Tambah UPTD Baru')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('organizations.index') }}">Organisasi</a></li>
    <li class="breadcrumb-item active">Tambah UPTD</li>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 xl:col-span-8">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Form Tambah UPTD Baru
                </h2>
                <a href="{{ route('organizations.index') }}" class="btn btn-outline-secondary">
                    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
                </a>
            </div>
            <form method="POST" action="{{ route('organizations.store') }}" class="p-5">
                @csrf
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Parent Organization (Fixed) -->
                    <div class="p-4 bg-blue-50 rounded-lg mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i data-feather="home" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-blue-800">Organisasi Induk</h4>
                                <p class="text-sm text-blue-600">UPTD akan ditambahkan di bawah organisasi ini</p>
                            </div>
                        </div>
                        <div class="mt-3 ml-13">
                            <div class="font-bold">{{ $dinasPUPR->organization_name }}</div>
                            <div class="text-sm text-gray-600">{{ $dinasPUPR->organization_code }}</div>
                        </div>
                    </div>
                    
                    <!-- Location -->
                    <div>
                        <label for="location" class="form-label">
                            Lokasi UPTD <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="location" name="location" 
                               class="form-control w-full" 
                               placeholder="Contoh: UPTD Medan, UPTD Sibolga, dll."
                               value="{{ old('location') }}"
                               required>
                        <div class="text-xs text-gray-500 mt-1">
                            * Lokasi geografis atau wilayah kerja UPTD
                        </div>
                        @error('location')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Organization Code -->
                    <div>
                        <label for="organization_code" class="form-label">
                            Kode UPTD <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="organization_code" name="organization_code" 
                               class="form-control w-full" 
                               placeholder="Contoh: UPTD-MEDAN-001, UPTD-SBLG-001"
                               value="{{ old('organization_code') }}"
                               required>
                        <div class="text-xs text-gray-500 mt-1">
                            * Kode unik untuk identifikasi UPTD. Format: UPTD-[LOKASI]-[NOMOR]
                        </div>
                        @error('organization_code')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Description -->
                    <div>
                        <label for="organization_description" class="form-label">
                            Deskripsi UPTD
                        </label>
                        <textarea id="organization_description" name="organization_description" 
                                  class="form-control w-full" 
                                  rows="4" 
                                  placeholder="Jelaskan secara detail tentang UPTD ini...">
                            {{ old('organization_description') }}
                        </textarea>
                        <div class="text-xs text-gray-500 mt-1">
                            * Deskripsi tentang wilayah kerja, tugas pokok, dan fungsi UPTD
                        </div>
                        @error('organization_description')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Informasi -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <h4 class="font-bold text-blue-800 mb-2 flex items-center">
                        <i data-feather="info" class="w-5 h-5 mr-2"></i> Informasi Penting:
                    </h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• UPTD adalah Unit Pelaksana Teknis di bawah Dinas PUPR</li>
                        <li>• Setiap UPTD memiliki lokasi/wilayah kerja tertentu</li>
                        <li>• Kode UPTD harus unik dan tidak boleh sama</li>
                        <li>• UPTD yang sudah dibuat dapat dikaitkan dengan data risiko dan proyek</li>
                    </ul>
                </div>
                
                <!-- Contoh UPTD -->
                <div class="mt-6 p-4 bg-green-50 rounded-lg border border-green-100">
                    <h4 class="font-bold text-green-800 mb-2 flex items-center">
                        <i data-feather="book-open" class="w-5 h-5 mr-2"></i> Contoh UPTD:
                    </h4>
                    <div class="text-sm text-green-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <strong>Lokasi:</strong> Medan Kota<br>
                                <strong>Kode:</strong> UPTD-MEDAN-001<br>
                                <strong>Deskripsi:</strong> UPTD wilayah pusat Kota Medan
                            </div>
                            <div>
                                <strong>Lokasi:</strong> Tapanuli Selatan<br>
                                <strong>Kode:</strong> UPTD-TAPSEL-001<br>
                                <strong>Deskripsi:</strong> UPTD wilayah Kabupaten Tapanuli Selatan
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end mt-8">
                    <a href="{{ route('organizations.index') }}" class="btn btn-outline-secondary w-24 mr-3">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary w-32">
                        <i data-feather="save" class="w-4 h-4 mr-2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-span-12 xl:col-span-4">
        <!-- UPTD yang Sudah Ada -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    UPTD yang Sudah Ada
                </h2>
                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded">
                    {{ $existingUptd->count() }} UPTD
                </span>
            </div>
            <div class="p-5">
                @if($existingUptd->count() > 0)
                    <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                        @foreach($existingUptd as $uptd)
                            <div class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3">
                                        <i data-feather="building" class="w-4 h-4 text-theme-1"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $uptd->organization_code }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $uptd->location }} • {{ $uptd->organization_description ? Str::limit($uptd->organization_description, 30) : 'Tidak ada deskripsi' }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-medium text-sm">{{ $uptd->risks_count ?? 0 }}</div>
                                        <div class="text-xs text-gray-500">risiko</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i data-feather="building" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                        <p class="text-gray-500">Belum ada UPTD</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Panduan -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="help-circle" class="w-5 h-5 mr-2 inline"></i> Panduan
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="map-pin" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Lokasi Spesifik</h4>
                            <p class="text-sm text-gray-600">Masukkan lokasi/wilayah kerja UPTD dengan jelas</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="hash" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Kode Unik</h4>
                            <p class="text-sm text-gray-600">Gunakan format standar untuk konsistensi</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="file-text" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Deskripsi Jelas</h4>
                            <p class="text-sm text-gray-600">Jelaskan tugas pokok dan fungsi UPTD</p>
                        </div>
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
    
    // Update preview
    const locationInput = document.getElementById('location');
    const codeInput = document.getElementById('organization_code');
    const descInput = document.getElementById('organization_description');
    
    const previewName = document.getElementById('preview-name');
    const previewLocation = document.getElementById('preview-location');
    const previewCode = document.getElementById('preview-code');
    const previewDesc = document.getElementById('preview-description');
    
    function updatePreview() {
        if (locationInput.value) {
            previewLocation.textContent = locationInput.value;
        } else {
            previewLocation.textContent = 'Lokasi';
        }
        
        if (codeInput.value) {
            previewCode.textContent = codeInput.value;
        } else {
            previewCode.textContent = 'Kode';
        }
        
        if (descInput.value) {
            previewDesc.textContent = descInput.value;
        } else {
            previewDesc.textContent = 'Deskripsi akan muncul di sini';
        }
    }
    
    locationInput.addEventListener('input', updatePreview);
    codeInput.addEventListener('input', updatePreview);
    descInput.addEventListener('input', updatePreview);
    
    // Auto-generate code suggestion
    locationInput.addEventListener('blur', function() {
        if (locationInput.value && !codeInput.value) {
            const location = locationInput.value.toUpperCase().replace(/\s+/g, '-');
            const suggestedCode = `UPTD-${location}-001`;
            codeInput.value = suggestedCode;
            updatePreview();
        }
    });
});
</script>
@endpush