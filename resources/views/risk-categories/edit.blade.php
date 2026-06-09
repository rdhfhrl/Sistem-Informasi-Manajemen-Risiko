@extends('layouts.master')

@section('title', 'Edit Kategori Risiko - SIMR')

@section('page-title', 'Edit Kategori Risiko')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('risk-categories.index') }}">Kategori Risiko</a></li>
    <li class="breadcrumb-item"><a href="{{ route('risk-categories.show', $category->risk_category_id) }}">Detail</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
@php
    // ===== HELPER FUNCTIONS =====
    $getCategoryColor = function($categoryName) {
        $colors = [
            'Waktu' => 'bg-red-500',
            'Lingkungan' => 'bg-green-500',
            'Manajemen' => 'bg-blue-500',
            'Hukum' => 'bg-purple-500',
            'SDM' => 'bg-yellow-500',
            'K3' => 'bg-orange-500',
        ];
        return $colors[$categoryName] ?? 'bg-gray-500';
    };

    $getCategoryFeatherIcon = function($categoryName) {
        $icons = [
            'Waktu' => 'clock',
            'Lingkungan' => 'globe',
            'Manajemen' => 'briefcase',
            'Hukum' => 'trello',
            'SDM' => 'users',
            'K3' => 'shield',
        ];
        return $icons[$categoryName] ?? 'folder';
    };
@endphp

<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 xl:col-span-8">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full {{ $getCategoryColor($category->risk_category_name) }} flex items-center justify-center mr-4">
                        <i data-feather="{{ $getCategoryFeatherIcon($category->risk_category_name) }}" class="w-6 h-6 text-black"></i>
                    </div>
                    <div>
                        <h2 class="font-medium text-base mr-auto">
                            Edit Kategori: {{ $category->risk_category_name }}
                        </h2>
                        <div class="text-gray-500 text-sm">{{ $category->risks_count ?? 0 }} risiko terkait</div>
                    </div>
                </div>
                <div class="ml-auto">
                    <a href="{{ route('risk-categories.show', $category->risk_category_id) }}" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Batal
                    </a>
                </div>
            </div>
            
            <form method="POST" action="{{ route('risk-categories.update', $category->risk_category_id) }}" class="p-5">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Nama Kategori -->
                    <div>
                        <label for="risk_category_name" class="form-label">
                            Nama Kategori <span class="text-danger">*</span>
                        </label>
                        <select id="risk_category_name" name="risk_category_name" 
                                class="form-select w-full" required>
                            <option value="">Pilih Kategori...</option>
                            @foreach($categoryOptions as $option)
                                <option value="{{ $option }}" 
                                        {{ old('risk_category_name', $category->risk_category_name) == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        <div class="text-xs text-gray-500 mt-1">
                            * Kategori risiko standar berdasarkan ISO 31000 untuk proyek konstruksi
                        </div>
                        @error('risk_category_name')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Deskripsi Kategori -->
                    <div>
                        <label for="risk_category_description" class="form-label">
                            Deskripsi Kategori <span class="text-danger">*</span>
                        </label>
                        <textarea id="risk_category_description" name="risk_category_description" 
                                  class="form-control w-full" 
                                  rows="6" 
                                  placeholder="Jelaskan secara detail tentang kategori risiko ini..."
                                  required>{{ old('risk_category_description', $category->risk_category_description) }}</textarea>
                        <div class="text-xs text-gray-500 mt-1">
                            * Deskripsi harus jelas dan membantu dalam identifikasi risiko
                        </div>
                        @error('risk_category_description')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Status Kategori -->
                    <div>
                        <label for="status" class="form-label">Status Kategori</label>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input type="radio" id="status_active" name="status" value="active" 
                                       class="form-radio" 
                                       {{ old('status', $category->status ?? 'active') == 'active' ? 'checked' : '' }}>
                                <label for="status_active" class="ml-2">Aktif</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="status_inactive" name="status" value="inactive" 
                                       class="form-radio"
                                       {{ old('status', $category->status ?? 'active') == 'inactive' ? 'checked' : '' }}>
                                <label for="status_inactive" class="ml-2">Non-Aktif</label>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            * Kategori non-aktif tidak akan muncul dalam pilihan saat menambah risiko baru
                        </div>
                    </div>
                </div>
                
                <!-- Preview Kategori -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium mb-3">Preview Perubahan</h4>
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full {{ $getCategoryColor($category->risk_category_name) }} flex items-center justify-center mr-4" 
                             id="preview-icon">
                            <i data-feather="{{ $getCategoryFeatherIcon($category->risk_category_name) }}" class="w-6 h-6 text-white" id="preview-icon-svg"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-lg" id="preview-name">{{ $category->risk_category_name }}</div>
                            <div class="text-gray-600 text-sm" id="preview-description">{{ $category->risk_category_description }}</div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full" id="preview-status">
                                Aktif
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Peringatan Perubahan -->
                <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                    <h4 class="font-bold text-yellow-800 mb-2 flex items-center">
                        <i data-feather="alert-triangle" class="w-4 h-4 mr-2"></i> Peringatan Perubahan:
                    </h4>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• Perubahan nama kategori akan mempengaruhi semua risiko yang menggunakan kategori ini</li>
                        <li>• Pastikan deskripsi tetap relevan dengan standar saat ini</li>
                        <li>• Kategori yang non-aktif masih dapat dilihat di laporan historis</li>
                        <li>• Verifikasi perubahan sebelum menyimpan</li>
                    </ul>
                </div>
                
                <!-- Informasi Risiko Terkait -->
                @if($category->risks_count > 0)
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <h4 class="font-bold text-blue-800 mb-2 flex items-center">
                        <i data-feather="info" class="w-4 h-4 mr-2"></i> Informasi Risiko Terkait:
                    </h4>
                    <div class="text-sm text-blue-700">
                        <p class="mb-2">Kategori ini saat ini digunakan oleh <strong>{{ $category->risks_count }} risiko</strong>.</p>
                        <p>Perubahan pada kategori ini akan berdampak pada:</p>
                        <ul class="list-disc pl-5 mt-1 space-y-1">
                            <li>Filter dan pencarian risiko</li>
                            <li>Laporan dan analisis risiko</li>
                            <li>Dashboard dan statistik</li>
                            <li>Manajemen dan mitigasi risiko</li>
                        </ul>
                    </div>
                </div>
                @endif
                
                <!-- Action Buttons -->
                <div class="flex justify-between mt-8">
                    <div>
                        <button type="button" onclick="resetForm()" class="btn btn-outline-secondary">
                            <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i> Reset
                        </button>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('risk-categories.show', $category->risk_category_id) }}" class="btn btn-outline-secondary w-24">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary w-32">
                            <i data-feather="save" class="w-4 h-4 mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Log Perubahan -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="history" class="w-5 h-5 mr-2"></i> Riwayat Perubahan
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="user" class="w-4 h-4 text-gray-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between">
                                <div class="font-medium">Dibuat</div>
                                <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($category->created_at)->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="text-sm text-gray-600">Kategori pertama kali dibuat</div>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="edit" class="w-4 h-4 text-gray-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between">
                                <div class="font-medium">Terakhir Diubah</div>
                                <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($category->updated_at)->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="text-sm text-gray-600">Perubahan terakhir yang disimpan</div>
                        </div>
                    </div>
                    
                    @if($category->risks_count > 0)
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="alert-triangle" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium">Risiko Terkait</div>
                            <div class="text-sm text-gray-600">
                                Terdapat {{ $category->risks_count }} risiko yang menggunakan kategori ini.
                                Perubahan akan berdampak pada semua risiko tersebut.
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-span-12 xl:col-span-4">
        <!-- Info Kategori Saat Ini -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Kategori Saat Ini
                </h2>
                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded">
                    Versi saat ini
                </span>
            </div>
            <div class="p-5">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-full {{ $getCategoryColor($category->risk_category_name) }} flex items-center justify-center mx-auto mb-3">
                        <i data-feather="{{ $getCategoryFeatherIcon($category->risk_category_name) }}" class="w-8 h-8 text-black"></i>
                    </div>
                    <h3 class="font-bold text-lg">{{ $category->risk_category_name }}</h3>
                    <div class="text-sm text-gray-500">Kode: {{ strtoupper(substr($category->risk_category_name, 0, 3)) }}</div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Deskripsi Saat Ini</div>
                        <div class="text-sm bg-gray-50 p-3 rounded-lg">
                            {{ $category->risk_category_description }}
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $category->risks_count ?? 0 }}</div>
                            <div class="text-xs text-gray-600">Total Risiko</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">
                                {{ $category->status == 'active' ? 'Aktif' : 'Non-Aktif' }}
                            </div>
                            <div class="text-xs text-gray-600">Status</div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t">
                    <a href="{{ route('risk-categories.show', $category->risk_category_id) }}" 
                       class="btn btn-outline-primary w-full">
                        <i data-feather="eye" class="w-4 h-4 mr-2"></i> Lihat Detail
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Panduan Edit -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="help-circle" class="w-5 h-5 mr-2"></i> Panduan Edit
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="check-circle" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Validasi Perubahan</h4>
                            <p class="text-sm text-gray-600">Pastikan perubahan sesuai dengan standar ISO 31000</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="users" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Dampak pada Risiko</h4>
                            <p class="text-sm text-gray-600">Perubahan akan mempengaruhi semua risiko terkait</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="file-text" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Dokumentasi</h4>
                            <p class="text-sm text-gray-600">Simpan catatan perubahan untuk audit trail</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Peringatan -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200 bg-red-50">
                <h2 class="font-medium text-base mr-auto text-red-700">
                    <i data-feather="alert-triangle" class="w-5 h-5 mr-2"></i> Peringatan
                </h2>
            </div>
            <div class="p-5">
                <div class="text-sm text-red-600">
                    <p class="mb-2"><strong>Hati-hati saat mengedit kategori:</strong></p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Perubahan nama kategori akan mempengaruhi semua risiko yang menggunakan kategori ini</li>
                        <li>Pastikan perubahan sudah diverifikasi dan disetujui</li>
                        <li>Kategori yang sudah digunakan tidak dapat dihapus</li>
                        <li>Simpan backup data sebelum perubahan besar</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/feather-icons"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Update preview based on selection
    const categorySelect = document.getElementById('risk_category_name');
    const descriptionTextarea = document.getElementById('risk_category_description');
    const statusActive = document.getElementById('status_active');
    const statusInactive = document.getElementById('status_inactive');
    
    const categoryInfo = {
        'Waktu': {
            icon: 'clock',
            color: 'bg-red-500',
            example: 'Risiko terkait jadwal proyek, keterlambatan, percepatan pekerjaan, dan timeline.'
        },
        'Lingkungan': {
            icon: 'globe',
            color: 'bg-green-500',
            example: 'Risiko dampak lingkungan, perizinan lingkungan, cuaca ekstrem, dan kondisi alam.'
        },
        'Manajemen': {
            icon: 'briefcase',
            color: 'bg-blue-500',
            example: 'Risiko manajemen proyek, koordinasi, komunikasi, dan pengambilan keputusan.'
        },
        'Hukum': {
            icon: 'scale',
            color: 'bg-purple-500',
            example: 'Risiko hukum, kontrak, regulasi, perizinan, sengketa, dan kepatuhan.'
        },
        'SDM': {
            icon: 'users',
            color: 'bg-yellow-500',
            example: 'Risiko sumber daya manusia, ketersediaan, kompetensi, produktivitas, dan turnover.'
        },
        'K3': {
            icon: 'shield',
            color: 'bg-orange-500',
            example: 'Risiko keselamatan dan kesehatan kerja, kecelakaan, insiden, dan prosedur K3.'
        }
    };
    
    categorySelect.addEventListener('change', updatePreview);
    descriptionTextarea.addEventListener('input', updatePreview);
    statusActive.addEventListener('change', updatePreview);
    statusInactive.addEventListener('change', updatePreview);
    
    function updatePreview() {
        const selectedCategory = categorySelect.value || '{{ $category->risk_category_name }}';
        const previewIcon = document.getElementById('preview-icon');
        const previewIconSvg = document.getElementById('preview-icon-svg');
        const previewName = document.getElementById('preview-name');
        const previewDescription = document.getElementById('preview-description');
        const previewStatus = document.getElementById('preview-status');
        
        if (categoryInfo[selectedCategory]) {
            const info = categoryInfo[selectedCategory];
            
            // Update icon and color
            previewIcon.className = `w-12 h-12 rounded-full ${info.color} flex items-center justify-center mr-4`;
            previewIconSvg.setAttribute('data-feather', info.icon);
            
            // Update name
            previewName.textContent = selectedCategory;
            
            // Update description
            if (descriptionTextarea.value) {
                previewDescription.textContent = descriptionTextarea.value;
            } else {
                previewDescription.textContent = info.example;
            }
        } else {
            // Keep current icon and color for existing category
            const currentColor = '{{ $getCategoryColor($category->risk_category_name) }}';
            const currentIcon = '{{ $getCategoryFeatherIcon($category->risk_category_name) }}';
            previewIcon.className = `w-12 h-12 rounded-full ${currentColor} flex items-center justify-center mr-4`;
            previewIconSvg.setAttribute('data-feather', currentIcon);
            
            // Update name
            previewName.textContent = selectedCategory || '{{ $category->risk_category_name }}';
            
            // Update description
            previewDescription.textContent = descriptionTextarea.value || '{{ $category->risk_category_description }}';
        }
        
        // Update status
        const status = statusInactive.checked ? 'Non-Aktif' : 'Aktif';
        const statusClass = statusInactive.checked ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800';
        previewStatus.textContent = status;
        previewStatus.className = `px-2 py-1 ${statusClass} text-xs rounded-full`;
        
        // Refresh feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }
    
    // Reset form function
    window.resetForm = function() {
        if (confirm('Reset semua perubahan ke nilai awal?')) {
            categorySelect.value = '{{ old('risk_category_name', $category->risk_category_name) }}';
            descriptionTextarea.value = '{{ old('risk_category_description', $category->risk_category_description) }}';
            const originalStatus = '{{ old('status', $category->status ?? 'active') }}';
            if (originalStatus === 'active') {
                statusActive.checked = true;
            } else {
                statusInactive.checked = true;
            }
            updatePreview();
        }
    };
    
    // Initial update
    updatePreview();
});
</script>
@endpush