@extends('layouts.master')

@section('title', 'Tujuan Strategis - SIMR')

@section('page-title', 'Manajemen Tujuan Strategis')

@section('page-action')
<a href="{{ route('strategic-objectives.create') }}" class="btn btn-primary shadow-md mr-2">
    <i data-feather="plus-circle" class="w-4 h-4 mr-2"></i> Tambah Tujuan
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Strategic Objectives List -->
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Daftar Tujuan Strategis
                    <span class="text-gray-500 text-sm ml-2">({{ $objectives->count() }} data)</span>
                </h2>
                <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                    <div class="dropdown">
                        <div class="dropdown-menu w-40">
                            <div class="dropdown-content">
                                <div class="p-2">
                                    <div class="form-check mt-2">
                                        <input type="checkbox" class="form-check-input column-toggle" id="col-risks" checked data-column="2">
                                        <label class="form-check-label" for="col-risks">Risiko</label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" class="form-check-input column-toggle" id="col-date" checked data-column="3">
                                        <label class="form-check-label" for="col-date">Tanggal</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-5">
                @if($objectives->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-report -mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">TUJUAN STRATEGIS</th>
                                    <th class="whitespace-nowrap">RISIKO TERKAIT</th>
                                    <th class="whitespace-nowrap">DIBUAT</th>
                                    <th class="whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($objectives as $objective)
                                    <tr class="intro-x hover:bg-gray-50">
                                        <td>
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full bg-theme-1/10 flex items-center justify-center mr-3">
                                                    <i data-feather="target" class="w-5 h-5 text-theme-1"></i>
                                                </div>
                                                <div>
                                                    <a href="{{ route('strategic-objectives.show', $objective->strategic_objective_id) }}" 
                                                       class="font-medium hover:text-theme-1">
                                                        {{ $objective->strategic_objective_name }}
                                                    </a>
                                                    <div class="text-gray-500 text-xs mt-0.5">
                                                        ID: {{ $objective->strategic_objective_id }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex items-center">
                                                @if(($objective->risks_count ?? 0) > 0)
                                                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center mr-2">
                                                        <i data-feather="alert-triangle" class="w-4 h-4 text-red-600"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium">{{ $objective->risks_count ?? 0 }}</div>
                                                        <div class="text-xs text-gray-500">risiko</div>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-sm text-gray-600">
                                                {{ \Carbon\Carbon::parse($objective->created_at)->format('d/m/Y') }}
                                            </div>
                                        </td>
                                        <td class="table-report__action w-56">
                                            <div class="flex justify-center items-center">
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('strategic-objectives.show', $objective->strategic_objective_id) }}">
                                                    <i data-feather="eye" class="w-4 h-4 mr-1"></i> Detail
                                                </a>
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('strategic-objectives.edit', $objective->strategic_objective_id) }}">
                                                    <i data-feather="edit" class="w-4 h-4 mr-1"></i> Edit
                                                </a>
                                                @if(($objective->risks_count ?? 0) == 0)
                                                <form method="POST" 
                                                      action="{{ route('strategic-objectives.destroy', $objective->strategic_objective_id) }}"
                                                      class="delete-form inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex items-center text-danger" 
                                                            onclick="return confirm('Hapus tujuan strategis ini?')">
                                                        <i data-feather="trash-2" class="w-4 h-4 mr-1"></i> Hapus
                                                    </button>
                                                </form>
                                                @endif
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
                            <i data-feather="target" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada tujuan strategis</h3>
                        <p class="text-gray-500 mb-6">Tujuan strategis digunakan untuk mengarahkan manajemen risiko</p>
                        <a href="{{ route('strategic-objectives.create') }}" 
                           class="btn btn-primary">
                            <i data-feather="plus-circle" class="w-4 h-4 mr-2"></i> Tambah Tujuan Strategis Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.table-report__action a {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    transition: all 0.2s;
}

.table-report__action a:hover {
    background-color: rgba(59, 130, 246, 0.1);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Column toggle functionality
    document.querySelectorAll('.column-toggle').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const columnIndex = parseInt(this.dataset.column);
            const table = document.querySelector('.table-report');
            const rows = table.querySelectorAll('tr');
            
            rows.forEach(row => {
                const cell = row.cells[columnIndex];
                if (cell) {
                    cell.style.display = this.checked ? '' : 'none';
                }
            });
        });
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) closeBtn.click();
        });
    }, 5000);
});
</script>
@endpush