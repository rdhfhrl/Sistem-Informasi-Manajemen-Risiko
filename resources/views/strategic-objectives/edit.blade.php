@extends('layouts.master')

@section('title', 'Edit Tujuan Strategis - SIMR')

@section('page-title', 'Edit Tujuan Strategis')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('strategic-objectives.index') }}">Tujuan Strategis</a></li>
    <li class="breadcrumb-item"><a href="{{ route('strategic-objectives.show', $objective->strategic_objective_id) }}">{{ $objective->strategic_objective_name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 xl:col-span-8">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-theme-1/10 flex items-center justify-center mr-4">
                        <i data-feather="target" class="w-6 h-6 text-theme-1"></i>
                    </div>
                    <div>
                        <h2 class="font-medium text-base mr-auto">
                            Edit Tujuan Strategis: {{ $objective->strategic_objective_name }}
                        </h2>
                        <div class="text-gray-500 text-sm">
                            ID: {{ $objective->strategic_objective_id }}
                        </div>
                    </div>
                </div>
                <div class="ml-auto flex items-center space-x-2">
                    <a href="{{ route('strategic-objectives.show', $objective->strategic_objective_id) }}" 
                       class="btn btn-outline-primary">
                        <i data-feather="eye" class="w-4 h-4 mr-2"></i> Detail
                    </a>
                    <a href="{{ route('strategic-objectives.index') }}" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
                    </a>
                </div>
            </div>
            <form method="POST" action="{{ route('strategic-objectives.update', $objective->strategic_objective_id) }}" class="p-5">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Current Information -->
                    <div class="p-4 bg-blue-50 rounded-lg mb-4">
                        <h4 class="font-medium text-blue-800 mb-2">Informasi Saat Ini</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm text-blue-600">Nama Tujuan</div>
                                <div class="font-medium">{{ $objective->strategic_objective_name }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-blue-600">UPTD</div>
                                <div class="font-medium">
                                    @if($objective->organization)
                                        {{ $objective->organization->organization_code }} - {{ $objective->organization->location }}
                                    @else
                                        <span class="text-red-600">UPTD tidak ditemukan</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="text-sm text-blue-600">Risiko Terkait</div>
                                <div class="font-medium">{{ $objective->risks_count ?? 0 }} risiko</div>
                            </div>
                            <div>
                                <div class="text-sm text-blue-600">Dibuat</div>
                                <div class="font-medium">
                                    {{ \Carbon\Carbon::parse($objective->created_at)->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- UPTD Selection -->
                    <div>
                        <label for="strategic_objective_organization_id" class="form-label">
                            Pilih UPTD <span class="text-danger">*</span>
                        </label>
                        <select id="strategic_objective_organization_id" name="strategic_objective_organization_id" 
                                class="form-select w-full" required>
                            <option value="">Pilih UPTD...</option>
                            @foreach($organizations as $org)
                                <option value="{{ $org->organization_id }}" 
                                        {{ old('strategic_objective_organization_id', $objective->strategic_objective_organization_id) == $org->organization_id ? 'selected' : '' }}>
                                    {{ $org->organization_code }} - {{ $org->location }}
                                </option>
                            @endforeach
                        </select>
                        <div class="text-xs text-gray-500 mt-1">
                            * Pilih UPTD yang akan memiliki tujuan strategis ini
                        </div>
                        @error('strategic_objective_organization_id')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Objective Name -->
                    <div>
                        <label for="strategic_objective_name" class="form-label">
                            Nama Tujuan Strategis <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="strategic_objective_name" name="strategic_objective_name" 
                               class="form-control w-full" 
                               placeholder="Contoh: Meningkatkan kualitas infrastruktur jalan, dll."
                               value="{{ old('strategic_objective_name', $objective->strategic_objective_name) }}"
                               required>
                        <div class="text-xs text-gray-500 mt-1">
                            * Nama tujuan strategis harus jelas dan terukur
                        </div>
                        @error('strategic_objective_name')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Data Terkait Warning -->
                @if(($objective->risks_count ?? 0) > 0)
                <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                    <h4 class="font-bold text-yellow-800 mb-2 flex items-center">
                        <i data-feather="alert-triangle" class="w-5 h-5 mr-2"></i> Peringatan:
                    </h4>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• Tujuan strategis ini memiliki {{ $objective->risks_count }} data risiko terkait</li>
                        <li>• Perubahan data dapat mempengaruhi analisis risiko</li>
                        <li>• Pastikan perubahan sesuai dengan kebutuhan manajemen risiko</li>
                    </ul>
                </div>
                @endif
                
                <!-- Preview -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium mb-3">Preview Perubahan</h4>
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-theme-1/10 flex items-center justify-center mr-4">
                            <i data-feather="target" class="w-6 h-6 text-theme-1"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-lg" id="preview-name">{{ $objective->strategic_objective_name }}</div>
                            <div class="text-gray-600 text-sm" id="preview-org">
                                @if($objective->organization)
                                    UPTD: {{ $objective->organization->organization_code }} - {{ $objective->organization->location }}
                                @else
                                    UPTD: Akan dipilih...
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Informasi Penting -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <h4 class="font-bold text-blue-800 mb-2 flex items-center">
                        <i data-feather="info" class="w-5 h-5 mr-2"></i> Informasi Penting:
                    </h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Pastikan perubahan sesuai dengan strategi organisasi</li>
                        <li>• Perubahan UPTD akan memindahkan semua risiko terkait</li>
                        <li>• Update laporan jika diperlukan setelah perubahan</li>
                        <li>• Verifikasi dengan stakeholder terkait</li>
                    </ul>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end mt-8">
                    <a href="{{ route('strategic-objectives.show', $objective->strategic_objective_id) }}" 
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
        <!-- Statistik Tujuan -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Statistik Tujuan
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-800">{{ $objective->risks_count ?? 0 }}</div>
                        <div class="text-sm text-blue-600">Total Risiko Terkait</div>
                    </div>
                    
                    @if($objective->organization)
                    <div class="p-3 bg-green-50 rounded-lg">
                        <h4 class="font-medium text-green-800 mb-2">Informasi UPTD</h4>
                        <div class="text-sm">
                            <div class="flex items-center mb-1">
                                <i data-feather="hash" class="w-4 h-4 text-green-600 mr-2"></i>
                                <span>{{ $objective->organization->organization_code }}</span>
                            </div>
                            <div class="flex items-center mb-1">
                                <i data-feather="map-pin" class="w-4 h-4 text-green-600 mr-2"></i>
                                <span>{{ $objective->organization->location }}</span>
                            </div>
                            <div class="flex items-center">
                                <i data-feather="target" class="w-4 h-4 text-green-600 mr-2"></i>
                                <span>{{ $objective->organization->strategicObjectives->count() }} tujuan strategis</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                @if(($objective->risks_count ?? 0) > 0)
                <div class="mt-6">
                    <h4 class="font-medium mb-2">Saran Perubahan:</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Review risiko yang terkait sebelum mengubah UPTD</li>
                        <li>• Informasikan perubahan kepada analis risiko</li>
                        <li>• Update dashboard monitoring risiko</li>
                        <li>• Verifikasi konsistensi dengan tujuan UPTD lainnya</li>
                    </ul>
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
                    <a href="{{ route('strategic-objectives.show', $objective->strategic_objective_id) }}" 
                       class="flex items-center p-3 bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                        <div class="w-8 h-8 rounded-full bg-theme-1 flex items-center justify-center mr-3">
                            <i data-feather="eye" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <div class="font-medium">Lihat Detail</div>
                            <div class="text-xs text-gray-600">Kembali ke halaman detail</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('risks.create', ['strategic_objective' => $objective->strategic_objective_id]) }}" 
                       class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center mr-3">
                            <i data-feather="plus" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <div class="font-medium">Tambah Risiko</div>
                            <div class="text-xs text-gray-600">Tambah risiko untuk tujuan ini</div>
                        </div>
                    </a>
                    
                    <form method="POST" action="{{ route('strategic-objectives.destroy', $objective->strategic_objective_id) }}" 
                          onsubmit="return confirmDelete()" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full flex items-center p-3 bg-red-50 hover:bg-red-100 rounded-lg transition-colors text-red-700">
                            <div class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center mr-3">
                                <i data-feather="trash-2" class="w-4 h-4 text-white"></i>
                            </div>
                            <div>
                                <div class="font-medium">Hapus Tujuan</div>
                                <div class="text-xs text-gray-600">Hanya jika tidak ada risiko terkait</div>
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
                            <p class="text-sm text-gray-600">Perubahan dapat mempengaruhi analisis risiko</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="refresh-cw" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Konsistensi</h4>
                            <p class="text-sm text-gray-600">Pastikan konsisten dengan tujuan UPTD lainnya</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="check-square" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Validasi</h4>
                            <p class="text-sm text-gray-600">Verifikasi dengan stakeholder terkait</p>
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
    const orgSelect = document.getElementById('strategic_objective_organization_id');
    const nameInput = document.getElementById('strategic_objective_name');
    
    const previewName = document.getElementById('preview-name');
    const previewOrg = document.getElementById('preview-org');
    
    // Data organizations untuk preview
    const orgData = {
        @foreach($organizations as $org)
            '{{ $org->organization_id }}': {
                code: '{{ $org->organization_code }}',
                location: '{{ $org->location }}'
            },
        @endforeach
    };
    
    function updatePreview() {
        // Update objective name
        if (nameInput.value) {
            previewName.textContent = nameInput.value;
        } else {
            previewName.textContent = '{{ $objective->strategic_objective_name }}';
        }
        
        // Update organization
        if (orgSelect.value && orgData[orgSelect.value]) {
            const org = orgData[orgSelect.value];
            previewOrg.textContent = `UPTD: ${org.code} - ${org.location}`;
        } else {
            previewOrg.textContent = 'UPTD: Akan dipilih...';
        }
    }
    
    orgSelect.addEventListener('change', updatePreview);
    nameInput.addEventListener('input', updatePreview);
    
    // Initial update
    updatePreview();
    
    // Confirm delete
    function confirmDelete() {
        return confirm('Apakah Anda yakin ingin menghapus tujuan strategis ini?\n\nPerhatian: Penghapusan tidak dapat dibatalkan dan hanya diperbolehkan jika tidak ada risiko terkait.');
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