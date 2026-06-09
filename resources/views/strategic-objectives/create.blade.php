@extends('layouts.master')

@section('title', 'Tambah Tujuan Strategis - SIMR')

@section('page-title', 'Tambah Tujuan Strategis Baru')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('strategic-objectives.index') }}">Tujuan Strategis</a></li>
    <li class="breadcrumb-item active">Tambah Baru</li>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 xl:col-span-8">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Form Tambah Tujuan Strategis
                </h2>
                <a href="{{ route('strategic-objectives.index') }}" class="btn btn-outline-secondary">
                    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
                </a>
            </div>
            <form method="POST" action="{{ route('strategic-objectives.store') }}" class="p-5">
                @csrf
                
                <div class="grid grid-cols-1 gap-6">
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
                                        {{ old('strategic_objective_organization_id') == $org->organization_id ? 'selected' : '' }}>
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
                               value="{{ old('strategic_objective_name') }}"
                               required>
                        <div class="text-xs text-gray-500 mt-1">
                            * Nama tujuan strategis harus jelas dan terukur
                        </div>
                        @error('strategic_objective_name')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Preview -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium mb-3">Preview Tujuan Strategis</h4>
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-theme-1/10 flex items-center justify-center mr-4">
                            <i data-feather="target" class="w-6 h-6 text-theme-1"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-lg" id="preview-name">Nama Tujuan Strategis</div>
                            <div class="text-gray-600 text-sm" id="preview-org">UPTD: Akan dipilih...</div>
                        </div>
                    </div>
                </div>
                
                <!-- Informasi Penting -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <h4 class="font-bold text-blue-800 mb-2 flex items-center">
                        <i data-feather="info" class="w-5 h-5 mr-2"></i> Informasi Penting:
                    </h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Tujuan strategis mengarahkan manajemen risiko di setiap UPTD</li>
                        <li>• Setiap UPTD dapat memiliki beberapa tujuan strategis</li>
                        <li>• Tujuan strategis akan dikaitkan dengan identifikasi risiko</li>
                        <li>• Pastikan nama tujuan jelas, terukur, dan relevan dengan UPTD</li>
                    </ul>
                </div>
                
                <!-- Contoh Tujuan Strategis -->
                <div class="mt-6 p-4 bg-green-50 rounded-lg border border-green-100">
                    <h4 class="font-bold text-green-800 mb-2 flex items-center">
                        <i data-feather="book-open" class="w-5 h-5 mr-2"></i> Contoh Tujuan Strategis PUPR:
                    </h4>
                    <div class="text-sm text-green-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <strong>Infrastruktur:</strong><br>
                                Meningkatkan kualitas dan kapasitas infrastruktur jalan
                            </div>
                            <div>
                                <strong>Pengelolaan Air:</strong><br>
                                Optimalisasi pengelolaan sumber daya air
                            </div>
                            <div>
                                <strong>Penataan Ruang:</strong><br>
                                Mewujudkan penataan ruang yang berkelanjutan
                            </div>
                            <div>
                                <strong>Konstruksi:</strong><br>
                                Meningkatkan kualitas konstruksi dan K3
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end mt-8">
                    <a href="{{ route('strategic-objectives.index') }}" class="btn btn-outline-secondary w-24 mr-3">
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
        <!-- UPTD yang Sudah Memiliki Tujuan -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    UPTD dengan Tujuan Strategis
                </h2>
                @php
                    $orgsWithObjectives = $organizations->filter(function($org) {
                        return $org->strategicObjectives->count() > 0;
                    });
                @endphp
                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded">
                    {{ $orgsWithObjectives->count() }} UPTD
                </span>
            </div>
            <div class="p-5">
                @if($orgsWithObjectives->count() > 0)
                    <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                        @foreach($orgsWithObjectives as $org)
                            <div class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                        <i data-feather="building" class="w-4 h-4 text-blue-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $org->organization_code }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $org->location }} • {{ $org->strategicObjectives->count() }} tujuan
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-medium text-sm">{{ $org->strategicObjectives->count() }}</div>
                                        <div class="text-xs text-gray-500">tujuan</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i data-feather="building" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                        <p class="text-gray-500">Belum ada UPTD dengan tujuan strategis</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Panduan -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="help-circle" class="w-5 h-5 mr-2 inline"></i> Panduan Tujuan Strategis
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="target" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Spesifik & Terukur</h4>
                            <p class="text-sm text-gray-600">Tujuan harus jelas dan dapat diukur pencapaiannya</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="clipboard" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Relevan dengan UPTD</h4>
                            <p class="text-sm text-gray-600">Sesuaikan dengan tugas pokok dan fungsi UPTD</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="clock" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Jangka Waktu</h4>
                            <p class="text-sm text-gray-600">Tetapkan periode pencapaian yang realistis</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Prinsip SMART -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="star" class="w-5 h-5 mr-2 inline"></i> Prinsip SMART
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    <div class="flex items-center text-sm">
                        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center mr-2 flex-shrink-0">
                            <span class="text-xs font-bold text-green-700">S</span>
                        </div>
                        <span><strong>Specific</strong> - Spesifik dan jelas</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center mr-2 flex-shrink-0">
                            <span class="text-xs font-bold text-green-700">M</span>
                        </div>
                        <span><strong>Measurable</strong> - Dapat diukur</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center mr-2 flex-shrink-0">
                            <span class="text-xs font-bold text-green-700">A</span>
                        </div>
                        <span><strong>Achievable</strong> - Dapat dicapai</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center mr-2 flex-shrink-0">
                            <span class="text-xs font-bold text-green-700">R</span>
                        </div>
                        <span><strong>Relevant</strong> - Relevan dengan organisasi</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center mr-2 flex-shrink-0">
                            <span class="text-xs font-bold text-green-700">T</span>
                        </div>
                        <span><strong>Time-bound</strong> - Terikat waktu</span>
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
            previewName.textContent = 'Nama Tujuan Strategis';
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
});
</script>
@endpush