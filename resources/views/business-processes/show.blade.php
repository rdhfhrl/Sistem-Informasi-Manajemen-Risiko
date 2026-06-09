@extends('layouts.master')

@section('title', 'Detail Proses Bisnis - SIMR')

@section('page-title', 'Detail Proses Bisnis')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('business-processes.index') }}">Proses Bisnis</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 xl:col-span-8">
        <!-- Process Information Card -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center mr-4">
                        <i data-feather="briefcase" class="w-6 h-6 text-purple-600"></i>
                    </div>
                    <div>
                        <h2 class="font-medium text-base mr-auto">
                            {{ $process->business_process_name }}
                        </h2>
                        <div class="text-gray-500 text-sm">
                            ID: BP-{{ str_pad($process->business_process_id, 4, '0', STR_PAD_LEFT) }}
                        </div>
                    </div>
                </div>
                <div class="ml-auto flex items-center space-x-2">
                    <a href="{{ route('business-processes.edit', $process->business_process_id) }}" 
                       class="btn btn-primary">
                        <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit
                    </a>
                    <a href="{{ route('business-processes.index') }}" 
                       class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-12 gap-6 mb-6">
                    <!-- Organization -->
                    <div class="col-span-12 md:col-span-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700 mb-2">Organisasi/UPTD</h4>
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <i data-feather="home" class="w-5 h-5 text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="font-medium">{{ $process->organization->organization_name ?? 'UPTD PUPR MEDAN' }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $process->organization->organization_code ?? '' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Risk Statistics -->
                    <div class="col-span-12 md:col-span-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700 mb-2">Statistik Risiko</h4>
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full 
                                    @if($process->risks->count() >= 5) bg-red-100 
                                    @elseif($process->risks->count() >= 3) bg-orange-100 
                                    @elseif($process->risks->count() > 0) bg-yellow-100 
                                    @else bg-green-100 
                                    @endif flex items-center justify-center mr-3">
                                    <i data-feather="alert-triangle" class="w-5 h-5 
                                        @if($process->risks->count() >= 5) text-red-600 
                                        @elseif($process->risks->count() >= 3) text-orange-600 
                                        @elseif($process->risks->count() > 0) text-yellow-600 
                                        @else text-green-600 
                                        @endif"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-xl">{{ $process->risks->count() }}</div>
                                    <div class="text-sm text-gray-600">Risiko Teridentifikasi</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Process Description -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-700 mb-3">Deskripsi Proses</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700 whitespace-pre-line">{{ $process->business_process_description ?? 'Tidak ada deskripsi' }}</p>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div>
                        <div class="text-sm text-gray-500">Dibuat: {{ \Carbon\Carbon::parse($process->created_at)->format('d F Y, H:i') }}</div>
                        <div class="text-sm text-gray-500">Diperbarui: {{ \Carbon\Carbon::parse($process->updated_at)->format('d F Y, H:i') }}</div>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('risks.create', ['business_process_id' => $process->business_process_id]) }}" 
                           class="btn btn-primary">
                            <i data-feather="plus" class="w-4 h-4 mr-2"></i> Tambah Risiko
                        </a>
                        <form method="POST" action="{{ route('business-processes.destroy', $process->business_process_id) }}" 
                              onsubmit="return confirm('Hapus proses bisnis ini? Data risiko yang terkait akan tetap ada.')"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Risks -->
        @if($process->risks->count() > 0)
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="alert-triangle" class="w-5 h-5 mr-2"></i> Daftar Risiko Terkait
                    <span class="text-gray-500 text-sm ml-2">({{ $process->risks->count() }} risiko)</span>
                </h2>
                <a href="{{ route('risks.create', ['business_process_id' => $process->business_process_id]) }}" 
                   class="btn btn-primary btn-sm">
                    <i data-feather="plus" class="w-4 h-4 mr-2"></i> Tambah Risiko
                </a>
            </div>
            <div class="p-5">
                <div class="overflow-x-auto">
                    <table class="table table-report -mt-2">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap">NAMA RISIKO</th>
                                <th class="whitespace-nowrap">TINGKAT RISIKO</th>
                                <th class="whitespace-nowrap">STATUS</th>
                                <th class="whitespace-nowrap">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($process->risks as $risk)
                            <tr class="intro-x hover:bg-gray-50">
                                <td>
                                    <a href="{{ route('risks.show', $risk->risk_id) }}" 
                                       class="font-medium hover:text-theme-1">
                                        {{ $risk->risk_description }}
                                    </a>
                                    @if($risk->risk_name)
                                    <div class="text-gray-500 text-xs mt-0.5">
                                        {{ $risk->risk_name }}
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $levelColors = [
                                            'Sangat Tinggi' => 'bg-red-600 text-white',
                                            'Tinggi' => 'bg-red-500 text-white',
                                            'Sedang' => 'bg-yellow-500 text-white',
                                            'Rendah' => 'bg-green-500 text-white',
                                            'Sangat Rendah' => 'bg-green-300 text-white',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs rounded-full {{ $levelColors[$risk->risk_level] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $risk->risk_level ?? 'Belum ditetapkan' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'Open' => 'bg-red-100 text-red-800',
                                            'Dalam Proses' => 'bg-yellow-100 text-yellow-800',
                                            'Tertangani' => 'bg-green-100 text-green-800',
                                            'Ditutup' => 'bg-gray-100 text-gray-800',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$risk->risk_status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $risk->risk_status ?? 'Open' }}
                                    </span>
                                </td>
                                <td class="table-report__action w-56">
                                    <div class="flex justify-center items-center">
                                        <a class="flex items-center mr-3" 
                                           href="{{ route('risks.show', $risk->risk_id) }}">
                                            <i data-feather="eye" class="w-4 h-4 mr-1"></i> Detail
                                        </a>
                                        <a class="flex items-center mr-3" 
                                           href="{{ route('risks.edit', $risk->risk_id) }}">
                                            <i data-feather="edit" class="w-4 h-4 mr-1"></i> Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="alert-triangle" class="w-5 h-5 mr-2"></i> Daftar Risiko Terkait
                </h2>
            </div>
            <div class="p-5 text-center">
                <div class="w-16 h-16 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-4">
                    <i data-feather="check" class="w-8 h-8 text-green-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Tidak ada risiko teridentifikasi</h3>
                <p class="text-gray-500 mb-4">Proses bisnis ini belum memiliki risiko yang diidentifikasi.</p>
                <a href="{{ route('risks.create', ['business_process_id' => $process->business_process_id]) }}" 
                   class="btn btn-primary">
                    <i data-feather="plus-circle" class="w-4 h-4 mr-2"></i> Identifikasi Risiko Pertama
                </a>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-span-12 xl:col-span-4">
        <!-- Quick Actions -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="zap" class="w-5 h-5 mr-2"></i> Aksi Cepat
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    <a href="{{ route('risks.create', ['business_process_id' => $process->business_process_id]) }}" 
                       class="flex items-center p-3 bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                        <div class="w-8 h-8 rounded-full bg-theme-1 flex items-center justify-center mr-3">
                            <i data-feather="plus" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <div class="font-medium">Tambah Risiko</div>
                            <div class="text-xs text-gray-600">Identifikasi risiko baru</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('business-processes.edit', $process->business_process_id) }}" 
                       class="flex items-center p-3 bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                        <div class="w-8 h-8 rounded-full bg-theme-1 flex items-center justify-center mr-3">
                            <i data-feather="edit" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <div class="font-medium">Edit Proses</div>
                            <div class="text-xs text-gray-600">Ubah nama atau deskripsi</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('business-processes.index') }}" 
                       class="flex items-center p-3 bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                        <div class="w-8 h-8 rounded-full bg-theme-1 flex items-center justify-center mr-3">
                            <i data-feather="list" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <div class="font-medium">Semua Proses</div>
                            <div class="text-xs text-gray-600">Kembali ke daftar</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Technical Information -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="database" class="w-5 h-5 mr-2"></i> Informasi Teknis
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">ID Proses</div>
                        <div class="font-mono text-sm bg-gray-100 p-2 rounded">{{ $process->business_process_id }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">ID Organisasi</div>
                        <div class="text-sm">{{ $process->business_process_organization_id }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Status Aktif</div>
                        <div class="text-sm flex items-center">
                            <i data-feather="check" class="w-4 h-4 text-green-500 mr-1"></i>
                            Aktif
                        </div>
                    </div>
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
    feather.replace();
});
</script>
@endpush