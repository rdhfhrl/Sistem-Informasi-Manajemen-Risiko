@extends('layouts.master')

@section('title', 'Tambah Proses Bisnis - SIMR')

@section('page-title', 'Tambah Proses Bisnis Baru')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('business-processes.index') }}">Proses Bisnis</a></li>
    <li class="breadcrumb-item active">Tambah Baru</li>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 xl:col-span-8">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Form Tambah Proses Bisnis
                </h2>
                <a href="{{ route('business-processes.index') }}" class="btn btn-outline-secondary">
                    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
                </a>
            </div>
            <form method="POST" action="{{ route('business-processes.store') }}" class="p-5">
                @csrf
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Organisasi/UPTD Selection -->
                    <div>
                        <label for="business_process_organization_id" class="form-label">
                            Pilih Organisasi/UPTD <span class="text-danger">*</span>
                        </label>
                        <select id="business_process_organization_id" name="business_process_organization_id" 
                                class="form-select w-full" required>
                            <option value="">Pilih Organisasi/UPTD...</option>
                            @foreach($organizations as $org)
                                <option value="{{ $org->organization_id }}" 
                                        {{ old('business_process_organization_id') == $org->organization_id ? 'selected' : '' }}>
                                    {{ $org->organization_code }} - {{ $org->organization_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="text-xs text-gray-500 mt-1">
                            * Pilih organisasi/UPTD yang akan memiliki proses bisnis ini
                        </div>
                        @error('business_process_organization_id')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Business Process Name -->
                    <div>
                        <label for="business_process_name" class="form-label">
                            Nama Proses Bisnis <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="business_process_name" name="business_process_name" 
                               class="form-control w-full" 
                               placeholder="Contoh: Pengadaan Barang & Jasa, Pemeliharaan Jalan, dll."
                               value="{{ old('business_process_name') }}"
                               required>
                        <div class="text-xs text-gray-500 mt-1">
                            * Beri nama yang jelas dan menggambarkan aktivitas utama
                        </div>
                        @error('business_process_name')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Business Process Description -->
                    <div>
                        <label for="business_process_description" class="form-label">
                            Deskripsi Proses Bisnis <span class="text-danger">*</span>
                        </label>
                        <textarea id="business_process_description" name="business_process_description" 
                                  class="form-control w-full" 
                                  rows="4"
                                  placeholder="Deskripsikan alur, tahapan, tujuan, dan pihak-pihak yang terlibat dalam proses ini..."
                                  required>{{ old('business_process_description') }}</textarea>
                        <div class="text-xs text-gray-500 mt-1">
                            * Jelaskan secara singkat dan jelas tentang proses ini
                        </div>
                        @error('business_process_description')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Preview -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium mb-3">Preview Proses Bisnis</h4>
                    <div class="flex items-start">
                        <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center mr-4 flex-shrink-0">
                            <i data-feather="briefcase" class="w-6 h-6 text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-lg" id="preview-name">Nama Proses Bisnis</div>
                            <div class="text-gray-600 text-sm mb-2" id="preview-org">Organisasi: Akan dipilih...</div>
                            <div class="text-gray-700 text-sm bg-white p-3 rounded border" id="preview-description">
                                Deskripsi proses bisnis akan muncul di sini...
                            </div>
                            <div class="mt-2 text-xs text-gray-500">
                                <i data-feather="clock" class="w-3 h-3 inline mr-1"></i>
                                <span id="preview-length">0</span> karakter
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
                        <li>• Proses bisnis merupakan serangkaian aktivitas terstruktur dalam organisasi</li>
                        <li>• Setiap proses bisnis harus dikaitkan dengan organisasi/UPTD tertentu</li>
                        <li>• Proses bisnis akan menjadi dasar untuk identifikasi risiko</li>
                        <li>• Deskripsi harus jelas agar risiko dapat diidentifikasi dengan tepat</li>
                        <li>• Pastikan nama proses spesifik dan tidak terlalu umum</li>
                    </ul>
                </div>
                
                <!-- Contoh Proses Bisnis PUPR -->
                <div class="mt-6 p-4 bg-green-50 rounded-lg border border-green-100">
                    <h4 class="font-bold text-green-800 mb-2 flex items-center">
                        <i data-feather="book-open" class="w-5 h-5 mr-2"></i> Contoh Proses Bisnis PUPR:
                    </h4>
                    <div class="text-sm text-green-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-3 bg-white rounded border">
                                <strong class="text-purple-600">Pengadaan Barang & Jasa:</strong><br>
                                Proses mulai dari perencanaan, penganggaran, pelaksanaan tender, hingga serah terima pekerjaan konstruksi.
                            </div>
                            <div class="p-3 bg-white rounded border">
                                <strong class="text-purple-600">Pemeliharaan Jalan:</strong><br>
                                Proses inspeksi, perencanaan, pelaksanaan perbaikan, dan evaluasi kondisi infrastruktur jalan.
                            </div>
                            <div class="p-3 bg-white rounded border">
                                <strong class="text-purple-600">Pengelolaan Sumber Daya Air:</strong><br>
                                Proses monitoring debit air, operasi bendungan, distribusi, dan konservasi sumber daya air.
                            </div>
                            <div class="p-3 bg-white rounded border">
                                <strong class="text-purple-600">Perencanaan Teknis:</strong><br>
                                Proses survei, desain engineering, kajian teknis, dan penyusunan dokumen perencanaan.
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end mt-8">
                    <a href="{{ route('business-processes.index') }}" class="btn btn-outline-secondary w-24 mr-3">
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
        <!-- Organisasi yang Sudah Memiliki Proses Bisnis -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Organisasi dengan Proses Bisnis
                </h2>
                @php
                    $orgsWithProcesses = $organizations->filter(function($org) {
                        return $org->businessProcesses->count() > 0;
                    });
                @endphp
                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded">
                    {{ $orgsWithProcesses->count() }} Organisasi
                </span>
            </div>
            <div class="p-5">
                @if($orgsWithProcesses->count() > 0)
                    <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                        @foreach($orgsWithProcesses as $org)
                            <div class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                        <i data-feather="briefcase" class="w-4 h-4 text-purple-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $org->organization_code }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $org->organization_name }} • {{ $org->businessProcesses->count() }} proses
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-medium text-sm">{{ $org->businessProcesses->count() }}</div>
                                        <div class="text-xs text-gray-500">proses</div>
                                    </div>
                                </div>
                                @if($org->businessProcesses->count() > 0)
                                <div class="mt-2 pl-11">
                                    <div class="text-xs text-gray-600">
                                        <strong>Proses utama:</strong>
                                        @foreach($org->businessProcesses->take(2) as $process)
                                            <div class="truncate">• {{ $process->business_process_name }}</div>
                                        @endforeach
                                        @if($org->businessProcesses->count() > 2)
                                            <div class="text-gray-500">+ {{ $org->businessProcesses->count() - 2 }} lainnya</div>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i data-feather="briefcase" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                        <p class="text-gray-500">Belum ada organisasi dengan proses bisnis</p>
                        <p class="text-xs text-gray-400 mt-1">Anda akan menjadi yang pertama!</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Panduan Proses Bisnis -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="help-circle" class="w-5 h-5 mr-2 inline"></i> Panduan Proses Bisnis
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="target" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Spesifik & Jelas</h4>
                            <p class="text-sm text-gray-600">Proses harus jelas cakupan dan batasannya</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="repeat" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Berulang & Terstruktur</h4>
                            <p class="text-sm text-gray-600">Aktivitas yang dilakukan berulang dengan alur tertentu</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="users" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Pihak Terkait</h4>
                            <p class="text-sm text-gray-600">Identifikasi unit/bagian yang terlibat</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="activity" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Tahapan Proses</h4>
                            <p class="text-sm text-gray-600">Jelaskan input, proses, dan output dengan jelas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Karakteristik Proses Bisnis -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="check-circle" class="w-5 h-5 mr-2 inline"></i> Karakteristik Proses Bisnis
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    <div class="flex items-start text-sm">
                        <i data-feather="check" class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span><strong>Memiliki Input & Output:</strong> Menerima masukan dan menghasilkan keluaran</span>
                    </div>
                    <div class="flex items-start text-sm">
                        <i data-feather="check" class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span><strong>Berorientasi Pelanggan:</strong> Memberikan nilai bagi pengguna/pemangku kepentingan</span>
                    </div>
                    <div class="flex items-start text-sm">
                        <i data-feather="check" class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span><strong>Cross-Functional:</strong> Melibatkan lebih dari satu unit/bagian</span>
                    </div>
                    <div class="flex items-start text-sm">
                        <i data-feather="check" class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span><strong>Terukur:</strong> Dapat diukur kinerja dan efektivitasnya</span>
                    </div>
                    <div class="flex items-start text-sm">
                        <i data-feather="check" class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span><strong>Berulang:</strong> Dilakukan secara reguler atau berkelanjutan</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tips Penulisan Deskripsi -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="edit-3" class="w-5 h-5 mr-2 inline"></i> Tips Penulisan Deskripsi
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-2 text-sm">
                    <div class="flex items-start">
                        <div class="w-6 h-6 rounded-full bg-yellow-100 flex items-center justify-center mr-2 flex-shrink-0">
                            <span class="text-xs">1</span>
                        </div>
                        <span>Mulai dengan tujuan/objektif proses</span>
                    </div>
                    <div class="flex items-start">
                        <div class="w-6 h-6 rounded-full bg-yellow-100 flex items-center justify-center mr-2 flex-shrink-0">
                            <span class="text-xs">2</span>
                        </div>
                        <span>Jelaskan tahapan utama secara berurutan</span>
                    </div>
                    <div class="flex items-start">
                        <div class="w-6 h-6 rounded-full bg-yellow-100 flex items-center justify-center mr-2 flex-shrink-0">
                            <span class="text-xs">3</span>
                        </div>
                        <span>Sebutkan unit/bagian yang bertanggung jawab</span>
                    </div>
                    <div class="flex items-start">
                        <div class="w-6 h-6 rounded-full bg-yellow-100 flex items-center justify-center mr-2 flex-shrink-0">
                            <span class="text-xs">4</span>
                        </div>
                        <span>Sertakan dokumen/regulasi yang mendasari</span>
                    </div>
                    <div class="flex items-start">
                        <div class="w-6 h-6 rounded-full bg-yellow-100 flex items-center justify-center mr-2 flex-shrink-0">
                            <span class="text-xs">5</span>
                        </div>
                        <span>Maksimal 500 karakter agar tetap efektif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Custom scrollbar */
    .max-h-96::-webkit-scrollbar {
        width: 6px;
    }
    .max-h-96::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    .max-h-96::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 3px;
    }
    .max-h-96::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Elements
    const orgSelect = document.getElementById('business_process_organization_id');
    const nameInput = document.getElementById('business_process_name');
    const descTextarea = document.getElementById('business_process_description');
    
    const previewName = document.getElementById('preview-name');
    const previewOrg = document.getElementById('preview-org');
    const previewDesc = document.getElementById('preview-description');
    const previewLength = document.getElementById('preview-length');
    
    // Data organizations untuk preview
    const orgData = {
        @foreach($organizations as $org)
            '{{ $org->organization_id }}': {
                code: '{{ $org->organization_code }}',
                name: '{{ $org->organization_name }}'
            },
        @endforeach
    };
    
    function updatePreview() {
        // Update process name
        if (nameInput.value) {
            previewName.textContent = nameInput.value;
        } else {
            previewName.textContent = 'Nama Proses Bisnis';
        }
        
        // Update organization
        if (orgSelect.value && orgData[orgSelect.value]) {
            const org = orgData[orgSelect.value];
            previewOrg.textContent = `Organisasi: ${org.code} - ${org.name}`;
        } else {
            previewOrg.textContent = 'Organisasi: Akan dipilih...';
        }
        
        // Update description
        if (descTextarea.value) {
            previewDesc.textContent = descTextarea.value;
        } else {
            previewDesc.textContent = 'Deskripsi proses bisnis akan muncul di sini...';
        }
        
        // Update character count
        const charCount = descTextarea.value.length;
        previewLength.textContent = charCount;
        
        // Add warning if too long
        if (charCount > 500) {
            previewDesc.classList.add('text-red-600', 'font-medium');
            previewLength.classList.add('text-red-500', 'font-bold');
        } else {
            previewDesc.classList.remove('text-red-600', 'font-medium');
            previewLength.classList.remove('text-red-500', 'font-bold');
        }
    }
    
    // Event listeners
    orgSelect.addEventListener('change', updatePreview);
    nameInput.addEventListener('input', updatePreview);
    descTextarea.addEventListener('input', updatePreview);
    
    // Add character counter to description textarea
    descTextarea.addEventListener('input', function() {
        const counter = this.parentElement.querySelector('.char-counter');
        if (!counter) {
            const counterDiv = document.createElement('div');
            counterDiv.className = 'text-xs text-gray-500 mt-1 char-counter';
            this.parentElement.appendChild(counterDiv);
        }
        this.parentElement.querySelector('.char-counter').textContent = 
            `${this.value.length} karakter (Rekomendasi: maksimal 500 karakter)`;
    });
    
    // Auto-suggest organization based on URL
    function getOrganizationFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        const orgId = urlParams.get('org_id');
        if (orgId && orgData[orgId]) {
            orgSelect.value = orgId;
            updatePreview();
        }
    }
    
    // Initial update
    updatePreview();
    getOrganizationFromUrl();
    
    // Trigger input event for counter
    descTextarea.dispatchEvent(new Event('input'));
});
</script>
@endpush