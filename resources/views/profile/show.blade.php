@extends('layouts.master')

@section('title', 'Profil - SIMR')

@section('page-title', 'Profil Pengguna')

@section('breadcrumb')
@parent
<li class="breadcrumb-item active">Profil</li>
@endsection

@section('page-action')
<a href="{{ route('profile.edit') }}" class="btn btn-primary shadow-md">
    <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit Profil
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 lg:col-span-4">
        <!-- Profile Card -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="user" class="w-5 h-5 mr-2 text-blue-500"></i>
                    Informasi Profil
                </h2>
            </div>
            <div class="p-5">
                <!-- Avatar -->
                <div class="text-center mb-6">
                    <div class="w-32 h-32 mx-auto mb-4 rounded-full overflow-hidden border-4 border-white shadow-lg">
                        @if($user->avatar && file_exists(public_path('storage/' . $user->avatar)))
                            <img src="{{ asset('storage/' . $user->avatar) }}" 
                                 alt="{{ $user->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                <span class="text-white text-4xl font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">{{ $user->name }}</h3>
                    <p class="text-gray-600">{{ $user->email }}</p>
                    <span class="inline-block mt-2 px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ $user->role_name }}
                    </span>
                </div>
                
                <!-- User Info -->
                <div class="space-y-4">
                    @if($user->phone)
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i data-feather="phone" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Telepon</div>
                            <div class="font-medium">{{ $user->phone }}</div>
                        </div>
                    </div>
                    @endif
                    
                    @if($user->address)
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                            <i data-feather="map-pin" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Alamat</div>
                            <div class="font-medium">{{ $user->address }}</div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                            <i data-feather="calendar" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Bergabung</div>
                            <div class="font-medium">{{ $user->created_at->format('d F Y') }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                            <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit
                        </a>
                        <button type="button" 
                                class="btn btn-outline-danger" 
                                data-tw-toggle="modal" 
                                data-tw-target="#delete-account-modal">
                            <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistics Card -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="bar-chart" class="w-5 h-5 mr-2 text-green-500"></i>
                    Statistik
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center mr-3">
                                <i data-feather="alert-triangle" class="w-4 h-4 text-orange-600"></i>
                            </div>
                            <div>
                                <div class="font-medium">Risiko</div>
                                <div class="text-sm text-gray-600">Total risiko dibuat</div>
                            </div>
                        </div>
                        <div class="text-2xl font-bold">{{ $stats['total_risks'] ?? 0 }}</div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                <i data-feather="alert-triangle" class="w-4 h-4 text-red-600"></i>
                            </div>
                            <div>
                                <div class="font-medium">Risiko Tinggi</div>
                                <div class="text-sm text-gray-600">Risiko tinggi/tinggi sekali</div>
                            </div>
                        </div>
                        <div class="text-2xl font-bold">{{ $stats['high_risks'] ?? 0 }}</div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i data-feather="shield" class="w-4 h-4 text-blue-600"></i>
                            </div>
                            <div>
                                <div class="font-medium">Mitigasi</div>
                                <div class="text-sm text-gray-600">Total mitigasi aktif</div>
                            </div>
                        </div>
                        <div class="text-2xl font-bold">{{ $stats['active_mitigations'] ?? 0 }}</div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                <i data-feather="file-text" class="w-4 h-4 text-green-600"></i>
                            </div>
                            <div>
                                <div class="font-medium">Laporan</div>
                                <div class="text-sm text-gray-600">Laporan dibuat</div>
                            </div>
                        </div>
                        <div class="text-2xl font-bold">{{ $stats['generated_reports'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-span-12 lg:col-span-8">
        <!-- Recent Activity -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="activity" class="w-5 h-5 mr-2 text-purple-500"></i>
                    Aktivitas Terbaru
                </h2>
            </div>
            <div class="p-5">
                @if(!empty($recentActivity))
                    <div class="space-y-4">
                        @foreach($recentActivity as $activity)
                        <div class="flex items-start p-3 rounded-lg border hover:bg-gray-50">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3 {{ $activity['icon_color'] }} bg-opacity-20">
                                <i data-feather="{{ $activity['icon'] }}" class="w-5 h-5"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-700">{{ $activity['title'] }}</div>
                                <div class="text-sm text-gray-600 mt-1">{{ $activity['description'] }}</div>
                                <div class="text-xs text-gray-500 mt-2">
                                    {{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}
                                </div>
                            </div>
                            <div class="ml-4">
                                <a href="{{ $activity['url'] }}" class="btn btn-outline-primary btn-sm">
                                    <i data-feather="eye" class="w-3 h-3"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i data-feather="activity" class="w-12 h-12 mx-auto mb-3"></i>
                        <p>Belum ada aktivitas</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Account Security -->
        <div class="intro-y box mt-6 border-red-200">
            <div class="flex items-center p-5 border-b border-red-200 bg-red-50">
                <h2 class="font-medium text-base mr-auto text-red-700">
                    <i data-feather="shield" class="w-5 h-5 mr-2 text-red-600"></i>
                    Keamanan Akun
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    <!-- Change Password -->
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Ubah Password</h4>
                        <p class="text-sm text-gray-600 mb-3">
                            Pastikan password Anda kuat dan sulit ditebak.
                        </p>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                            <i data-feather="key" class="w-4 h-4 mr-2"></i> Ubah Password
                        </a>
                    </div>
                    
                    <!-- Delete Account -->
                    <div class="pt-4 border-t border-gray-200">
                        <h4 class="font-medium text-red-700 mb-2">Hapus Akun</h4>
                        <p class="text-sm text-gray-600 mb-3">
                            Hapus akun Anda dan semua data yang terkait. Tindakan ini tidak dapat dibatalkan.
                        </p>
                        <button type="button" 
                                class="btn btn-outline-danger" 
                                data-tw-toggle="modal" 
                                data-tw-target="#delete-account-modal">
                            <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus Akun
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal" id="delete-account-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Hapus Akun</h2>
            </div>
            <form action="{{ route('profile.destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-3">
                            <i data-feather="alert-triangle" class="w-8 h-8 text-red-600"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-800 mb-2">Apakah Anda yakin?</h3>
                        <p class="text-gray-600 mb-4">
                            Tindakan ini akan menghapus akun Anda secara permanen. 
                            Semua data yang terkait dengan akun ini akan hilang.
                        </p>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="password" class="form-label">Password Saat Ini</label>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   class="form-control" 
                                   placeholder="Masukkan password Anda"
                                   required>
                        </div>
                        
                        <div>
                            <label for="confirmation" class="form-label">
                                Ketik <strong>DELETE</strong> untuk konfirmasi
                            </label>
                            <input type="text" 
                                   name="confirmation" 
                                   id="confirmation" 
                                   class="form-control" 
                                   placeholder="Ketik DELETE"
                                   required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-danger w-20">
                        Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Load preferences
    loadPreferences();
});

function loadPreferences() {
    fetch('{{ route("profile.get-preferences") }}')
        .then(response => response.json())
        .then(data => {
            // Set theme
            if (data.theme) {
                document.querySelector('select[name="theme"]').value = data.theme;
            }
            
            // Set language
            if (data.language) {
                document.querySelector('select[name="language"]').value = data.language;
            }
            
            // Set timezone
            if (data.timezone) {
                document.querySelector('select[name="timezone"]').value = data.timezone;
            }
            
            // Set date format
            if (data.date_format) {
                document.querySelector('select[name="date_format"]').value = data.date_format;
            }
            
            // Set time format
            if (data.time_format) {
                document.querySelector(`input[name="time_format"][value="${data.time_format}"]`).checked = true;
            }
            
            // Set notifications
            if (data.notifications) {
                document.querySelector('input[name="notifications_email"]').checked = data.notifications.email || false;
                document.querySelector('input[name="notifications_push"]').checked = data.notifications.push || false;
            }
        })
        .catch(error => {
            console.error('Error loading preferences:', error);
        });
}

function savePreferences() {
    const formData = new FormData(document.getElementById('preferences-form'));
    const data = {
        theme: formData.get('theme'),
        language: formData.get('language'),
        timezone: formData.get('timezone'),
        date_format: formData.get('date_format'),
        time_format: formData.get('time_format'),
        notifications_email: document.querySelector('input[name="notifications_email"]').checked,
        notifications_push: document.querySelector('input[name="notifications_push"]').checked,
    };
    
    fetch('{{ route("profile.update-preferences") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Preferensi berhasil disimpan');
            
            // Apply theme immediately
            if (data.theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else if (data.theme === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                // Follow system preference
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
        } else {
            showToast('error', data.message || 'Gagal menyimpan preferensi');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan saat menyimpan preferensi');
    });
}

function showToast(type, message) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast--${type} show`;
    toast.innerHTML = `
        <div class="toast__icon">
            <i data-feather="${type === 'success' ? 'check-circle' : 'alert-circle'}"></i>
        </div>
        <div class="toast__content">${message}</div>
        <div class="toast__close">
            <i data-feather="x"></i>
        </div>
    `;
    
    document.body.appendChild(toast);
    feather.replace();
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.remove();
    }, 5000);
    
    // Close on click
    toast.querySelector('.toast__close').addEventListener('click', () => {
        toast.remove();
    });
}
</script>

<style>
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    padding: 16px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    display: flex;
    align-items: center;
    min-width: 300px;
    transform: translateX(150%);
    transition: transform 0.3s ease;
}

.toast.show {
    transform: translateX(0);
}

.toast__icon {
    margin-right: 12px;
}

.toast__content {
    flex: 1;
}

.toast__close {
    margin-left: 12px;
    cursor: pointer;
    opacity: 0.7;
}

.toast__close:hover {
    opacity: 1;
}

.toast--success .toast__icon {
    color: #10b981;
}

.toast--error .toast__icon {
    color: #ef4444;
}

.toast--warning .toast__icon {
    color: #f59e0b;
}

.toast--info .toast__icon {
    color: #3b82f6;
}
</style>
@endpush