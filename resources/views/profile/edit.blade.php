@extends('layouts.master')

@section('title', 'Edit Profil - SIMR')

@section('page-title', 'Edit Profil')

@section('breadcrumb')
@parent
<li class="breadcrumb-item"><a href="{{ route('profile.show') }}">Profil</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Edit Form -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="edit" class="w-5 h-5 mr-2 text-blue-500"></i>
                    Edit Profil
                </h2>
            </div>
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="p-5">
                    <!-- Avatar Upload -->
                    <div class="mb-8">
                        <label class="form-label">Foto Profil</label>
                        <div class="flex flex-col sm:flex-row items-center">
                            <div class="mr-5">
                                <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-white shadow-lg">
                                    @if($user->avatar && file_exists(public_path('storage/' . $user->avatar)))
                                        <img id="avatar-preview" 
                                             src="{{ asset('storage/' . $user->avatar) }}" 
                                             alt="{{ $user->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div id="avatar-preview" 
                                             class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                            <span class="text-white text-2xl font-bold">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="space-y-3">
                                    <input type="file" 
                                           id="avatar" 
                                           name="avatar" 
                                           class="form-control-file"
                                           accept="image/*"
                                           onchange="previewAvatar(event)">
                                    <div class="text-gray-500 text-sm">
                                        Format: JPG, PNG, GIF (max 2MB)
                                    </div>
                                    @if($user->avatar)
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               id="remove_avatar" 
                                               name="remove_avatar" 
                                               value="1"
                                               class="form-check-input mr-2">
                                        <label for="remove_avatar" class="text-red-600 text-sm">
                                            Hapus foto profil
                                        </label>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Basic Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">
                            Informasi Dasar
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       class="form-control w-full @error('name') border-red-500 @enderror" 
                                       value="{{ old('name', $user->name) }}"
                                       required>
                                @error('name')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label for="email" class="form-label">Email <span class="text-red-500">*</span></label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       class="form-control w-full @error('email') border-red-500 @enderror" 
                                       value="{{ old('email', $user->email) }}"
                                       required>
                                @error('email')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Phone -->
                            <div>
                                <label for="phone" class="form-label">Telepon</label>
                                <input type="text" 
                                       id="phone" 
                                       name="phone" 
                                       class="form-control w-full @error('phone') border-red-500 @enderror" 
                                       value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Address -->
                            <div>
                                <label for="address" class="form-label">Alamat</label>
                                <textarea id="address" 
                                          name="address" 
                                          class="form-control w-full @error('address') border-red-500 @enderror" 
                                          rows="3">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Password Change -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">
                            Ubah Password
                        </h3>
                        <p class="text-gray-600 mb-4 text-sm">
                            Kosongkan jika tidak ingin mengubah password
                        </p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Current Password -->
                            <div>
                                <label for="current_password" class="form-label">Password Saat Ini</label>
                                <input type="password" 
                                       id="current_password" 
                                       name="current_password" 
                                       class="form-control w-full @error('current_password') border-red-500 @enderror">
                                @error('current_password')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- New Password -->
                            <div>
                                <label for="new_password" class="form-label">Password Baru</label>
                                <input type="password" 
                                       id="new_password" 
                                       name="new_password" 
                                       class="form-control w-full @error('new_password') border-red-500 @enderror">
                                @error('new_password')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Confirm Password -->
                            <div>
                                <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" 
                                       id="new_password_confirmation" 
                                       name="new_password_confirmation" 
                                       class="form-control w-full">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                            <i data-feather="x" class="w-4 h-4 mr-2"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="w-4 h-4 mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Help Card -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="help-circle" class="w-5 h-5 mr-2 text-purple-500"></i>
                    Tips Keamanan
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
    <div class="flex items-start">
        <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center mr-3 flex-shrink-0">
            <i data-feather="shield" class="w-3 h-3 text-purple-600"></i>
        </div>
        <p class="text-sm text-gray-600">
            Pastikan foto profil sesuai dengan identitas Anda dan mudah dikenali.
        </p>
    </div>
    
    <div class="flex items-start">
        <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center mr-3 flex-shrink-0">
            <i data-feather="shield" class="w-3 h-3 text-purple-600"></i>
        </div>
        <p class="text-sm text-gray-600">
            Gunakan alamat email aktif yang dapat diakses untuk verifikasi dan pemulihan akun.
        </p>
    </div>
    
    <div class="flex items-start">
        <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center mr-3 flex-shrink-0">
            <i data-feather="shield" class="w-3 h-3 text-purple-600"></i>
        </div>
        <p class="text-sm text-gray-600">
            Untuk keamanan yang lebih baik, gunakan password yang kuat dengan kombinasi huruf besar, huruf kecil, angka, dan simbol.
        </p>
    </div>
    
    <div class="flex items-start">
        <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center mr-3 flex-shrink-0">
            <i data-feather="shield" class="w-3 h-3 text-purple-600"></i>
        </div>
        <p class="text-sm text-gray-600">
            Selalu perbarui informasi kontak Anda agar tetap dapat menerima notifikasi penting.
        </p>
    </div>
</div>
            </div>
        </div>
        
        <!-- Account Information -->
        <div class="intro-y box mt-6 border-blue-200">
            <div class="flex items-center p-5 border-b border-blue-200 bg-blue-50">
                <h2 class="font-medium text-base mr-auto text-blue-700">
                    <i data-feather="info" class="w-5 h-5 mr-2 text-blue-600"></i>
                    Informasi Akun
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- User ID -->
                    <div>
                        <label class="form-label text-gray-500">ID Pengguna</label>
                        <div class="font-medium text-gray-700">{{ $user->id }}</div>
                    </div>
                    
                    <!-- Role -->
                    <div>
                        <label class="form-label text-gray-500">Peran</label>
                        <div>
                            <span class="inline-block px-2 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $user->role_name }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Email Verified -->
                    <div>
                        <label class="form-label text-gray-500">Status Verifikasi Email</label>
                        <div>
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center text-green-600">
                                    <i data-feather="check-circle" class="w-4 h-4 mr-1"></i>
                                    Terverifikasi
                                </span>
                            @else
                                <span class="inline-flex items-center text-red-600">
                                    <i data-feather="x-circle" class="w-4 h-4 mr-1"></i>
                                    Belum diverifikasi
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Last Login -->
                    <div>
                        <label class="form-label text-gray-500">Login Terakhir</label>
                        <div class="font-medium text-gray-700">
                            @if($user->last_login_at)
                                {{ \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() }}
                            @else
                                Belum pernah login
                            @endif
                        </div>
                    </div>
                    
                    <!-- Created At -->
                    <div>
                        <label class="form-label text-gray-500">Tanggal Bergabung</label>
                        <div class="font-medium text-gray-700">
                            {{ $user->created_at->format('d F Y H:i') }}
                        </div>
                    </div>
                    
                    <!-- Updated At -->
                    <div>
                        <label class="form-label text-gray-500">Terakhir Diperbarui</label>
                        <div class="font-medium text-gray-700">
                            {{ $user->updated_at->format('d F Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password Strength Modal -->
<div class="modal" id="password-strength-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Tips Password yang Kuat</h2>
            </div>
            <div class="modal-body">
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Kriteria Password yang Baik:</h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start">
                                <i data-feather="check" class="w-4 h-4 mr-2 text-green-500 mt-0.5 flex-shrink-0"></i>
                                <span>Minimal 8 karakter</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="check" class="w-4 h-4 mr-2 text-green-500 mt-0.5 flex-shrink-0"></i>
                                <span>Kombinasi huruf besar dan kecil</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="check" class="w-4 h-4 mr-2 text-green-500 mt-0.5 flex-shrink-0"></i>
                                <span>Minimal satu angka</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="check" class="w-4 h-4 mr-2 text-green-500 mt-0.5 flex-shrink-0"></i>
                                <span>Minimal satu simbol</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="check" class="w-4 h-4 mr-2 text-green-500 mt-0.5 flex-shrink-0"></i>
                                <span>Tidak mengandung informasi pribadi</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Contoh Password yang Baik:</h4>
                        <div class="bg-gray-50 p-3 rounded">
                            <code class="text-sm">S!mR@2024#Secure</code>
                        </div>
                    </div>
                    
                    <div class="border-t pt-4">
                        <h4 class="font-medium text-gray-700 mb-2">Apa yang Harus Dihindari:</h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start">
                                <i data-feather="x" class="w-4 h-4 mr-2 text-red-500 mt-0.5 flex-shrink-0"></i>
                                <span>Password yang mudah ditebak (123456, password, dll)</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="x" class="w-4 h-4 mr-2 text-red-500 mt-0.5 flex-shrink-0"></i>
                                <span>Informasi pribadi (tanggal lahir, nama, dll)</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="x" class="w-4 h-4 mr-2 text-red-500 mt-0.5 flex-shrink-0"></i>
                                <span>Kata-kata umum yang ada di kamus</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="x" class="w-4 h-4 mr-2 text-red-500 mt-0.5 flex-shrink-0"></i>
                                <span>Sequence keyboard (qwerty, asdfgh, dll)</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-tw-dismiss="modal" class="btn btn-primary w-20">
                    Mengerti
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Initialize form validation
    initFormValidation();
    
    // Initialize password strength checker
    initPasswordStrengthChecker();
});

function previewAvatar(event) {
    const input = event.target;
    const preview = document.getElementById('avatar-preview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            // Create image element
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'w-full h-full object-cover';
            img.alt = 'Preview avatar';
            
            // Replace preview content
            preview.innerHTML = '';
            preview.appendChild(img);
        }
        
        reader.readAsDataURL(input.files[0]);
        
        // Validate file size (2MB)
        if (input.files[0].size > 2 * 1024 * 1024) {
            showToast('error', 'Ukuran file terlalu besar. Maksimal 2MB.');
            input.value = '';
            
            // Restore original preview
            restoreAvatarPreview();
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(input.files[0].type)) {
            showToast('error', 'Format file tidak didukung. Gunakan JPG, PNG, atau GIF.');
            input.value = '';
            
            // Restore original preview
            restoreAvatarPreview();
        }
    }
}

function restoreAvatarPreview() {
    const preview = document.getElementById('avatar-preview');
    const userAvatar = "{{ $user->avatar }}";
    const userName = "{{ $user->name }}";
    
    if (userAvatar && "{{ file_exists(public_path('storage/' . $user->avatar)) ? 'true' : 'false' }}" === 'true') {
        const img = document.createElement('img');
        img.src = "{{ asset('storage/' . $user->avatar) }}";
        img.className = 'w-full h-full object-cover';
        img.alt = userName;
        preview.innerHTML = '';
        preview.appendChild(img);
    } else {
        preview.innerHTML = `
            <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                <span class="text-white text-2xl font-bold">
                    ${userName.charAt(0).toUpperCase()}
                </span>
            </div>
        `;
    }
}

function initFormValidation() {
    const form = document.querySelector('form');
    const passwordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('new_password_confirmation');
    
    if (form) {
        form.addEventListener('submit', function(event) {
            // Validate email format
            const emailInput = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!emailRegex.test(emailInput.value)) {
                event.preventDefault();
                showToast('error', 'Format email tidak valid');
                emailInput.focus();
                return false;
            }
            
            // Validate phone format (if provided)
            const phoneInput = document.getElementById('phone');
            if (phoneInput.value.trim() !== '') {
                const phoneRegex = /^[\d\s\-\+\(\)]+$/;
                if (!phoneRegex.test(phoneInput.value)) {
                    event.preventDefault();
                    showToast('error', 'Format telepon tidak valid');
                    phoneInput.focus();
                    return false;
                }
            }
            
            // Validate password match (if new password is provided)
            if (passwordInput.value.trim() !== '') {
                if (passwordInput.value !== confirmPasswordInput.value) {
                    event.preventDefault();
                    showToast('error', 'Password baru dan konfirmasi password tidak cocok');
                    confirmPasswordInput.focus();
                    return false;
                }
                
                // Validate password strength
                if (!validatePasswordStrength(passwordInput.value)) {
                    event.preventDefault();
                    showToast('warning', 'Password terlalu lemah. Gunakan kombinasi huruf besar, kecil, angka, dan simbol.');
                    passwordInput.focus();
                    return false;
                }
            }
            
            // Validate avatar size and type
            const avatarInput = document.getElementById('avatar');
            if (avatarInput.files && avatarInput.files[0]) {
                if (avatarInput.files[0].size > 2 * 1024 * 1024) {
                    event.preventDefault();
                    showToast('error', 'Ukuran file avatar terlalu besar. Maksimal 2MB.');
                    return false;
                }
                
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(avatarInput.files[0].type)) {
                    event.preventDefault();
                    showToast('error', 'Format file avatar tidak didukung. Gunakan JPG, PNG, atau GIF.');
                    return false;
                }
            }
            
            // Show loading state
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = `
                <i data-feather="loader" class="w-4 h-4 mr-2 animate-spin"></i>
                Menyimpan...
            `;
            submitButton.disabled = true;
            feather.replace();
        });
    }
}

function initPasswordStrengthChecker() {
    const passwordInput = document.getElementById('new_password');
    const strengthIndicator = document.createElement('div');
    strengthIndicator.className = 'mt-2';
    
    if (passwordInput) {
        passwordInput.parentNode.appendChild(strengthIndicator);
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            
            if (password.trim() === '') {
                strengthIndicator.innerHTML = '';
                return;
            }
            
            let strengthText = '';
            let strengthClass = '';
            
            switch(strength.level) {
                case 'very-weak':
                    strengthText = 'Sangat Lemah';
                    strengthClass = 'bg-red-100 text-red-800';
                    break;
                case 'weak':
                    strengthText = 'Lemah';
                    strengthClass = 'bg-orange-100 text-orange-800';
                    break;
                case 'moderate':
                    strengthText = 'Cukup';
                    strengthClass = 'bg-yellow-100 text-yellow-800';
                    break;
                case 'strong':
                    strengthText = 'Kuat';
                    strengthClass = 'bg-green-100 text-green-800';
                    break;
                case 'very-strong':
                    strengthText = 'Sangat Kuat';
                    strengthClass = 'bg-emerald-100 text-emerald-800';
                    break;
            }
            
            strengthIndicator.innerHTML = `
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium">Kekuatan Password:</span>
                    <span class="text-xs px-2 py-1 rounded-full ${strengthClass}">${strengthText}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full ${getStrengthBarClass(strength.level)}" 
                         style="width: ${strength.score * 20}%"></div>
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    ${strength.feedback}
                </div>
                <div class="mt-2">
                    <button type="button" 
                            onclick="openPasswordTips()" 
                            class="text-xs text-blue-600 hover:text-blue-800 flex items-center">
                        <i data-feather="info" class="w-3 h-3 mr-1"></i>
                        Tips membuat password yang kuat
                    </button>
                </div>
            `;
            feather.replace();
        });
    }
}

function calculatePasswordStrength(password) {
    let score = 0;
    let feedback = '';
    
    // Length check
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    
    // Character variety checks
    if (/[a-z]/.test(password)) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^a-zA-Z0-9]/.test(password)) score++;
    
    // Determine level
    let level;
    if (score <= 2) {
        level = 'very-weak';
        feedback = 'Tambahkan lebih banyak karakter dan variasi';
    } else if (score <= 3) {
        level = 'weak';
        feedback = 'Coba tambahkan huruf besar, angka, atau simbol';
    } else if (score <= 4) {
        level = 'moderate';
        feedback = 'Password cukup baik, bisa lebih kuat';
    } else if (score <= 5) {
        level = 'strong';
        feedback = 'Password kuat, pertahankan!';
    } else {
        level = 'very-strong';
        feedback = 'Password sangat kuat dan aman';
    }
    
    return {
        score: score,
        level: level,
        feedback: feedback
    };
}

function getStrengthBarClass(level) {
    switch(level) {
        case 'very-weak': return 'bg-red-500';
        case 'weak': return 'bg-orange-500';
        case 'moderate': return 'bg-yellow-500';
        case 'strong': return 'bg-green-500';
        case 'very-strong': return 'bg-emerald-500';
        default: return 'bg-gray-500';
    }
}

function validatePasswordStrength(password) {
    if (password.trim() === '') return true; // Empty password is allowed (not changing)
    
    const strength = calculatePasswordStrength(password);
    return strength.score >= 3; // At least moderate strength
}

function openPasswordTips() {
    const modal = new Modal(document.getElementById('password-strength-modal'));
    modal.show();
}

function showToast(type, message) {
    // Reuse toast function from previous code
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
    
    setTimeout(() => {
        toast.remove();
    }, 5000);
    
    toast.querySelector('.toast__close').addEventListener('click', () => {
        toast.remove();
    });
}

// Initialize modal if needed
if (typeof Modal === 'undefined') {
    class Modal {
        constructor(element) {
            this.element = element;
        }
        
        show() {
            this.element.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        
        hide() {
            this.element.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
    
    // Add click handlers for modal close
    document.addEventListener('click', function(event) {
        if (event.target.hasAttribute('data-tw-dismiss') && event.target.getAttribute('data-tw-dismiss') === 'modal') {
            const modal = event.target.closest('.modal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }
    });
}
</script>

<style>
/* Toast styles (same as previous) */
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

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1050;
    overflow-y: auto;
}

.modal-dialog {
    position: relative;
    width: auto;
    margin: 1.75rem auto;
    max-width: 500px;
}

.modal-content {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 100%;
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.modal-body {
    position: relative;
    flex: 1 1 auto;
    padding: 1rem;
}

.modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 1rem;
    border-top: 1px solid #e5e7eb;
}

/* Form file input styling */
.form-control-file {
    display: block;
    width: 100%;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    line-height: 1.25;
    color: #374151;
    background-color: white;
    background-clip: padding-box;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control-file:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Animations */
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
@endpush