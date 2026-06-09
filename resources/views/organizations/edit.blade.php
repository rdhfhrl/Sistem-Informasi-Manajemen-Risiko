@extends('layouts.master')

@section('title', 'Edit UPTD - SIMR')

@section('page-title', 'Edit Data UPTD')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('organizations.index') }}">Organisasi</a></li>
    <li class="breadcrumb-item"><a href="{{ route('organizations.show', $organization->organization_id) }}">{{ $organization->organization_code }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 xl:col-span-8">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-theme-1/10 flex items-center justify-center mr-4">
                        <i data-feather="building" class="w-6 h-6 text-theme-1"></i>
                    </div>
                    <div>
                        <h2 class="font-medium text-base mr-auto">
                            Edit Data UPTD: {{ $organization->organization_code }}
                        </h2>
                        <div class="text-gray-500 text-sm">
                            {{ $organization->organization_name }} - {{ $organization->location }}
                        </div>
                    </div>
                </div>
                <div class="ml-auto flex items-center space-x-2">
                    <a href="{{ route('organizations.show', $organization->organization_id) }}" 
                       class="btn btn-outline-primary">
                        <i data-feather="eye" class="w-4 h-4 mr-2"></i> Detail
                    </a>
                    <a href="{{ route('organizations.index') }}" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
                    </a>
                </div>
            </div>
            <form method="POST" action="{{ route('organizations.update', $organization->organization_id) }}" class="p-5">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Parent Organization (Fixed) -->
                    <div class="p-4 bg-blue-50 rounded-lg mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i data-feather="home" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-blue-800">Organisasi Induk</h4>
                                <p class="text-sm text-blue-600">UPTD berada di bawah organisasi ini</p>
                            </div>
                        </div>
                        <div class="mt-3 ml-13">
                            @if($organization->parent)
                                <div class="font-bold">{{ $organization->parent->organization_name }}</div>
                                <div class="text-sm text-gray-600">{{ $organization->parent->organization_code }}</div>
                            @else
                                <div class="font-bold text-red-600">Dinas PUPR tidak ditemukan</div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Current Information -->
                    <div class="p-4 bg-green-50 rounded-lg mb-4">
                        <h4 class="font-medium text-green-800 mb-2">Informasi Saat Ini</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm text-green-600">Kode UPTD</div>
                                <div class="font-medium">{{ $organization->organization_code }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-green-600">Lokasi</div>
                                <div class="font-medium">{{ $organization->location }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-green-600">Status</div>
                                <div class="font-medium">
                                    @if($organization->is_active)
                                        <span class="text-green-600">Aktif</span>
                                    @else
                                        <span class="text-red-600">Non-Aktif</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="text-sm text-green-600">Dibuat</div>
                                <div class="font-medium">
                                    {{ \Carbon\Carbon::parse($organization->created_at)->format('d/m/Y') }}
                                </div>
                            </div>
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
                               value="{{ old('location', $organization->location) }}"
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
                               value="{{ old('organization_code', $organization->organization_code) }}"
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
                            {{ old('organization_description', $organization->organization_description) }}
                        </textarea>
                        <div class="text-xs text-gray-500 mt-1">
                            * Deskripsi tentang wilayah kerja, tugas pokok, dan fungsi UPTD
                        </div>
                        @error('organization_description')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label for="is_active" class="form-label">
                            Status UPTD
                        </label>
                        <div class="mt-2">
                            <div class="form-check form-switch">
                                <input type="checkbox" id="is_active" name="is_active" 
                                       class="form-check-input" value="1"
                                       {{ old('is_active', $organization->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <span class="text-sm">Aktifkan UPTD</span>
                                    <div class="text-xs text-gray-500">
                                        Non-aktifkan jika UPTD sudah tidak beroperasi
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Preview -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium mb-3">Preview Perubahan</h4>
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-theme-1/10 flex items-center justify-center mr-4">
                            <i data-feather="building" class="w-6 h-6 text-theme-1"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-lg">UPTD PUPR Medan</div>
                            <div class="text-gray-600 text-sm">
                                <span id="preview-location">{{ $organization->location }}</span> • 
                                <span id="preview-code">{{ $organization->organization_code }}</span>
                            </div>
                            <div class="text-gray-500 text-sm mt-1" id="preview-description">
                                {{ $organization->organization_description ?: 'Tidak ada deskripsi' }}
                            </div>
                            <div class="mt-2">
                                <span id="preview-status" class="px-2 py-1 rounded-full text-xs font-medium 
                                    {{ $organization->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $organization->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Data Terkait Warning -->
                @php
                    $hasRelatedData = $organization->risks_count > 0 || 
                                     $organization->strategicObjectives_count > 0 ||
                                     $organization->businessProcesses_count > 0;
                @endphp
                
                @if($hasRelatedData)
                <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                    <h4 class="font-bold text-yellow-800 mb-2 flex items-center">
                        <i data-feather="alert-triangle" class="w-5 h-5 mr-2"></i> Peringatan:
                    </h4>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        @if($organization->risks_count > 0)
                        <li>• UPTD ini memiliki {{ $organization->risks_count }} data risiko terkait</li>
                        @endif
                        @if($organization->strategicObjectives_count > 0)
                        <li>• UPTD ini memiliki {{ $organization->strategicObjectives_count }} tujuan strategis</li>
                        @endif
                        @if($organization->businessProcesses_count > 0)
                        <li>• UPTD ini memiliki {{ $organization->businessProcesses_count }} proses bisnis</li>
                        @endif
                        <li>• Perubahan data dapat mempengaruhi laporan dan analisis</li>
                    </ul>
                </div>
                @endif
                
                <!-- Informasi -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <h4 class="font-bold text-blue-800 mb-2 flex items-center">
                        <i data-feather="info" class="w-5 h-5 mr-2"></i> Informasi Penting:
                    </h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Pastikan perubahan data sesuai dengan struktur organisasi</li>
                        <li>• Kode UPTD harus tetap unik setelah diubah</li>
                        <li>• Non-aktifkan UPTD jika sudah tidak beroperasi</li>
                        <li>• Perubahan akan diterapkan pada semua data terkait</li>
                    </ul>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end mt-8">
                    <a href="{{ route('organizations.show', $organization->organization_id) }}" 
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
        <!-- Statistik UPTD -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Statistik UPTD
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-800">{{ $organization->risks_count ?? 0 }}</div>
                        <div class="text-sm text-blue-600">Total Risiko</div>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-800">{{ $organization->strategicObjectives_count ?? 0 }}</div>
                        <div class="text-sm text-green-600">Tujuan Strategis</div>
                    </div>
                    <div class="text-center p-3 bg-purple-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-800">{{ $organization->businessProcesses_count ?? 0 }}</div>
                        <div class="text-sm text-purple-600">Proses Bisnis</div>
                    </div>
                    <div class="text-center p-3 bg-yellow-50 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-800">{{ $organization->audits_count ?? 0 }}</div>
                        <div class="text-sm text-yellow-600">Audit</div>
                    </div>
                </div>
                
                @if($organization->risks_count > 0)
                <div class="mt-6">
                    <h4 class="font-medium mb-2">Distribusi Level Risiko:</h4>
                    @php
                        // Simulasi distribusi risiko (dalam aplikasi nyata, ini dari database)
                        $riskLevels = [
                            'Tinggi' => ['count' => rand(0, $organization->risks_count), 'color' => 'bg-red-500'],
                            'Sedang' => ['count' => rand(0, $organization->risks_count), 'color' => 'bg-yellow-500'],
                            'Rendah' => ['count' => rand(0, $organization->risks_count), 'color' => 'bg-green-500'],
                        ];
                        $totalRisks = array_sum(array_column($riskLevels, 'count'));
                    @endphp
                    
                    <div class="space-y-2">
                        @foreach($riskLevels as $level => $data)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>{{ $level }}</span>
                                <span>{{ $data['count'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $data['color'] }} h-2 rounded-full" 
                                     style="width: {{ $totalRisks > 0 ? ($data['count'] / $totalRisks * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Aksi Cepat -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="zap" class="w-5 h-5 mr-2 inline"></i> Aksi Cepat
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    <a href="{{ route('risks.create', ['organization' => $organization->organization_id]) }}" 
                       class="flex items-center p-3 bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                        <div class="w-8 h-8 rounded-full bg-theme-1 flex items-center justify-center mr-3">
                            <i data-feather="plus" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <div class="font-medium">Tambah Risiko</div>
                            <div class="text-xs text-gray-600">Tambah risiko untuk UPTD ini</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('strategic-objectives.create', ['organization' => $organization->organization_id]) }}" 
                       class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center mr-3">
                            <i data-feather="target" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <div class="font-medium">Tujuan Strategis</div>
                            <div class="text-xs text-gray-600">Atur tujuan strategis UPTD</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('organizations.show', $organization->organization_id) }}" 
                       class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center mr-3">
                            <i data-feather="eye" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <div class="font-medium">Lihat Detail</div>
                            <div class="text-xs text-gray-600">Kembali ke halaman detail</div>
                        </div>
                    </a>
                    
                    <form method="POST" action="{{ route('organizations.destroy', $organization->organization_id) }}" 
                          onsubmit="return confirmDelete()" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full flex items-center p-3 bg-red-50 hover:bg-red-100 rounded-lg transition-colors text-red-700">
                            <div class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center mr-3">
                                <i data-feather="trash-2" class="w-4 h-4 text-white"></i>
                            </div>
                            <div>
                                <div class="font-medium">Hapus UPTD</div>
                                <div class="text-xs text-gray-600">Hanya jika tidak ada data terkait</div>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Panduan Edit -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="help-circle" class="w-5 h-5 mr-2 inline"></i> Panduan Edit
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="alert-triangle" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Hati-hati</h4>
                            <p class="text-sm text-gray-600">Perubahan kode UPTD dapat mempengaruhi data terkait</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="refresh-cw" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Konsistensi</h4>
                            <p class="text-sm text-gray-600">Pastikan format kode konsisten dengan UPTD lain</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="toggle-right" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Status</h4>
                            <p class="text-sm text-gray-600">Non-aktifkan UPTD yang sudah tidak beroperasi</p>
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
    
    // Update preview on input change
    const locationInput = document.getElementById('location');
    const codeInput = document.getElementById('organization_code');
    const descInput = document.getElementById('organization_description');
    const statusCheckbox = document.getElementById('is_active');
    
    const previewLocation = document.getElementById('preview-location');
    const previewCode = document.getElementById('preview-code');
    const previewDesc = document.getElementById('preview-description');
    const previewStatus = document.getElementById('preview-status');
    
    function updatePreview() {
        // Update location
        if (locationInput.value) {
            previewLocation.textContent = locationInput.value;
        } else {
            previewLocation.textContent = '{{ $organization->location }}';
        }
        
        // Update code
        if (codeInput.value) {
            previewCode.textContent = codeInput.value;
        } else {
            previewCode.textContent = '{{ $organization->organization_code }}';
        }
        
        // Update description
        if (descInput.value) {
            previewDesc.textContent = descInput.value;
        } else {
            previewDesc.textContent = '{{ $organization->organization_description ?: "Tidak ada deskripsi" }}';
        }
        
        // Update status
        if (statusCheckbox.checked) {
            previewStatus.textContent = 'Aktif';
            previewStatus.className = 'px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
        } else {
            previewStatus.textContent = 'Non-Aktif';
            previewStatus.className = 'px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800';
        }
    }
    
    // Event listeners
    locationInput.addEventListener('input', updatePreview);
    codeInput.addEventListener('input', updatePreview);
    descInput.addEventListener('input', updatePreview);
    statusCheckbox.addEventListener('change', updatePreview);
    
    // Auto-generate code suggestion
    locationInput.addEventListener('blur', function() {
        if (locationInput.value && locationInput.value !== '{{ $organization->location }}' && !codeInput.value) {
            const location = locationInput.value.toUpperCase().replace(/\s+/g, '-');
            const suggestedCode = `UPTD-${location}-001`;
            codeInput.value = suggestedCode;
            updatePreview();
        }
    });
    
    // Confirm delete
    function confirmDelete() {
        return confirm('Apakah Anda yakin ingin menghapus UPTD ini?\n\nPerhatian: Penghapusan tidak dapat dibatalkan dan dapat mempengaruhi data terkait.');
    }
    
    // Auto-hide alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) closeBtn.click();
        });
    }, 5000);
});
</script>
@endpush