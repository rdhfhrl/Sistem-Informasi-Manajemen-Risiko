@extends('layouts.master')


@section('title', 'Tambah Kategori Risiko - SIMR')

@section('page-title', 'Tambah Kategori Risiko Baru')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('risk-categories.index') }}">Kategori Risiko</a></li>
<li class="breadcrumb-item active">Tambah Baru</li>
@endsection

@section('content')

@php
    if (!function_exists('getCategoryColor')) {
        function getCategoryColor($categoryName) {
            $colors = [
                'Waktu' => 'bg-red-500',
                'Lingkungan' => 'bg-green-500',
                'Manajemen' => 'bg-blue-500',
                'Hukum' => 'bg-purple-500',
                'SDM' => 'bg-yellow-500',
                'K3' => 'bg-orange-500',
            ];
            return $colors[$categoryName] ?? 'bg-gray-500';
        }
    }

    if (!function_exists('getCategoryIcon')) {
        function getCategoryIcon($categoryName) {
            $icons = [
                'Waktu' => 'schedule',
                'Lingkungan' => 'public',
                'Manajemen' => 'work',
                'Hukum' => 'gavel',
                'SDM' => 'group',
                'K3' => 'security',
            ];
            return $icons[$categoryName] ?? 'folder';
        }
    }
@endphp

<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 xl:col-span-8">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Form Tambah Kategori Risiko
                </h2>
                <a href="{{ route('risk-categories.index') }}" class="btn btn-outline-secondary">
                    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
                </a>
            </div>
            <form method="POST" action="{{ route('risk-categories.store') }}" class="p-5">
                @csrf
                
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
                                        {{ old('risk_category_name') == $option ? 'selected' : '' }}>
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
                                  rows="4" 
                                  placeholder="Jelaskan secara detail tentang kategori risiko ini..."
                                  required>{{ old('risk_category_description') }}</textarea>
                        <div class="text-xs text-gray-500 mt-1">
                            * Deskripsi harus jelas dan membantu dalam identifikasi risiko
                        </div>
                        @error('risk_category_description')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Preview Kategori -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium mb-3">Preview Kategori</h4>
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center mr-4" 
                             id="preview-icon">
                            <i data-feather="folder" class="w-6 h-6 text-gray-600" id="preview-icon-svg"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-lg" id="preview-name">Nama Kategori</div>
                            <div class="text-gray-600 text-sm" id="preview-description">Deskripsi akan muncul di sini</div>
                        </div>
                    </div>
                </div>
                
                <!-- Informasi Kategori -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <h4 class="font-bold text-blue-800 mb-2 flex items-center">
                        <i data-feather="info" class="w-5 h-5 mr-2"></i> Informasi Penting:
                    </h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Kategori risiko digunakan untuk mengelompokkan dan menganalisis risiko</li>
                        <li>• Setiap risiko harus dikategorikan untuk memudahkan manajemen</li>
                        <li>• Kategori yang sudah digunakan tidak dapat dihapus</li>
                        <li>• Pastikan deskripsi jelas untuk membantu identifikasi risiko</li>
                    </ul>
                </div>
                
                <!-- Contoh Kategori -->
                <div class="mt-6 p-4 bg-green-50 rounded-lg border border-green-100">
                    <h4 class="font-bold text-green-800 mb-2 flex items-center">
                        <i data-feather="book-open" class="w-5 h-5 mr-2"></i> Contoh Kategori Risiko Konstruksi:
                    </h4>
                    <div class="text-sm text-green-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <strong>Waktu:</strong> Risiko terkait jadwal, keterlambatan, percepatan pekerjaan
                            </div>
                            <div>
                                <strong>Lingkungan:</strong> Dampak lingkungan, perizinan, cuaca ekstrem
                            </div>
                            <div>
                                <strong>Hukum:</strong> Kontrak, regulasi, perizinan, sengketa
                            </div>
                            <div>
                                <strong>SDM:</strong> Ketersediaan tenaga kerja, kompetensi, produktivitas
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end mt-8">
                    <a href="{{ route('risk-categories.index') }}" class="btn btn-outline-secondary w-24 mr-3">
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
        <!-- Daftar Kategori yang Sudah Ada -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Kategori yang Sudah Ada
                </h2>
                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded">
                    {{ $existingCategories->count() }} kategori
                </span>
            </div>
            <div class="p-5">
                @if($existingCategories->count() > 0)
                    <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                        @foreach($existingCategories as $category)
                            <div class="p-3 border border-gray-200 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full {{ getCategoryColor($category->risk_category_name) }} flex items-center justify-center mr-3">
                                        <i data-feather="{{ getCategoryIcon($category->risk_category_name) }}" class="w-4 h-4 text-white"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $category->risk_category_name }}</div>
                                        <div class="text-xs text-gray-500 truncate">
                                            {{ Str::limit($category->risk_category_description, 40) }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-medium text-sm">{{ $category->risks_count ?? 0 }}</div>
                                        <div class="text-xs text-gray-500">risiko</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i data-feather="folder" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                        <p class="text-gray-500">Belum ada kategori risiko</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Panduan Kategori -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="help-circle" class="w-5 h-5 mr-2 inline"></i> Panduan Kategori
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="target" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Kategori Standar</h4>
                            <p class="text-sm text-gray-600">Gunakan kategori standar ISO 31000 untuk konsistensi</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="file-text" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Deskripsi Jelas</h4>
                            <p class="text-sm text-gray-600">Deskripsi harus membantu identifikasi risiko</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="bar-chart-2" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Analisis Risiko</h4>
                            <p class="text-sm text-gray-600">Kategori memungkinkan analisis risiko per kelompok</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Warna & Ikon Kategori -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="palette" class="w-5 h-5 mr-2 inline"></i> Warna & Ikon Kategori
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-3">
                    @foreach($categoryOptions as $option)
                        <div class="flex items-center p-2 bg-gray-50 rounded">
                            <div class="w-6 h-6 rounded-full {{ getCategoryColor($option) }} flex items-center justify-center mr-2">
                                <i data-feather="{{ getCategoryIcon($option) }}" class="w-3 h-3 text-white"></i>
                            </div>
                            <span class="text-sm">{{ $option }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-3">
                    Setiap kategori memiliki warna dan ikon unik untuk identifikasi visual
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update preview based on selection
    const categorySelect = document.getElementById('risk_category_name');
    const descriptionTextarea = document.getElementById('risk_category_description');
    
    const categoryInfo = {
        'Waktu': {
            icon: 'schedule',
            color: 'bg-red-500',
            example: 'Risiko terkait jadwal proyek, keterlambatan, percepatan pekerjaan, dan timeline.'
        },
        'Lingkungan': {
            icon: 'public',
            color: 'bg-green-500',
            example: 'Risiko dampak lingkungan, perizinan lingkungan, cuaca ekstrem, dan kondisi alam.'
        },
        'Manajemen': {
            icon: 'work',
            color: 'bg-blue-500',
            example: 'Risiko manajemen proyek, koordinasi, komunikasi, dan pengambilan keputusan.'
        },
        'Hukum': {
            icon: 'gavel',
            color: 'bg-purple-500',
            example: 'Risiko hukum, kontrak, regulasi, perizinan, sengketa, dan kepatuhan.'
        },
        'SDM': {
            icon: 'group',
            color: 'bg-yellow-500',
            example: 'Risiko sumber daya manusia, ketersediaan, kompetensi, produktivitas, dan turnover.'
        },
        'K3': {
            icon: 'security',
            color: 'bg-orange-500',
            example: 'Risiko keselamatan dan kesehatan kerja, kecelakaan, insiden, dan prosedur K3.'
        }
    };
    
    categorySelect.addEventListener('change', updatePreview);
    descriptionTextarea.addEventListener('input', updatePreview);
    
    function updatePreview() {
        const selectedCategory = categorySelect.value;
        const previewIcon = document.getElementById('preview-icon');
        const previewIconSvg = document.getElementById('preview-icon-svg');
        const previewName = document.getElementById('preview-name');
        const previewDescription = document.getElementById('preview-description');
        
        if (selectedCategory && categoryInfo[selectedCategory]) {
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
                if (!descriptionTextarea.value) {
                    descriptionTextarea.value = info.example;
                }
            }
        } else {
            // Reset preview
            previewIcon.className = 'w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center mr-4';
            previewIconSvg.setAttribute('data-feather', 'folder');
            previewName.textContent = 'Nama Kategori';
            previewDescription.textContent = 'Deskripsi akan muncul di sini';
        }
        
        feather.replace();
    }
    
    // Initial update
    updatePreview();
});
</script>
@endpush