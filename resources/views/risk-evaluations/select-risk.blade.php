@extends('layouts.master')

@section('title', 'Pilih Risiko untuk Evaluasi - SIMR')

@section('page-title', 'Pilih Risiko')

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="alert-triangle" class="w-5 h-5 mr-2 text-red-500"></i>
                    Pilih Risiko untuk Evaluasi
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
                                Pilih risiko dari daftar di bawah ini untuk melihat atau membuat evaluasi risiko.
                                Anda juga bisa melihat semua evaluasi tanpa filter.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mb-8">
                    <h4 class="font-medium text-gray-700 mb-4">Aksi Cepat</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="{{ route('risk-evaluations.all') }}" class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-black p-6 rounded-lg shadow-md transition-all duration-300 transform hover:-translate-y-1">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center mr-4">
                                    <i data-feather="star" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <div class="text-lg font-bold">Semua Evaluasi</div>
                                    <div class="text-sm opacity-90 mt-1">Lihat semua evaluasi risiko</div>
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
                    <h4 class="font-medium text-gray-700 mb-4">Daftar Risiko Terbaru</h4>
                    
                    @php
                        // Ambil 10 risiko terbaru dengan evaluasi
                        $risks = \App\Models\Risk::with(['evaluations' => function($query) {
                            $query->latest()->limit(1);
                        }])
                        ->orderBy('created_at', 'desc')
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
                                        @if($risk->evaluations->count() > 0)
                                            @php
                                                $latestEvaluation = $risk->evaluations->first();
                                            @endphp
                                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                                @if($latestEvaluation->risk_evaluation_priority == 'rendah') bg-green-100 text-green-800
                                                @elseif($latestEvaluation->risk_evaluation_priority == 'sedang') bg-yellow-100 text-yellow-800
                                                @elseif($latestEvaluation->risk_evaluation_priority == 'tinggi') bg-orange-100 text-orange-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucwords($latestEvaluation->risk_evaluation_priority) }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex justify-between items-center mt-4">
                                        <div class="text-xs text-gray-500">
                                            @if($risk->evaluations->count() > 0)
                                                Evaluasi terakhir: {{ \Carbon\Carbon::parse($risk->evaluations->first()->evaluation_date)->format('d/m/Y') }}
                                            @else
                                                Belum ada evaluasi
                                            @endif
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('risk-evaluations.by-risk', $risk->risk_id) }}" 
                                               class="text-xs bg-purple-100 text-purple-800 hover:bg-purple-200 px-3 py-1 rounded-full transition-colors duration-300">
                                                Lihat Evaluasi
                                            </a>
                                            <a href="{{ route('risk-evaluations.create', $risk->risk_id) }}" 
                                               class="text-xs bg-green-100 text-green-800 hover:bg-green-200 px-3 py-1 rounded-full transition-colors duration-300">
                                                Evaluasi Baru
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6 text-center">
                            <a href="{{ route('risks.index') }}" class="btn btn-primary">
                                <i data-feather="list" class="w-4 h-4 mr-2"></i> Lihat Semua Risiko
                            </a>
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                                <i data-feather="alert-triangle" class="w-10 h-10 text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada risiko</h3>
                            <p class="text-gray-500 mb-6">Tambahkan risiko terlebih dahulu untuk melakukan evaluasi</p>
                            <a href="{{ route('risks.create') }}" class="btn btn-primary">
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