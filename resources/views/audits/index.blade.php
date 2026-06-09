@extends('layouts.master')

@section('title', 'Daftar Audit - SIMR')

@section('page-title', 'Daftar Audit')

@section('page-action')
<a href="{{ route('audits.create') }}" class="btn btn-primary shadow-md">
    <i data-feather="plus" class="w-4 h-4 mr-2"></i> Tambah Audit
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Stats Cards -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-100">
                                <i data-feather="clipboard" class="w-6 h-6 text-blue-600"></i>
                            </div>
                            <div class="ml-auto">
                                <div class="text-3xl font-bold leading-8">{{ $totalAudits }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Total Audit</div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-orange-100">
                                <i data-feather="alert-circle" class="w-6 h-6 text-orange-600"></i>
                            </div>
                            <div class="ml-auto">
                                <div class="text-3xl font-bold leading-8">{{ $auditsWithFindings }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Dengan Temuan</div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $totalAudits > 0 ? round(($auditsWithFindings / $totalAudits) * 100, 1) : 0 }}% dari total
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-100">
                                <i data-feather="calendar" class="w-6 h-6 text-green-600"></i>
                            </div>
                            <div class="ml-auto">
                                <div class="text-3xl font-bold leading-8">{{ $recentAudits }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Bulan Ini</div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ \Carbon\Carbon::now()->format('M Y') }}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-purple-100">
                                <i data-feather="users" class="w-6 h-6 text-purple-600"></i>
                            </div>
                            <div class="ml-auto">
                                <div class="text-3xl font-bold leading-8">{{ $auditorsCount }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Jumlah Auditor</div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $uniqueAuditorsThisMonth }} aktif bulan ini
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="intro-y box mb-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="filter" class="w-5 h-5 mr-2 text-purple-500"></i>
                    Filter Audit
                </h2>
            </div>
            <div class="p-5">
                <form method="GET" action="{{ route('audits.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="auditor" class="form-label">Auditor</label>
                        <input type="text" 
                               id="auditor" 
                               name="auditor" 
                               class="form-control" 
                               value="{{ request('auditor') }}"
                               placeholder="Cari nama auditor">
                    </div>
                    
                    <div>
                        <label for="audit_date" class="form-label">Tanggal Audit</label>
                        <input type="date" 
                               id="audit_date" 
                               name="audit_date" 
                               class="form-control" 
                               value="{{ request('audit_date') }}">
                    </div>
                    
                    <div>
                        <label for="has_findings" class="form-label">Memiliki Temuan</label>
                        <select id="has_findings" name="has_findings" class="form-select">
                            <option value="">Semua</option>
                            <option value="yes" {{ request('has_findings') == 'yes' ? 'selected' : '' }}>Ya</option>
                            <option value="no" {{ request('has_findings') == 'no' ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="has_recommendations" class="form-label">Memiliki Rekomendasi</label>
                        <select id="has_recommendations" name="has_recommendations" class="form-select">
                            <option value="">Semua</option>
                            <option value="yes" {{ request('has_recommendations') == 'yes' ? 'selected' : '' }}>Ya</option>
                            <option value="no" {{ request('has_recommendations') == 'no' ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>
                    
                    <div class="md:col-span-4 flex justify-end space-x-3">
                        <button type="submit" class="btn btn-primary w-32">
                            <i data-feather="filter" class="w-4 h-4 mr-2"></i> Filter
                        </button>
                        <a href="{{ route('audits.index') }}" class="btn btn-outline-secondary w-32">
                            <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Daftar Audit
                    <span class="text-gray-500 text-sm ml-2">({{ $audits->total() }} data)</span>
                </h2>
                <div class="flex items-center space-x-3">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-feather="download" class="w-4 h-4 mr-2"></i> Export
                        </button>
                        <div class="dropdown-menu" aria-labelledby="exportDropdown">
                            <a class="dropdown-item" href="#">
                                <i data-feather="file-text" class="w-4 h-4 mr-2"></i> Excel
                            </a>
                            <a class="dropdown-item" href="#">
                                <i data-feather="file" class="w-4 h-4 mr-2"></i> PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-5">
                @if($audits->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-report -mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">TANGGAL</th>
                                    <th class="whitespace-nowrap">AUDITOR</th>
                                    <th class="whitespace-nowrap">RISIKO TERKAIT</th>
                                    <th class="whitespace-nowrap">TEMUAN</th>
                                    <th class="whitespace-nowrap">REKOMENDASI</th>
                                    <th class="whitespace-nowrap">STATUS</th>
                                    <th class="whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($audits as $audit)
                                    <tr class="intro-x hover:bg-gray-50">
                                        <td>
                                            <div class="font-medium">
                                                {{ \Carbon\Carbon::parse($audit->audit_date)->format('d M Y') }}
                                            </div>
                                            <div class="text-gray-500 text-xs mt-0.5">
                                                {{ \Carbon\Carbon::parse($audit->audit_date)->diffForHumans() }}
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="font-medium">{{ $audit->auditor }}</div>
                                        </td>
                                        
                                        <td>
                                            @if($audit->risk)
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                                        <i data-feather="alert-triangle" class="w-4 h-4 text-blue-600"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium">{{ $audit->risk->risk_code }}</div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ Str::limit($audit->risk->risk_description, 30) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-sm">Audit Umum</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($audit->audit_findings)
                                                @php
                                                    $findingsCount = count(explode("\n", $audit->audit_findings));
                                                    $badgeColor = $findingsCount > 5 ? 'bg-red-100 text-red-800' : 
                                                                ($findingsCount > 2 ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800');
                                                @endphp
                                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badgeColor }}">
                                                    <i data-feather="alert-circle" class="w-3 h-3 inline mr-1"></i>
                                                    {{ $findingsCount }} temuan
                                                </span>
                                            @else
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Tidak ada
                                                </span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($audit->audit_recommendations)
                                                @php
                                                    $recommendationsCount = count(explode("\n", $audit->audit_recommendations));
                                                    $badgeColor = $recommendationsCount > 5 ? 'bg-green-100 text-green-800' : 
                                                                ($recommendationsCount > 2 ? 'bg-emerald-100 text-emerald-800' : 'bg-teal-100 text-teal-800');
                                                @endphp
                                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badgeColor }}">
                                                    <i data-feather="check-circle" class="w-3 h-3 inline mr-1"></i>
                                                    {{ $recommendationsCount }} rekomendasi
                                                </span>
                                            @else
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Tidak ada
                                                </span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @php
                                                $status = 'bg-gray-100 text-gray-800';
                                                if($audit->audit_findings && !$audit->audit_recommendations) {
                                                    $status = 'bg-orange-100 text-orange-800';
                                                } elseif($audit->audit_findings && $audit->audit_recommendations) {
                                                    $status = 'bg-green-100 text-green-800';
                                                }
                                            @endphp
                                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $status }}">
                                                @if($audit->audit_findings && $audit->audit_recommendations)
                                                    Selesai
                                                @elseif($audit->audit_findings && !$audit->audit_recommendations)
                                                    Perlu Rekomendasi
                                                @else
                                                    Draft
                                                @endif
                                            </span>
                                        </td>
                                        
                                        <td class="table-report__action">
                                            <div class="flex justify-center items-center space-x-2">
                                                <a class="flex items-center text-blue-600" 
                                                   href="{{ route('audits.show', $audit->audit_id) }}"
                                                   title="Lihat Detail">
                                                    <i data-feather="eye" class="w-4 h-4"></i>
                                                </a>
                                                <a class="flex items-center text-yellow-600" 
                                                   href="{{ route('audits.edit', $audit->audit_id) }}"
                                                   title="Edit">
                                                    <i data-feather="edit" class="w-4 h-4"></i>
                                                </a>
                                                <form action="{{ route('audits.destroy', $audit->audit_id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus audit ini?')"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex items-center text-red-600"
                                                            title="Hapus">
                                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($audits->hasPages())
                    <div class="flex flex-col sm:flex-row items-center p-5 border-t border-gray-200">
                        <div class="text-sm text-gray-500">
                            Menampilkan {{ $audits->firstItem() }} - {{ $audits->lastItem() }} dari {{ $audits->total() }} audit
                        </div>
                        <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                            {{ $audits->appends(request()->query())->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="clipboard" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada audit</h3>
                        <p class="text-gray-500 mb-6">Tambahkan audit pertama untuk memulai</p>
                        <a href="{{ route('audits.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="w-4 h-4 mr-2"></i> Tambah Audit Pertama
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
    feather.replace();
});
</script>
@endpush