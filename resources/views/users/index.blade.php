@extends('layouts.master')

@section('title', 'Kelola Pengguna - SIMR')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">
        Kelola Pengguna
        <span class="text-sm text-slate-500 font-normal ml-2">Manajemen Akses Sistem</span>
    </h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <a href="{{ route('users.create') }}" class="btn btn-primary shadow-md mr-2">
            <i data-feather="user-plus" class="w-4 h-4 mr-2"></i> Tambah Pengguna
        </a>
    </div>
</div>

<!-- User Statistics -->
<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-100">
                        <i data-feather="users" class="w-6 h-6 text-blue-600"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold leading-8 mt-6">{{ $totalUsers }}</div>
                <div class="text-base text-gray-600 mt-1">Total Pengguna</div>
            </div>
        </div>
    </div>
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-100">
                        <i data-feather="user-check" class="w-6 h-6 text-green-600"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold leading-8 mt-6">{{ $activeUsers }}</div>
                <div class="text-base text-gray-600 mt-1">Aktif</div>
            </div>
        </div>
    </div>
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-yellow-100">
                        <i data-feather="user-x" class="w-6 h-6 text-yellow-600"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold leading-8 mt-6">{{ $inactiveUsers }}</div>
                <div class="text-base text-gray-600 mt-1">Nonaktif</div>
            </div>
        </div>
    </div>
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-purple-100">
                        <i data-feather="clock" class="w-6 h-6 text-purple-600"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold leading-8 mt-6">{{ $newUsersMonth }}</div>
                <div class="text-base text-gray-600 mt-1">Baru Bulan Ini</div>
            </div>
        </div>
    </div>
</div>

<!-- User Role Distribution -->
<div class="grid grid-cols-12 gap-6 mt-5">
    <!-- Role Distribution -->
    <div class="col-span-12 lg:col-span-4">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-slate-200/60">
                <h2 class="font-medium text-base mr-auto">Distribusi Peran</h2>
            </div>
            <div class="p-5">
                @foreach($roleDistribution as $role)
                <div class="flex items-center mb-3">
                    @php
                        $roleColors = [
                            'admin' => '#3b82f6',
                            'unit_pemilik_risiko' => '#10b981',
                            'auditor' => '#f59e0b'  
                        ];
                        $roleNames = [
                            'admin' => 'Administrator',
                            'unit_pemilik_risiko' => 'Unit Pemilik Risiko',
                            'auditor' => 'Auditor'  
                        ];
                    @endphp
                    <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $roleColors[$role->role] ?? '#6c757d' }}"></div>
                    <div class="flex-1">
                        <div class="flex justify-between">
                            <span class="text-sm">{{ $roleNames[$role->role] ?? ucfirst($role->role) }}</span>
                            <span class="text-sm font-medium">{{ $role->count }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1">
                            <div class="bg-primary rounded-full h-1.5" style="width: {{ ($role->count / max($totalUsers, 1)) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- User List -->
    <div class="col-span-12 lg:col-span-8">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-slate-200/60">
                <h2 class="font-medium text-base mr-auto">Daftar Pengguna</h2>
                <div class="w-full sm:w-auto flex">
                    <div class="relative">
                        <i data-feather="search" class="w-4 h-4 absolute my-auto inset-y-0 ml-3 left-0 z-10 text-slate-500"></i>
                        <input type="text" class="form-control w-full sm:w-64 box pl-10" placeholder="Cari pengguna...">
                    </div>
                </div>
            </div>
            <div class="p-5">
                <div class="overflow-x-auto">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap">Nama</th>
                                <th class="whitespace-nowrap">Email</th>
                                <th class="whitespace-nowrap">Peran</th>
                                <th class="whitespace-nowrap">Status</th>
                                <th class="whitespace-nowrap">Terakhir Login</th>
                                <th class="whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="flex items-center">
                                        <div class="ml-4">
                                            <div class="font-medium">{{ $user->name }}</div>
                                            <div class="text-gray-600 text-xs mt-0.5">
                                                {{ $user->username ?? $user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @php
                                        $roleBadges = [
                                            'admin' => 'badge-primary',
                                            'unit_pemilik_risiko' => 'badge-success',
                                            'auditor' => 'badge-warning'
                                        ];
                                        $roleNames = [
                                            'admin' => 'Administrator',
                                            'unit_pemilik_risiko' => 'Unit Pemilik Risiko',
                                            'auditor' => 'Auditor'
                                        ];
                                    @endphp
                                    <span class="badge {{ $roleBadges[$user->role] ?? 'badge-secondary' }}">
                                        {{ $roleNames[$user->role] ?? ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                    @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <!-- GANTI last_login_at dengan created_at atau updated_at -->
                                    {{ $user->created_at->format('d/m/Y') }}
                                    <div class="text-xs text-gray-500">
                                        Bergabung: {{ $user->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td>
                                    <div class="flex space-x-1">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                            <i data-feather="edit" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Hapus pengguna ini?')" title="Hapus">
                                                <i data-feather="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-8">
                                    <div class="text-gray-500">
                                        <i data-feather="users" class="w-12 h-12 mx-auto mb-4"></i>
                                        <p>Belum ada pengguna</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($users->hasPages())
                <div class="mt-5">
                    {{ $users->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function refreshPage() {
    location.reload();
}

function exportUsers() {
    // Implement export functionality
    alert('Export pengguna ke CSV/Excel coming soon');
}

function bulkActions() {
    // Implement bulk actions
    alert('Aksi massal pengguna coming soon');
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality
    const searchInput = document.querySelector('input[type="text"]');
    searchInput?.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            const searchTerm = this.value;
            // Implement search functionality
            console.log('Search for:', searchTerm);
        }
    });
});
</script>
@endsection