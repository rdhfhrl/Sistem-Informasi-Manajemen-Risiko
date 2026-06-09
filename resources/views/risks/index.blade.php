@extends('layouts.master')

@section('title', 'Manajemen Risiko - SIMR')

@section('page-title', 'Manajemen Risiko')

@section('page-action')
<a href="{{ route('risks.create') }}" class="btn btn-primary shadow-md mr-2">
    <i data-feather="plus-circle" class="w-4 h-4 mr-2"></i> Tambah Risiko
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Stats Cards -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-100">
                                <i data-feather="alert-triangle" class="w-6 h-6 text-blue-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <div class="text-3xl font-bold leading-8">{{ $risks->total() }}</div>
                            <div class="text-base text-gray-600 mt-1">Total Risiko</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-red-100">
                                <i data-feather="alert-circle" class="w-6 h-6 text-red-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            @php
                                $highRisks = $risks->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])->count();
                            @endphp
                            <div class="text-3xl font-bold leading-8">{{ $highRisks }}</div>
                            <div class="text-base text-gray-600 mt-1">Risiko Tinggi</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-yellow-100">
                                <i data-feather="alert-triangle" class="w-6 h-6 text-yellow-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            @php
                                $mediumRisks = $risks->where('risk_level', 'sedang')->count();
                            @endphp
                            <div class="text-3xl font-bold leading-8">{{ $mediumRisks }}</div>
                            <div class="text-base text-gray-600 mt-1">Risiko Sedang</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-100">
                                <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            @php
                                $lowRisks = $risks->whereIn('risk_level', ['rendah', 'sangat_rendah'])->count();
                            @endphp
                            <div class="text-3xl font-bold leading-8">{{ $lowRisks }}</div>
                            <div class="text-base text-gray-600 mt-1">Risiko Rendah</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risks List -->
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Daftar Risiko
                    <span class="text-gray-500 text-sm ml-2">({{ $risks->total() }} data)</span>
                </h2>
                <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                    <div class="dropdown">
                        <div class="dropdown-menu w-40">
                            <div class="dropdown-content">
                                <a href="{{ route('risks.index', ['sort' => 'score_desc']) }}" 
                                   class="dropdown-item {{ request('sort') == 'score_desc' ? 'active' : '' }}">
                                    Skor Tertinggi
                                </a>
                                <a href="{{ route('risks.index', ['sort' => 'score_asc']) }}" 
                                   class="dropdown-item {{ request('sort') == 'score_asc' ? 'active' : '' }}">
                                    Skor Terendah
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('risks.index', ['sort' => 'date_desc']) }}" 
                                   class="dropdown-item {{ request('sort') == 'date_desc' ? 'active' : '' }}">
                                    Terbaru
                                </a>
                                <a href="{{ route('risks.index', ['sort' => 'date_asc']) }}" 
                                   class="dropdown-item {{ request('sort') == 'date_asc' ? 'active' : '' }}">
                                    Terlama
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-5">
                @if($risks->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-report -mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">KODE</th>
                                    <th class="whitespace-nowrap">DESKRIPSI</th>
                                    <th class="whitespace-nowrap">ORGANISASI</th>
                                    <th class="whitespace-nowrap">PROYEK</th>
                                    <th class="whitespace-nowrap">KATEGORI</th>
                                    <th class="whitespace-nowrap">LEVEL</th>
                                    <th class="whitespace-nowrap">SKOR</th>
                                    <th class="whitespace-nowrap text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($risks as $risk)
                                    <tr class="intro-x hover:bg-gray-50">
                                        <td>
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                                    <i data-feather="alert-triangle" class="w-5 h-5 text-red-600"></i>
                                                </div>
                                                <div>
                                                    @if($risk && $risk->risk_id)
                                                        <a href="{{ route('risks.show', ['risk' => $risk->risk_id]) }}" class="font-medium hover:text-red-600">
                                                            {{ $risk->risk_code }}
                                                        </a>
                                                    @endif
                                                    <div class="text-gray-500 text-xs mt-0.5">
                                                        {{ \Carbon\Carbon::parse($risk->created_at)->format('d/m/Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="max-w-xs">
                                                <div class="text-sm text-gray-600 line-clamp-2">
                                                    {{ $risk->risk_description }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    @if($risk->strategicObjective)
                                                        <span class="bg-blue-100 text-blue-800 px-1 rounded">TO: {{ Str::limit($risk->strategicObjective->strategic_objective_name, 20) }}</span>
                                                    @endif
                                                    @if($risk->businessProcess)
                                                        <span class="bg-purple-100 text-purple-800 px-1 rounded ml-1">PB: {{ Str::limit($risk->businessProcess->business_process_name, 20) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($risk->organization)
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                                        <i data-feather="briefcase" class="w-3 h-3 text-blue-600"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="font-medium text-sm truncate">
                                                            {{ $risk->organization->organization_code }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 truncate">
                                                            {{ Str::limit($risk->organization->organization_name, 15) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($risk->project)
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-2">
                                                        <i data-feather="briefcase" class="w-3 h-3 text-theme-1"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="font-medium text-sm truncate">
                                                            {{ Str::limit($risk->project->pro_nama, 15) }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ \Carbon\Carbon::parse($risk->project->pro_tanggal_mulai)->format('d/m/Y') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($risk->category)
                                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded">
                                                    {{ $risk->category->risk_category_name }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $levelColors = [
                                                    'sangat_rendah' => 'bg-green-100 text-green-800',
                                                    'rendah' => 'bg-yellow-100 text-yellow-800',
                                                    'sedang' => 'bg-orange-100 text-orange-800',
                                                    'tinggi' => 'bg-red-100 text-red-800',
                                                    'sangat_tinggi' => 'bg-red-600 text-red'
                                                ];
                                                
                                                $levelTexts = [
                                                    'sangat_rendah' => 'Sangat Rendah',
                                                    'rendah' => 'Rendah',
                                                    'sedang' => 'Sedang',
                                                    'tinggi' => 'Tinggi',
                                                    'sangat_tinggi' => 'Sangat Tinggi'
                                                ];
                                                
                                                $color = $levelColors[$risk->risk_level] ?? 'bg-gray-100 text-gray-800';
                                                $text = $levelTexts[$risk->risk_level] ?? '-';
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded font-medium {{ $color }}">
                                                {{ $text }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <div class="text-xl font-bold 
                                                    @if($risk->risk_level == 'sangat_tinggi') text-red-600
                                                    @elseif($risk->risk_level == 'tinggi') text-orange-600
                                                    @elseif($risk->risk_level == 'sedang') text-yellow-600
                                                    @else text-green-600
                                                    @endif">
                                                    {{ $risk->risk_score }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $risk->likelihood_level }} × {{ $risk->impact_level }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="flex justify-center items-center space-x-2">
                                                <a href="{{ route('risk-analyses.index', ['riskId' => $risk->risk_id]) }}" 
                                                class="btn btn-primary btn-sm" title="Analisis Risiko">
                                                    <i data-feather="bar-chart-2" class="w-4 h-4"></i>
                                                </a>
                                                <a href="{{ route('risks.show', ['risk' => $risk->risk_id]) }}"  
                                                    class="btn btn-secondary btn-sm" title="Detail">
                                                    <i data-feather="eye" class="w-4 h-4"></i>
                                                </a>
                                                <a href="{{ route('risks.edit', ['risk' => $risk->risk_id]) }}"  
                                                    class="btn btn-warning btn-sm" title="Edit">
                                                    <i data-feather="edit" class="w-4 h-4"></i>
                                                </a>
                                                <form method="POST" 
                                                    action="{{ route('risks.destroy', ['risk' => $risk->risk_id]) }}"  
                                                    class="delete-form inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                            onclick="return confirm('Hapus risiko {{ $risk->risk_code }}?')"
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
                    @if($risks->hasPages())
                    <div class="flex flex-col sm:flex-row items-center p-5 border-t border-gray-200">
                        <div class="text-sm text-gray-500">
                            Menampilkan {{ $risks->firstItem() }} - {{ $risks->lastItem() }} dari {{ $risks->total() }} risiko
                        </div>
                        <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                            {{ $risks->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="alert-triangle" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        @if(request()->has('search') || request()->has('category') || request()->has('organization') || request()->has('project'))
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Tidak ditemukan</h3>
                            <p class="text-gray-500 mb-6">Tidak ada risiko yang sesuai dengan filter yang dipilih</p>
                            <a href="{{ route('risks.index') }}" 
                               class="btn btn-secondary mr-2">
                                <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i> Reset Filter
                            </a>
                        @else
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada data risiko</h3>
                            <p class="text-gray-500 mb-6">Mulai dengan menambahkan risiko baru untuk manajemen risiko</p>
                        @endif
                        <a href="{{ route('risks.create') }}" 
                           class="btn btn-primary">
                            <i data-feather="plus-circle" class="w-4 h-4 mr-2"></i> Tambah Risiko Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Risk Matrix Preview -->
        @if($risks->count() > 0)
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="grid" class="w-5 h-5 mr-2"></i> Matriks Risiko
                </h2>
            </div>
            <div class="p-5">
                <div class="risk-matrix-preview">
                    <table class="w-full text-sm">
                        <thead>
                            <tr>
                                <th class="p-2 text-center font-medium bg-gray-50" colspan="6">Tingkat Dampak (Impact)</th>
                            </tr>
                            <tr>
                                <th class="p-2 text-center font-medium bg-gray-50">L/D</th>
                                @for($impact = 1; $impact <= 5; $impact++)
                                <th class="p-2 text-center font-medium bg-gray-50">{{ $impact }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @for($likelihood = 5; $likelihood >= 1; $likelihood--)
                            <tr>
                                <td class="p-2 text-center font-medium bg-gray-50">{{ $likelihood }}</td>
                                @for($impact = 1; $impact <= 5; $impact++)
                                    @php
                                        $score = $likelihood * $impact;
                                        $level = match(true) {
                                            $score >= 20 => 'sangat_tinggi',
                                            $score >= 15 => 'tinggi',
                                            $score >= 10 => 'sedang',
                                            $score >= 5 => 'rendah',
                                            default => 'sangat_rendah'
                                        };
                                        $color = match($level) {
                                            'sangat_tinggi' => 'bg-red-600',
                                            'tinggi' => 'bg-red-400',
                                            'sedang' => 'bg-orange-400',
                                            'rendah' => 'bg-yellow-400',
                                            'sangat_rendah' => 'bg-green-400',
                                            default => 'bg-gray-400'
                                        };
                                        $riskCount = $risks->where('likelihood_level', $likelihood)
                                            ->where('impact_level', $impact)
                                            ->count();
                                    @endphp
                                    <td class="p-2 text-center border">
                                        <div class="{{ $color }} text-white rounded p-1 text-xs font-medium">
                                            {{ $score }}
                                        </div>
                                        @if($riskCount > 0)
                                        <div class="text-xs text-gray-600 mt-1">{{ $riskCount }}</div>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                            @endfor
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" class="p-2 text-xs text-gray-500 text-center bg-gray-50">
                                    <div class="flex justify-center items-center space-x-4">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-green-400 rounded mr-1"></div>
                                            <span>1-4: Sangat Rendah</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-yellow-400 rounded mr-1"></div>
                                            <span>5-9: Rendah</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-orange-400 rounded mr-1"></div>
                                            <span>10-14: Sedang</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-red-400 rounded mr-1"></div>
                                            <span>15-19: Tinggi</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-red-600 rounded mr-1"></div>
                                            <span>20-25: Sangat Tinggi</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Full Risk Matrix -->
<div class="modal" id="risk-matrix-modal">
    <div class="modal__content modal__content--xl">
        <div class="p-5">
            <h2 class="text-lg font-medium mb-4">Matriks Risiko Detail</h2>
            <div id="full-risk-matrix">
                <!-- Content will be loaded via AJAX -->
                <div class="text-center py-8">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-gray-500 mt-2">Memuat matriks risiko...</p>
                </div>
            </div>
            <div class="flex justify-end mt-6">
                <button onclick="closeRiskMatrixModal()" class="btn btn-outline-secondary w-24">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .line-clamp-2 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        line-clamp: 2;
    }
    
    .table-report td {
        vertical-align: middle;
    }
    
    .risk-matrix-preview table {
        border-collapse: separate;
        border-spacing: 2px;
    }
    
    .risk-matrix-preview th,
    .risk-matrix-preview td {
        border: 1px solid #e5e7eb;
    }
    
    .modal__content--xl {
        max-width: 90%;
        width: 90%;
    }
    
    .spinner-border {
        display: inline-block;
        width: 2rem;
        height: 2rem;
        border: 0.25em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spinner-border .75s linear infinite;
    }
    
    @keyframes spinner-border {
        to { transform: rotate(360deg); }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) closeBtn.click();
        });
    }, 5000);
    
    // Quick search on enter
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });
    }
});

// Load full risk matrix
function loadFullRiskMatrix() {
    const modal = document.getElementById('risk-matrix-modal');
    const content = document.getElementById('full-risk-matrix');
    
    // Show modal
    modal.style.display = 'flex';
    modal.classList.add('modal--show');
    
    // Load data via AJAX
    fetch('{{ route("risks.matrix") }}')
        .then(response => response.json())
        .then(data => {
            let html = '<div class="overflow-x-auto">';
            html += '<table class="w-full text-sm">';
            html += '<thead><tr><th class="p-3 text-center font-medium bg-gray-50" colspan="7">MATRIKS RISIKO - Tingkat Kemungkinan (Likelihood) vs Tingkat Dampak (Impact)</th></tr>';
            html += '<tr><th class="p-3 text-center font-medium bg-gray-50">Likelihood / Impact</th>';
            
            for (let impact = 1; impact <= 5; impact++) {
                html += `<th class="p-3 text-center font-medium bg-gray-50">Impact ${impact}</th>`;
            }
            
            html += '</tr></thead><tbody>';
            
            for (let likelihood = 5; likelihood >= 1; likelihood--) {
                html += `<tr><td class="p-3 text-center font-medium bg-gray-50">Likelihood ${likelihood}</td>`;
                
                for (let impact = 1; impact <= 5; impact++) {
                    const cell = data[likelihood][impact];
                    const textColor = cell.level === 'sangat_tinggi' ? 'text-white' : 'text-gray-800';
                    
                    html += `<td class="p-2 text-center border" style="background-color: ${cell.color}">`;
                    html += `<div class="${textColor} font-bold text-lg">${cell.score}</div>`;
                    html += `<div class="text-xs ${textColor}">${cell.level}</div>`;
                    if (cell.count > 0) {
                        html += `<div class="mt-2 p-1 bg-black bg-opacity-20 rounded ${textColor}">${cell.count} risiko</div>`;
                    }
                    html += '</td>';
                }
                
                html += '</tr>';
            }
            
            html += '</tbody></table></div>';
            
            // Add legend
            html += '<div class="mt-6 p-4 bg-gray-50 rounded-lg">';
            html += '<h4 class="font-medium mb-3">Keterangan Level Risiko:</h4>';
            html += '<div class="grid grid-cols-5 gap-4">';
            html += '<div class="text-center"><div class="h-4 bg-green-400 rounded mb-1"></div><span class="text-xs">Sangat Rendah (1-4)</span></div>';
            html += '<div class="text-center"><div class="h-4 bg-yellow-400 rounded mb-1"></div><span class="text-xs">Rendah (5-9)</span></div>';
            html += '<div class="text-center"><div class="h-4 bg-orange-400 rounded mb-1"></div><span class="text-xs">Sedang (10-14)</span></div>';
            html += '<div class="text-center"><div class="h-4 bg-red-400 rounded mb-1"></div><span class="text-xs">Tinggi (15-19)</span></div>';
            html += '<div class="text-center"><div class="h-4 bg-red-600 rounded mb-1"></div><span class="text-xs">Sangat Tinggi (20-25)</span></div>';
            html += '</div></div>';
            
            content.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading risk matrix:', error);
            content.innerHTML = '<div class="text-center py-8 text-red-500">Gagal memuat matriks risiko</div>';
        });
}

// Close risk matrix modal
function closeRiskMatrixModal() {
    const modal = document.getElementById('risk-matrix-modal');
    modal.style.display = 'none';
    modal.classList.remove('modal--show');
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('risk-matrix-modal');
    if (e.target === modal) {
        closeRiskMatrixModal();
    }
});
</script>
@endpush