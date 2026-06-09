@extends('layouts.auth')

@section('title', 'Login - SIMR')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf
    
    <div class="mb-4">
        <label for="email" class="form-label">Email</label>
        <div class="relative">
            <input type="email" id="email" name="email" class="form-control w-full pl-10" 
                   placeholder="email@example.com" value="{{ old('email') }}" required autofocus>
            <div class="absolute left-3 top-2.5">
                <i data-feather="mail" class="w-5 h-5 text-gray-400"></i>
            </div>
        </div>
        @error('email')
            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-6">
        <label for="password" class="form-label">Password</label>
        <div class="relative">
            <input type="password" id="password" name="password" class="form-control w-full pl-10" 
                   placeholder="Masukkan password" required>
            <div class="absolute left-3 top-2.5">
                <i data-feather="lock" class="w-5 h-5 text-gray-400"></i>
            </div>
        </div>
        @error('password')
            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="flex items-center justify-between mb-6">
        <div class="form-check">
            <input type="checkbox" id="remember" name="remember" class="form-check-input">
            <label for="remember" class="form-check-label text-sm">Ingat saya</label>
        </div>
        @if(false) <!-- Sementara nonaktif karena route password.request tidak ada -->
            <a href="#" class="text-sm text-primary hover:underline">
                Lupa password?
            </a>
        @endif
    </div>
    
    <button type="submit" class="btn btn-primary w-full py-3 text-lg">
        <i data-feather="log-in" class="w-5 h-5 mr-2 inline"></i> Login
    </button>
    
    <!-- Hapus bagian register karena tidak ada route 'register' -->
    <!-- 
    @if(Route::has('register'))
    <div class="mt-6 text-center">
        <p class="text-gray-600">Belum punya akun?</p>
        <a href="{{ route('register') }}" class="btn btn-outline-primary w-full py-3 mt-2">
            <i data-feather="user-plus" class="w-5 h-5 mr-2 inline"></i> Daftar Akun Baru
        </a>
    </div>
    @endif
    -->
    
    <div class="mt-8 pt-6 border-t border-gray-200">
        <div class="grid grid-cols-3 gap-2">
            @php
                // Sesuaikan dengan data seeder yang sudah dibuat
                $demoAccounts = [
                    ['email' => 'admin@dpupr.test', 'role' => 'Admin', 'password' => 'password123'],
                    ['email' => 'riskmanager@uptd.test', 'role' => 'UPR', 'password' => 'password123'],
                    ['email' => 'auditor@simr.test', 'role' => 'Auditor', 'password' => 'password123'],
                ];
            @endphp
            
            @foreach($demoAccounts as $account)
                <button type="button" 
                        onclick="setDemoCredentials('{{ $account['email'] }}', '{{ $account['password'] }}')" 
                        class="btn btn-outline-secondary py-2 text-xs" 
                        title="Login sebagai {{ $account['role'] }}">
                    {{ $account['role'] }}
                </button>
            @endforeach
        </div>
    </div>
</form>

@push('scripts')
<script>
function setDemoCredentials(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
    
    // Optional: Tampilkan toast notification
    showToast('Login sebagai ' + email, 'info');
}

function showToast(message, type = 'info') {
    // Buat elemen toast sederhana
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 p-3 rounded-md shadow-lg text-white ${
        type === 'info' ? 'bg-blue-500' : 
        type === 'success' ? 'bg-green-500' : 
        'bg-gray-500'
    }`;
    toast.innerHTML = `
        <div class="flex items-center">
            <i data-feather="check-circle" class="w-4 h-4 mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Update feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Hapus toast setelah 3 detik
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>
@endpush
@endsection