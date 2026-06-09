@extends('layouts.master')

@section('title', 'Organisasi - SIMR')

@section('page-title', 'Manajemen Dinas Pekerjaan Umum dan Penataan Ruang Provinsi Sumatera Utara')

@section('page-action')
<a href="{{ route('organizations.create') }}" class="btn btn-primary shadow-md mr-2">
    <i data-feather="plus-circle" class="w-4 h-4 mr-2"></i> Tambah UPTD
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Dinas PUPR Information -->
        @if($dinasPUPR)
        <div class="intro-y box mb-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center mr-4">
                        <i data-feather="home" class="text-black w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-lg text-blue-800">{{ $dinasPUPR->organization_name }}</h2>
                        <div class="text-blue-600 text-sm">
                            <i data-feather="tag" class="w-3 h-3 inline mr-1"></i>
                            {{ $dinasPUPR->organization_code }}
                        </div>
                    </div>
                </div>
                <div class="ml-auto">
                    <div class="text-right">
                        <div class="text-2xl font-bold text-blue-800">{{ $uptdList->count() }}</div>
                        <div class="text-sm text-gray-600">Total UPTD</div>
                    </div>
                </div>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i data-feather="info" class="text-blue-600 w-4 h-4"></i>
                            </div>
                            <div>
                                <div class="font-medium text-blue-800">Induk Organisasi</div>
                                <div class="text-sm text-blue-600">Parent dari semua UPTD</div>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                <i data-feather="check-circle" class="text-green-600 w-4 h-4"></i>
                            </div>
                            <div>
                                <div class="font-medium text-green-800">Status Aktif</div>
                                <div class="text-sm text-green-600">Organisasi berjalan</div>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                <i data-feather="map-pin" class="text-purple-600 w-4 h-4"></i>
                            </div>
                            <div>
                                <div class="font-medium text-purple-800">Kota Medan</div>
                                <div class="text-sm text-purple-600">Lokasi utama</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- UPTD List -->
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Daftar UPTD PUPR
                    <span class="text-gray-500 text-sm ml-2">({{ $uptdList->count() }} unit)</span>
                </h2>
            </div>
            <div class="p-5">
                @if($uptdList->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-report -mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">KODE UPTD</th>
                                    <th class="whitespace-nowrap">LOKASI</th>
                                    <th class="whitespace-nowrap">DESKRIPSI</th>
                                    <th class="whitespace-nowrap">STATUS</th>
                                    <th class="whitespace-nowrap">RISIKO</th>
                                    <th class="whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($uptdList as $uptd)
                                    <tr class="intro-x hover:bg-gray-50">
                                        <td>
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full bg-theme-1/10 flex items-center justify-center mr-3">
                                                    <i data-feather="home" class="text-theme-1 w-5 h-5"></i>
                                                </div>
                                                <div>
                                                    <a href="{{ route('organizations.show', $uptd->organization_id) }}" 
                                                       class="font-medium hover:text-theme-1">
                                                        {{ $uptd->organization_name }}
                                                    </a>
                                                    <div class="text-gray-500 text-xs mt-0.5">
                                                        {{ $uptd->organization_code }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex items-center">
                                                <i data-feather="map-pin" class="text-gray-400 w-4 h-4 mr-2"></i>
                                                <span class="font-medium">{{ $uptd->location }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-gray-600 max-w-xs">
                                                @if($uptd->organization_description)
                                                    {{ Str::limit($uptd->organization_description, 50) }}
                                                @else
                                                    <span class="text-gray-400">Tidak ada deskripsi</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($uptd->is_active)
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i data-feather="check-circle" class="w-3 h-3 inline mr-1"></i>
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i data-feather="x-circle" class="w-3 h-3 inline mr-1"></i>
                                                    Non-Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex items-center">
                                                @if(($uptd->risks_count ?? 0) > 0)
                                                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center mr-2">
                                                        <i data-feather="alert-triangle" class="text-red-600 w-4 h-4"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium">{{ $uptd->risks_count ?? 0 }}</div>
                                                        <div class="text-xs text-gray-500">risiko</div>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400"> 0</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="table-report__action w-56">
                                            <div class="flex justify-center items-center">
                                                <a class="flex items-center mr-3 text-theme-1 hover:text-theme-2" 
                                                   href="{{ route('organizations.show', $uptd->organization_id) }}">
                                                    <i data-feather="eye" class="w-4 h-4 mr-1"></i> Detail
                                                </a>
                                                <a class="flex items-center mr-3 text-blue-600 hover:text-blue-800" 
                                                   href="{{ route('organizations.edit', $uptd->organization_id) }}">
                                                    <i data-feather="edit" class="w-4 h-4 mr-1"></i> Edit
                                                </a>
                                                <form method="POST" 
                                                      action="{{ route('organizations.destroy', $uptd->organization_id) }}"
                                                      class="delete-form inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex items-center text-red-600 hover:text-red-800" 
                                                            onclick="return confirm('Hapus UPTD ini?')">
                                                        <i data-feather="trash-2" class="w-4 h-4 mr-1"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="building" class="text-gray-400 w-8 h-8"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada data UPTD</h3>
                        <p class="text-gray-500 mb-6">Mulai dengan menambahkan UPTD pertama</p>
                        <a href="{{ route('organizations.create') }}" 
                           class="btn btn-primary">
                            <i data-feather="plus-circle" class="w-4 h-4 mr-2"></i> Tambah UPTD
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush