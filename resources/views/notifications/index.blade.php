@extends('layouts.master')

@section('title', 'Notifikasi - SIMR')

@section('page-title', 'Manajemen Notifikasi')

@section('breadcrumb')
@parent
<li class="breadcrumb-item active">Notifikasi</li>
@endsection

@section('page-action')
<button type="button" class="btn btn-outline-secondary shadow-md" onclick="markAllAsRead()">
    <i data-feather="check-circle" class="w-4 h-4 mr-2"></i> Tandai Semua Dibaca
</button>
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
                                <i data-feather="bell" class="w-6 h-6 text-blue-600"></i>
                            </div>
                            <div class="ml-auto">
                                <div class="text-3xl font-bold leading-8">{{ $counts['total'] ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Total Notifikasi</div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-red-100">
                                <i data-feather="alert-triangle" class="w-6 h-6 text-red-600"></i>
                            </div>
                            <div class="ml-auto">
                                <div class="text-3xl font-bold leading-8">{{ $counts['overdue_mitigations'] ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Mitigasi Terlambat</div>
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
                                <div class="text-3xl font-bold leading-8">{{ $counts['high_risk_no_mitigation'] ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Risiko Tinggi</div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-purple-100">
                                <i data-feather="calendar" class="w-6 h-6 text-purple-600"></i>
                            </div>
                            <div class="ml-auto">
                                <div class="text-3xl font-bold leading-8">{{ $counts['due_schedules'] ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="text-base text-gray-600 mt-1">Jadwal Mendatang</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="bell" class="w-5 h-5 mr-2 text-blue-500"></i>
                    Semua Notifikasi
                </h2>
                <div class="text-gray-500 text-sm">
                    {{ is_array($notifications) ? count($notifications) : 0 }} kategori
                </div>
            </div>
            <div class="p-5">
                @if(($counts['total'] ?? 0) == 0)
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="check-circle" class="w-8 h-8 text-green-600"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Tidak ada notifikasi</h3>
                        <p class="text-gray-500 mb-6">Semua sistem berjalan dengan baik</p>
                    </div>
                @elseif(!is_array($notifications) || empty($notifications))
                    <!-- Error State -->
                    <div class="text-center py-12">
                        <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="alert-circle" class="w-8 h-8 text-red-600"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Gagal memuat notifikasi</h3>
                        <p class="text-gray-500 mb-6">Terjadi kesalahan saat memuat data</p>
                    </div>
                @else
                    <!-- Notifications Categories -->
                    <div class="space-y-6">
                        @foreach($notifications as $key => $notification)
                            @if(isset($notification['count']) && $notification['count'] > 0)
                            <div class="border rounded-lg overflow-hidden">
                                <!-- Notification Header -->
                                <div class="flex items-center justify-between p-4 bg-gray-50 border-b">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full {{ $notification['icon_bg'] ?? 'bg-gray-100' }} flex items-center justify-center mr-3">
                                            <i data-feather="{{ $notification['icon'] ?? 'alert-circle' }}" class="w-5 h-5 {{ $notification['icon_color'] ?? 'text-gray-600' }}"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-medium text-gray-700">{{ $notification['title'] ?? 'Notifikasi' }}</h3>
                                            <p class="text-sm text-gray-600">{{ $notification['description'] ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium 
                                            @if(($notification['type'] ?? 'info') == 'danger') bg-red-100 text-red-800
                                            @elseif(($notification['type'] ?? 'info') == 'warning') bg-orange-100 text-orange-800
                                            @elseif(($notification['type'] ?? 'info') == 'info') bg-blue-100 text-blue-800
                                            @elseif(($notification['type'] ?? 'info') == 'primary') bg-purple-100 text-purple-800
                                            @elseif(($notification['type'] ?? 'info') == 'secondary') bg-teal-100 text-teal-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $notification['count'] ?? 0 }} item
                                        </span>
                                        <a href="{{ $notification['url'] ?? '#' }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                            Lihat
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Notification Items -->
                                <div class="p-4">
                                    @if(!empty($notification['items']) && is_array($notification['items']) && count($notification['items']) > 0)
                                        <div class="space-y-3">
                                            @foreach($notification['items'] as $item)
                                            <div class="flex items-center justify-between p-3 bg-white rounded-lg border hover:bg-gray-50">
                                                <div class="flex-1">
                                                    <div class="font-medium text-gray-700">{{ $item['title'] ?? 'Item' }}</div>
                                                    @if(isset($item['description']))
                                                    <div class="text-sm text-gray-600 mt-1">{{ $item['description'] }}</div>
                                                    @endif
                                                    @if(isset($item['deadline']))
                                                    <div class="text-xs text-red-600 mt-1">
                                                        <i data-feather="clock" class="w-3 h-3 inline mr-1"></i>
                                                        Deadline: {{ $item['deadline'] }}
                                                        @if(isset($item['days_overdue']))
                                                        <span class="ml-2">(Terlambat {{ $item['days_overdue'] }} hari)</span>
                                                        @endif
                                                    </div>
                                                    @endif
                                                    @if(isset($item['responsible']))
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        <i data-feather="user" class="w-3 h-3 inline mr-1"></i>
                                                        {{ $item['responsible'] }}
                                                    </div>
                                                    @endif
                                                    @if(isset($item['risk_level']))
                                                    <div class="text-xs {{ $item['risk_level'] == 'tinggi' || $item['risk_level'] == 'sangat_tinggi' ? 'text-red-600' : 'text-orange-600' }} mt-1">
                                                        <i data-feather="alert-circle" class="w-3 h-3 inline mr-1"></i>
                                                        Level: {{ $item['risk_level_label'] ?? $item['risk_level'] }}
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <a href="{{ $item['url'] ?? '#' }}" class="btn btn-outline-primary btn-sm">
                                                        <i data-feather="eye" class="w-3 h-3"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        
                                        @if(count($notification['items']) < ($notification['count'] ?? 0))
                                        <div class="text-center mt-4">
                                            <a href="{{ $notification['url'] ?? '#' }}" class="text-blue-600 text-sm hover:text-blue-800">
                                                Lihat {{ ($notification['count'] ?? 0) - count($notification['items']) }} item lainnya →
                                            </a>
                                        </div>
                                        @endif
                                    @else
                                        <div class="text-center py-4 text-gray-500">
                                            Tidak ada item detail
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        @endforeach
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
    // Initialize Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Load notification settings
    loadNotificationSettings();
});

function loadNotificationSettings() {
    // Load from localStorage or API
    const settings = JSON.parse(localStorage.getItem('notification_settings') || '{}');
    
    document.getElementById('email_notifications').checked = settings.email_notifications !== false;
    document.getElementById('push_notifications').checked = settings.push_notifications !== false;
    document.getElementById('overdue_mitigations').checked = settings.overdue_mitigations !== false;
    document.getElementById('high_risk_notifications').checked = settings.high_risk_notifications !== false;
    document.getElementById('schedule_notifications').checked = settings.schedule_notifications !== false;
}

function saveNotificationSettings() {
    const settings = {
        email_notifications: document.getElementById('email_notifications').checked,
        push_notifications: document.getElementById('push_notifications').checked,
        overdue_mitigations: document.getElementById('overdue_mitigations').checked,
        high_risk_notifications: document.getElementById('high_risk_notifications').checked,
        schedule_notifications: document.getElementById('schedule_notifications').checked,
    };
    
    localStorage.setItem('notification_settings', JSON.stringify(settings));
    
    // Show success message
    showToast('success', 'Pengaturan notifikasi berhasil disimpan');
}

function markAllAsRead() {
    fetch('/api/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Semua notifikasi telah ditandai sebagai dibaca');
            setTimeout(() => {
                location.reload();
            }, 1500);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Gagal menandai notifikasi sebagai dibaca');
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
    
    // Replace Feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
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
    animation: slideIn 0.3s ease forwards;
}

@keyframes slideIn {
    from {
        transform: translateX(150%);
    }
    to {
        transform: translateX(0);
    }
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

.toast--success {
    border-left: 4px solid #10b981;
}

.toast--error {
    border-left: 4px solid #ef4444;
}

.toast--warning {
    border-left: 4px solid #f59e0b;
}

.toast--info {
    border-left: 4px solid #3b82f6;
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