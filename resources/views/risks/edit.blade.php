@extends('layouts.master')

@section('title', 'Edit Risiko - SIMR')

@section('page-title', 'Edit Risiko: ' . $risk->risk_code)

@section('page-action')
<div class="w-full sm:w-auto flex">
    @if($risk && $risk->risk_id)
        <a href="{{ route('risks.show', ['risk' => $risk->risk_id]) }}" class="btn btn-outline-secondary shadow-md mr-2">
            <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Batal
        </a>
    @endif
</div>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 xl:col-span-8">
        <!-- Form Edit Risiko -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                    <i data-feather="edit-2" class="w-5 h-5 text-red-600"></i>
                </div>
                <h2 class="font-medium text-base mr-auto">
                    Edit Risiko
                    <span class="text-gray-500 text-sm ml-2">
                        {{ $risk->risk_code }}
                    </span>
                </h2>
                <div class="flex items-center">
                    @php
                        $levelColors = [
                            'sangat_rendah' => 'bg-green-100 text-green-800',
                            'rendah' => 'bg-yellow-100 text-yellow-800',
                            'sedang' => 'bg-orange-100 text-orange-800',
                            'tinggi' => 'bg-red-100 text-red-800',
                            'sangat_tinggi' => 'bg-red-600 text-white'
                        ];
                        $color = $levelColors[$risk->risk_level] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="px-2 py-1 text-xs rounded font-medium {{ $color }}">
                        {{ ucfirst(str_replace('_', ' ', $risk->risk_level)) }}
                    </span>
                </div>
            </div>
            <form method="POST" action="{{ route('risks.update', ['risk' => $risk->risk_id]) }}" class="p-5" id="riskForm">
                @csrf
                @method('PUT')
                
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
                                        {{ old('risk_pro_id', $risk->risk_pro_id) == $project->pro_id ? 'selected' : '' }}>
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
                                        {{ old('risk_organization_id', $risk->risk_organization_id) == $org->organization_id ? 'selected' : '' }}>
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
                                        {{ old('risk_strategic_objective_id', $risk->risk_strategic_objective_id) == $objective->strategic_objective_id ? 'selected' : '' }}>
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
                                        {{ old('risk_business_process_id', $risk->risk_business_process_id) == $process->business_process_id ? 'selected' : '' }}>
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
                                        {{ old('risk_category_id', $risk->risk_category_id) == $category->risk_category_id ? 'selected' : '' }}>
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
                                        {{ old('risk_user_id', $risk->risk_user_id) == $user->id ? 'selected' : '' }}>
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
                                  required>{{ old('risk_description', $risk->risk_description) }}</textarea>
                        <div class="text-xs text-gray-500 mt-1">
                            Jelaskan risiko dengan spesifik dan jelas. Minimal 20 karakter.
                        </div>
                        @error('risk_description')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Current Information -->
                <div class="mt-8 p-4 bg-gray-50 rounded-lg border">
                    <h4 class="font-medium mb-3 flex items-center">
                        <i data-feather="info" class="w-4 h-4 mr-2"></i> Informasi Saat Ini
                    </h4>
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-6">
                            <div class="p-3 bg-white rounded border">
                                <div class="text-xs text-gray-500 mb-1">Kode Risiko</div>
                                <div class="font-bold text-red-600">{{ $risk->risk_code }}</div>
                            </div>
                        </div>
                        <div class="col-span-12 md:col-span-6">
                            <div class="p-3 bg-white rounded border">
                                <div class="text-xs text-gray-500 mb-1">Level Risiko</div>
                                <div class="font-medium {{ $color }}">
                                    {{ ucfirst(str_replace('_', ' ', $risk->risk_level)) }}
                                    <span class="text-gray-600 text-xs">(Skor: {{ $risk->risk_score }})</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12">
                            <div class="p-3 bg-white rounded border">
                                <div class="text-xs text-gray-500 mb-1">Analisis Terakhir</div>
                                <div class="text-sm">
                                    @if($risk->last_analysis_date)
                                        {{ \Carbon\Carbon::parse($risk->last_analysis_date)->format('d F Y') }}
                                        <span class="text-gray-500 text-xs">
                                            ({{ $risk->likelihood_level }} × {{ $risk->impact_level }})
                                        </span>
                                    @else
                                        <span class="text-yellow-600">Belum dianalisis</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Warning jika sudah ada analisis -->
                @if($risk->analyses->count() > 0)
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h4 class="font-bold text-yellow-800 mb-2 flex items-center">
                        <i data-feather="alert-triangle" class="w-5 h-5 mr-2"></i> Perhatian!
                    </h4>
                    <p class="text-yellow-700 text-sm">
                        Risiko ini sudah memiliki <strong>{{ $risk->analyses->count() }} data analisis</strong>. 
                        Perubahan pada informasi dasar risiko dapat mempengaruhi analisis yang sudah ada. 
                        Pastikan untuk memperbarui analisis jika diperlukan.
                    </p>
                </div>
                @endif
                
                <!-- Form Actions -->
                <div class="flex justify-end mt-8 pt-5 border-t border-gray-200">
                    <a href="{{ route('risks.show', ['risk' => $risk->risk_id]) }}" 
                       class="btn btn-outline-secondary w-24 mr-3">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary w-32" id="submitBtn">
                        <i data-feather="save" class="w-4 h-4 mr-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-span-12 xl:col-span-4">
        <!-- History Risiko -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="clock" class="w-5 h-5 mr-2"></i> History Risiko
                </h2>
            </div>
            <div class="p-5">
                <div class="relative before:content-[''] before:block before:w-px before:h-full before:bg-gray-200 before:absolute before:left-0 before:top-0 before:ml-3.5">
                    <!-- Created -->
                    <div class="flex items-center mb-3">
                        <div class="w-7 h-7 flex items-center justify-center rounded-full bg-green-100 z-10">
                            <i data-feather="plus" class="w-3 h-3 text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <div class="font-medium">Risiko Diidentifikasi</div>
                            <div class="text-xs text-gray-500">{{ $risk->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    
                    <!-- Last Updated -->
                    @if($risk->updated_at != $risk->created_at)
                    <div class="flex items-center mb-3">
                        <div class="w-7 h-7 flex items-center justify-center rounded-full bg-blue-100 z-10">
                            <i data-feather="edit" class="w-3 h-3 text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <div class="font-medium">Terakhir Diupdate</div>
                            <div class="text-xs text-gray-500">{{ $risk->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Analyses -->
                    @if($risk->analyses->count() > 0)
                    <div class="flex items-center mb-3">
                        <div class="w-7 h-7 flex items-center justify-center rounded-full bg-orange-100 z-10">
                            <i data-feather="activity" class="w-3 h-3 text-orange-600"></i>
                        </div>
                        <div class="ml-4">
                            <div class="font-medium">{{ $risk->analyses->count() }} Analisis</div>
                            <div class="text-xs text-gray-500">
                                Terakhir: {{ $risk->last_analysis_date ? \Carbon\Carbon::parse($risk->last_analysis_date)->format('d/m/Y') : '-' }}
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Mitigations -->
                    @if($risk->mitigations->count() > 0)
                    <div class="flex items-center mb-3">
                        <div class="w-7 h-7 flex items-center justify-center rounded-full bg-purple-100 z-10">
                            <i data-feather="shield" class="w-3 h-3 text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <div class="font-medium">{{ $risk->mitigations->count() }} Rencana Mitigasi</div>
                            <div class="text-xs text-gray-500">
                                {{ $risk->mitigations->where('status', 'selesai')->count() }} selesai
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Monitorings -->
                    @if($risk->monitorings->count() > 0)
                    <div class="flex items-center">
                        <div class="w-7 h-7 flex items-center justify-center rounded-full bg-teal-100 z-10">
                            <i data-feather="eye" class="w-3 h-3 text-teal-600"></i>
                        </div>
                        <div class="ml-4">
                            <div class="font-medium">{{ $risk->monitorings->count() }} Pemantauan</div>
                            <div class="text-xs text-gray-500">
                                Terakhir: {{ $risk->monitorings->max('monitoring_date') ? \Carbon\Carbon::parse($risk->monitorings->max('monitoring_date'))->format('d/m/Y') : '-' }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Related Data -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="link" class="w-5 h-5 mr-2"></i> Data Terkait
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    @if($risk->project)
                    <a href="{{ route('projects.show', $risk->project->pro_id) }}" 
                       class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3">
                            <i data-feather="folder" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium">Proyek: {{ $risk->project->pro_nama }}</div>
                            <div class="text-xs text-gray-500">{{ $risk->project->pro_status }}</div>
                        </div>
                    </a>
                    @endif
                    
                    @if($risk->organization)
                    <a href="{{ route('organizations.show', $risk->organization->organization_id) }}" 
                       class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i data-feather="home" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium">Organisasi: {{ $risk->organization->organization_name }}</div>
                            <div class="text-xs text-gray-500">{{ $risk->organization->organization_code }}</div>
                        </div>
                    </a>
                    @endif
                    
                    @if($risk->strategicObjective)
                    <a href="{{ route('strategic-objectives.show', $risk->strategicObjective->strategic_objective_id) }}" 
                       class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                            <i data-feather="target" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium">Tujuan Strategis</div>
                            <div class="text-xs text-gray-500 truncate">{{ $risk->strategicObjective->strategic_objective_name }}</div>
                        </div>
                    </a>
                    @endif
                    
                    @if($risk->businessProcess)
                    <a href="{{ route('business-processes.show', $risk->businessProcess->business_process_id) }}" 
                       class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                            <i data-feather="briefcase" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium">Proses Bisnis</div>
                            <div class="text-xs text-gray-500 truncate">{{ $risk->businessProcess->business_process_name }}</div>
                        </div>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="zap" class="w-5 h-5 mr-2"></i> Aksi Cepat
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-2">
                    <a href="{{ route('risk-analyses.create', ['riskId' => $risk->risk_id]) }}" 
                    class="flex items-center p-3 bg-orange-50 hover:bg-orange-100 rounded-lg transition">
                        <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center mr-3">
                            <i data-feather="activity" class="w-4 h-4 text-orange-600"></i>
                        </div>
                        <div>
                            <div class="font-medium">Analisis Risiko</div>
                            <div class="text-xs text-gray-500">Tambah analisis baru</div>
                        </div>
                    </a>

                    <a href="{{ route('risk-mitigations.create', ['riskId' => $risk->risk_id]) }}" 
                       class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition">
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                            <i data-feather="shield" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <div>
                            <div class="font-medium">Rencana Mitigasi</div>
                            <div class="text-xs text-gray-500">Buat rencana mitigasi</div>
                        </div>
                    </a>

                    <a href="{{ route('risk-monitorings.create', ['riskId' => $risk->risk_id]) }}" 
                       class="flex items-center p-3 bg-teal-50 hover:bg-teal-100 rounded-lg transition">
                        <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center mr-3">
                            <i data-feather="eye" class="w-4 h-4 text-teal-600"></i>
                        </div>
                        <div>
                            <div class="font-medium">Pemantauan</div>
                            <div class="text-xs text-gray-500">Tambah data pemantauan</div>
                        </div>
                    </a>
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
        
        if (selectedOrgId) {
            // Filter objectives
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
        }
    }
    
    // Form validation
    form.addEventListener('submit', function(e) {
        const description = document.getElementById('risk_description').value;
        
        if (description.length < 20) {
            e.preventDefault();
            alert('Deskripsi risiko terlalu pendek. Minimal 20 karakter.');
            document.getElementById('risk_description').focus();
            return false;
        }
        
        // Disable submit button to prevent double submission
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-feather="loader" class="w-4 h-4 mr-2 animate-spin"></i> Menyimpan...';
        
        return true;
    });
    
    // Initial filter
    filterOptions();
    
    // Auto-filter when organization changes
    orgSelect.addEventListener('change', filterOptions);
});
</script>
@endpush