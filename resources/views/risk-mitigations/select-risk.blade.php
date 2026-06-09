@extends('layouts.master')

@section('title', 'Pilih Risiko untuk Mitigasi - SIMR')

@section('page-title', 'Pilih Risiko')

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="alert-triangle" class="w-5 h-5 mr-2 text-red-500"></i>
                    Pilih Risiko untuk Mitigasi
                </h2>
            </div>
            <div class="p-5">
                <!-- Info -->
                <div class="mb-8 bg-blue-50 p-4 rounded-lg">
                    <div class="flex items-start">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i data-feather="info" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-blue-800 mb-2">Informasi</h4>
                            <p class="text-blue-700 text-sm">
                                Pilih risiko dari daftar di bawah ini untuk melihat atau membuat rencana mitigasi.
                                Anda juga bisa melihat semua rencana mitigasi tanpa filter.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mb-8">
                    <h4 class="font-medium text-gray-700 mb-4">Aksi Cepat</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="{{ route('risk-mitigations.all') }}" class="bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-black p-6 rounded-lg shadow-md transition-all duration-300 transform hover:-translate-y-1">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center mr-4">
                                    <i data-feather="shield" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <div class="text-lg font-bold">Semua Mitigasi</div>
                                    <div class="text-sm opacity-90 mt-1">Lihat semua rencana mitigasi</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('risks.create') }}" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-black p-6 rounded-lg shadow-md transition-all duration-300 transform hover:-translate-y-1">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center mr-4">
                                    <i data-feather="plus-circle" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <div class="text-lg font-bold">Risiko Baru</div>
                                    <div class="text-sm opacity-90 mt-1">Buat risiko baru</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('risks.index') }}" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-black p-6 rounded-lg shadow-md transition-all duration-300 transform hover:-translate-y-1">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center mr-4">
                                    <i data-feather="list" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <div class="text-lg font-bold">Daftar Risiko</div>
                                    <div class="text-sm opacity-90 mt-1">Kelola semua risiko</div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('dashboard') }}" class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-black p-6 rounded-lg shadow-md transition-all duration-300 transform hover:-translate-y-1">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center mr-4">
                                    <i data-feather="home" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <div class="text-lg font-bold">Dashboard</div>
                                    <div class="text-sm opacity-90 mt-1">Kembali ke dashboard</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- List of Risks -->
                <div class="mt-8">
                    <h4 class="font-medium text-gray-700 mb-4">Daftar Risiko dengan Prioritas Tinggi</h4>
                    
                    @php
                        // Ambil 10 risiko dengan skor tertinggi dan memiliki evaluasi
                        $risks = \App\Models\Risk::with(['mitigations' => function($query) {
                            $query->latest()->limit(1);
                        }])
                        ->where('risk_level', 'like', '%tinggi%') // Ambil risiko tinggi dan sangat tinggi
                        ->orderBy('risk_score', 'desc')
                        ->limit(10)
                        ->get();
                    @endphp
                    
                    @if($risks->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($risks as $risk)
                                <div class="bg-white border rounded-lg p-4 hover:shadow-lg transition-shadow duration-300">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <div class="font-bold text-gray-800">{{ $risk->risk_code }}</div>
                                            <div class="text-sm text-gray-600 mt-1">{{ Str::limit($risk->risk_description, 40) }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold 
                                                @if($risk->risk_level == 'sangat_tinggi') text-red-600
                                                @elseif($risk->risk_level == 'tinggi') text-orange-600
                                                @else text-yellow-600
                                                @endif">
                                                {{ $risk->risk_score ?? 'N/A' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ ucwords(str_replace('_', ' ', $risk->risk_level)) }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        @if($risk->mitigations->count() > 0)
                                            @php
                                                $latestMitigation = $risk->mitigations->first();
                                                $statusColor = match($latestMitigation->status) {
                                                    'selesai' => 'bg-green-100 text-green-800',
                                                    'dalam proses' => 'bg-blue-100 text-blue-800',
                                                    'belum dimulai' => 'bg-gray-100 text-gray-800',
                                                    'ditunda' => 'bg-yellow-100 text-yellow-800',
                                                    default => 'bg-red-100 text-red-800'
                                                };
                                            @endphp
                                            <span class="px-2 py-1 rounded text-xs font-medium {{ $statusColor }}">
                                                Mitigasi: {{ ucwords($latestMitigation->status) }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                                Belum ada mitigasi
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex justify-between items-center mt-4">
                                        <div class="text-xs text-gray-500">
                                            @if($risk->mitigations->count() > 0)
                                                {{ $risk->mitigations->count() }} rencana mitigasi
                                            @else
                                                Belum ada mitigasi
                                            @endif
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('risk-mitigations.by-risk', $risk->risk_id) }}" 
                                               class="text-xs bg-indigo-100 text-indigo-800 hover:bg-indigo-200 px-3 py-1 rounded-full transition-colors duration-300 flex items-center">
                                                <i data-feather="eye" class="w-3 h-3 mr-1"></i> Lihat Mitigasi
                                            </a>
                                            <a href="{{ route('risk-mitigations.create', $risk->risk_id) }}" 
                                               class="text-xs bg-green-100 text-green-800 hover:bg-green-200 px-3 py-1 rounded-full transition-colors duration-300 flex items-center">
                                                <i data-feather="plus" class="w-3 h-3 mr-1"></i> Mitigasi Baru
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6 text-center">
                            <a href="{{ route('risks.index') }}" class="btn btn-primary flex items-center justify-center mx-auto">
                                <i data-feather="list" class="w-4 h-4 mr-2"></i> Lihat Semua Risiko
                            </a>
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                                <i data-feather="alert-triangle" class="w-8 h-8 text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada risiko dengan prioritas tinggi</h3>
                            <p class="text-gray-500 mb-6">Mulailah dengan menambahkan risiko terlebih dahulu</p>
                            <a href="{{ route('risks.create') }}" class="btn btn-primary flex items-center justify-center mx-auto">
                                <i data-feather="plus" class="w-4 h-4 mr-2"></i> Tambah Risiko Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush