@extends('layouts.master')

@section('title', 'Tambah Risiko - SIMR')

@section('page-title', 'Tambah Risiko Baru')

@section('page-action')
<div class="w-full sm:w-auto flex">
    <a href="{{ route('risks.index') }}" class="btn btn-outline-secondary shadow-md">
        <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
    </a>
</div>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 xl:col-span-8">
        <!-- Form Tambah Risiko -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                    <i data-feather="alert-triangle" class="w-5 h-5 text-red-600"></i>
                </div>
                <h2 class="font-medium text-base mr-auto">
                    Form Identifikasi Risiko
                </h2>
                <div class="flex items-center">
                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">
                        Auto-generated Code
                    </span>
                </div>
            </div>
            <form method="POST" action="{{ route('risks.store') }}" class="p-5" id="riskForm">
                @csrf
                
                <div class="grid grid-cols-12 gap-6">
                    <!-- Proyek -->
                    <div class="col-span-12 md:col-span-6">
                        <label for="risk_pro_id" class="form-label">
                            Proyek <span class="text-danger">*</span>
                        </label>
                        <select id="risk_pro_id" name="risk_pro_id" 
                                class="form-select w-full @error('risk_pro_id') border-danger @enderror" 
                                required>
                            <option value="">Pilih Proyek...</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->pro_id }}" 
                                        {{ old('risk_pro_id') == $project->pro_id ? 'selected' : '' }}>
                                    {{ $project->pro_nama }}
                                    @if($project->pro_status == 'Aktif')
                                        <span class="text-green-600">(Aktif)</span>
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('risk_pro_id')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Organisasi -->
                    <div class="col-span-12 md:col-span-6">
                        <label for="risk_organization_id" class="form-label">
                            Organisasi <span class="text-danger">*</span>
                        </label>
                        <select id="risk_organization_id" name="risk_organization_id" 
                                class="form-select w-full @error('risk_organization_id') border-danger @enderror" 
                                required>
                            <option value="">Pilih Organisasi...</option>
                            @foreach($organizations as $org)
                                <option value="{{ $org->organization_id }}" 
                                        {{ old('risk_organization_id') == $org->organization_id ? 'selected' : '' }}>
                                    {{ $org->organization_code }} - {{ $org->organization_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('risk_organization_id')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Tujuan Strategis -->
                    <div class="col-span-12 md:col-span-6">
                        <label for="risk_strategic_objective_id" class="form-label">
                            Tujuan Strategis <span class="text-danger">*</span>
                        </label>
                        <select id="risk_strategic_objective_id" name="risk_strategic_objective_id" 
                                class="form-select w-full @error('risk_strategic_objective_id') border-danger @enderror" 
                                required>
                            <option value="">Pilih Tujuan Strategis...</option>
                            @foreach($objectives as $objective)
                                <option value="{{ $objective->strategic_objective_id }}" 
                                        data-org="{{ $objective->strategic_objective_organization_id }}"
                                        {{ old('risk_strategic_objective_id') == $objective->strategic_objective_id ? 'selected' : '' }}>
                                    {{ $objective->strategic_objective_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('risk_strategic_objective_id')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Proses Bisnis -->
                    <div class="col-span-12 md:col-span-6">
                        <label for="risk_business_process_id" class="form-label">
                            Proses Bisnis <span class="text-danger">*</span>
                        </label>
                        <select id="risk_business_process_id" name="risk_business_process_id" 
                                class="form-select w-full @error('risk_business_process_id') border-danger @enderror" 
                                required>
                            <option value="">Pilih Proses Bisnis...</option>
                            @foreach($processes as $process)
                                <option value="{{ $process->business_process_id }}" 
                                        data-org="{{ $process->business_process_organization_id }}"
                                        {{ old('risk_business_process_id') == $process->business_process_id ? 'selected' : '' }}>
                                    {{ $process->business_process_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('risk_business_process_id')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Kategori Risiko -->
                    <div class="col-span-12 md:col-span-6">
                        <label for="risk_category_id" class="form-label">
                            Kategori Risiko <span class="text-danger">*</span>
                        </label>
                        <select id="risk_category_id" name="risk_category_id" 
                                class="form-select w-full @error('risk_category_id') border-danger @enderror" 
                                required>
                            <option value="">Pilih Kategori...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->risk_category_id }}" 
                                        {{ old('risk_category_id') == $category->risk_category_id ? 'selected' : '' }}>
                                    {{ $category->risk_category_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('risk_category_id')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- UPR / Pemilik Risiko -->
                    <div class="col-span-12 md:col-span-6">
                        <label for="risk_user_id" class="form-label">
                            UPR / Pemilik Risiko <span class="text-danger">*</span>
                        </label>
                        <select id="risk_user_id" name="risk_user_id" 
                                class="form-select w-full @error('risk_user_id') border-danger @enderror" 
                                required>
                            <option value="">Pilih UPR...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" 
                                        {{ old('risk_user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                    <span class="text-gray-500 text-xs">({{ $user->role }})</span>
                                </option>
                            @endforeach
                        </select>
                        @error('risk_user_id')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Deskripsi Risiko -->
                    <div class="col-span-12">
                        <label for="risk_description" class="form-label">
                            Deskripsi Risiko <span class="text-danger">*</span>
                        </label>
                        <textarea id="risk_description" name="risk_description" 
                                  class="form-control w-full @error('risk_description') border-danger @enderror" 
                                  rows="4"
                                  placeholder="Deskripsikan risiko secara detail..."
                                  required>{{ old('risk_description') }}</textarea>
                        <div class="text-xs text-gray-500 mt-1">
                            Jelaskan risiko dengan spesifik dan jelas. Minimal 20 karakter.
                        </div>
                        @error('risk_description')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Preview Section -->
                <div class="mt-8 p-4 bg-gray-50 rounded-lg border">
                    <h4 class="font-medium mb-3 flex items-center">
                        <i data-feather="eye" class="w-4 h-4 mr-2"></i> Preview Risiko
                    </h4>
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-6">
                            <div class="p-3 bg-white rounded border">
                                <div class="text-xs text-gray-500 mb-1">Kode Risiko</div>
                                <div id="previewCode" class="font-bold text-red-600">
                                    RISK-{{ str_pad(($lastNumber ?? 0) + 1, 3, '0', STR_PAD_LEFT) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 md:col-span-6">
                            <div class="p-3 bg-white rounded border">
                                <div class="text-xs text-gray-500 mb-1">Status</div>
                                <div class="font-medium text-yellow-600">
                                    <i data-feather="clock" class="w-4 h-4 inline mr-1"></i> Menunggu Analisis
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12">
                            <div class="p-3 bg-white rounded border">
                                <div class="text-xs text-gray-500 mb-1">Deskripsi Preview</div>
                                <div id="previewDescription" class="text-sm">
                                    {{ old('risk_description') ?: 'Deskripsi akan muncul di sini...' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="flex justify-end mt-8 pt-5 border-t border-gray-200">
                    <a href="{{ route('risks.index') }}" 
                       class="btn btn-outline-secondary w-24 mr-3">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary w-32" id="submitBtn">
                        <i data-feather="save" class="w-4 h-4 mr-2"></i> Simpan Risiko
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-span-12 xl:col-span-4">
        <!-- Panduan Identifikasi -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="help-circle" class="w-5 h-5 mr-2"></i> Panduan Identifikasi
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="alert-triangle" class="w-4 h-4 text-red-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Spesifik dan Terukur</h4>
                            <p class="text-sm text-gray-600">Deskripsikan risiko dengan jelas dan dapat diukur</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="target" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Kaitkan dengan Tujuan</h4>
                            <p class="text-sm text-gray-600">Hubungkan risiko dengan tujuan strategis organisasi</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="briefcase" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Proses yang Jelas</h4>
                            <p class="text-sm text-gray-600">Identifikasi proses bisnis tempat risiko terjadi</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="user" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Tentukan UPR</h4>
                            <p class="text-sm text-gray-600">Pilih pemilik risiko yang bertanggung jawab</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contoh Risiko -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="book-open" class="w-5 h-5 mr-2"></i> Contoh Risiko
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="text-xs text-gray-500 mb-1">Risiko Operasional</div>
                        <div class="text-sm font-medium">Keterlambatan penyelesaian konstruksi karena cuaca ekstrem</div>
                        <div class="text-xs text-gray-500 mt-1">Proyek: Pembangunan Jalan Tol</div>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="text-xs text-gray-500 mb-1">Risiko Keuangan</div>
                        <div class="text-sm font-medium">Kenaikan harga material konstruksi yang tidak terduga</div>
                        <div class="text-xs text-gray-500 mt-1">Proses: Pengadaan Barang & Jasa</div>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="text-xs text-gray-500 mb-1">Risiko Kepatuhan</div>
                        <div class="text-sm font-medium">Ketidaksesuaian dokumen tender dengan regulasi yang berlaku</div>
                        <div class="text-xs text-gray-500 mt-1">Kategori: Legal & Compliance</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informasi Penting -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="info" class="w-5 h-5 mr-2"></i> Informasi Penting
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex items-start">
                        <i data-feather="check-circle" class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span>Risiko yang sudah diidentifikasi akan otomatis mendapat kode unik</span>
                    </div>
                    <div class="flex items-start">
                        <i data-feather="check-circle" class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span>Setelah disimpan, risiko akan masuk ke tahap analisis</span>
                    </div>
                    <div class="flex items-start">
                        <i data-feather="check-circle" class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span>Pastikan semua data sudah benar sebelum disimpan</span>
                    </div>
                    <div class="flex items-start">
                        <i data-feather="check-circle" class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span>PIC yang dipilih akan menerima notifikasi</span>
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
    
    // Elements
    const orgSelect = document.getElementById('risk_organization_id');
    const objectiveSelect = document.getElementById('risk_strategic_objective_id');
    const processSelect = document.getElementById('risk_business_process_id');
    const descriptionTextarea = document.getElementById('risk_description');
    const previewDescription = document.getElementById('previewDescription');
    const previewCode = document.getElementById('previewCode');
    const form = document.getElementById('riskForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Get objective and process options data
    const objectiveOptions = {};
    const processOptions = {};
    
    @foreach($objectives as $objective)
        objectiveOptions[{{ $objective->strategic_objective_id }}] = {
            orgId: {{ $objective->strategic_objective_organization_id }},
            name: '{{ addslashes($objective->strategic_objective_name) }}'
        };
    @endforeach
    
    @foreach($processes as $process)
        processOptions[{{ $process->business_process_id }}] = {
            orgId: {{ $process->business_process_organization_id }},
            name: '{{ addslashes($process->business_process_name) }}'
        };
    @endforeach
    
    // Filter objectives and processes based on selected organization
    function filterOptions() {
        const selectedOrgId = orgSelect.value;
        
        // Filter objectives
        if (selectedOrgId) {
            // Enable/disable options based on organization
            Array.from(objectiveSelect.options).forEach(option => {
                if (option.value) {
                    const optionData = objectiveOptions[option.value];
                    if (optionData && optionData.orgId == selectedOrgId) {
                        option.disabled = false;
                        option.style.display = '';
                    } else {
                        option.disabled = true;
                        option.style.display = 'none';
                    }
                }
            });
            
            // Filter processes
            Array.from(processSelect.options).forEach(option => {
                if (option.value) {
                    const optionData = processOptions[option.value];
                    if (optionData && optionData.orgId == selectedOrgId) {
                        option.disabled = false;
                        option.style.display = '';
                    } else {
                        option.disabled = true;
                        option.style.display = 'none';
                    }
                }
            });
            
            // Reset selections if they don't belong to selected organization
            if (objectiveSelect.value) {
                const selectedObjective = objectiveOptions[objectiveSelect.value];
                if (selectedObjective && selectedObjective.orgId != selectedOrgId) {
                    objectiveSelect.value = '';
                }
            }
            
            if (processSelect.value) {
                const selectedProcess = processOptions[processSelect.value];
                if (selectedProcess && selectedProcess.orgId != selectedOrgId) {
                    processSelect.value = '';
                }
            }
        } else {
            // If no organization selected, enable all options
            Array.from(objectiveSelect.options).forEach(option => {
                option.disabled = false;
                option.style.display = '';
            });
            
            Array.from(processSelect.options).forEach(option => {
                option.disabled = false;
                option.style.display = '';
            });
        }
    }
    
    // Update preview description
    function updatePreview() {
        if (descriptionTextarea.value) {
            previewDescription.textContent = descriptionTextarea.value;
        } else {
            previewDescription.textContent = 'Deskripsi akan muncul di sini...';
        }
    }
    
    // Event listeners
    orgSelect.addEventListener('change', filterOptions);
    descriptionTextarea.addEventListener('input', updatePreview);
    
    // Form validation
    form.addEventListener('submit', function(e) {
        const description = descriptionTextarea.value;
        
        if (description.length < 20) {
            e.preventDefault();
            alert('Deskripsi risiko terlalu pendek. Minimal 20 karakter.');
            descriptionTextarea.focus();
            return false;
        }
        
        if (!orgSelect.value) {
            e.preventDefault();
            alert('Pilih organisasi terlebih dahulu.');
            orgSelect.focus();
            return false;
        }
        
        if (!objectiveSelect.value) {
            e.preventDefault();
            alert('Pilih tujuan strategis terlebih dahulu.');
            objectiveSelect.focus();
            return false;
        }
        
        if (!processSelect.value) {
            e.preventDefault();
            alert('Pilih proses bisnis terlebih dahulu.');
            processSelect.focus();
            return false;
        }
        
        // Disable submit button to prevent double submission
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-feather="loader" class="w-4 h-4 mr-2 animate-spin"></i> Menyimpan...';
        
        return true;
    });
    
    // Initial filter
    filterOptions();
    updatePreview();
    
    // Auto-select organization based on URL parameters
    function getOrganizationFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        const orgId = urlParams.get('org_id');
        if (orgId) {
            orgSelect.value = orgId;
            filterOptions();
        }
        
        const proId = urlParams.get('pro_id');
        if (proId) {
            document.getElementById('risk_pro_id').value = proId;
        }
    }
    
    getOrganizationFromUrl();
});
</script>
@endpush