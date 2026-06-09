@extends('layouts.auth')

@section('title', 'Register - SIMR Development')

@section('content')
<div class="text-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Buat Akun Baru</h2>
    <p class="text-gray-600 mt-2">Hanya untuk keperluan development/testing</p>
</div>

<form method="POST" action="{{ route('register') }}">
    @csrf
    
    <div class="mb-4">
        <label for="name" class="form-label">Nama Lengkap</label>
        <input type="text" id="name" name="name" class="form-control" 
               value="{{ old('name') }}" required autofocus>
        @error('name')
            <div class="text-danger mt-1 text-sm">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-4">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control" 
               value="{{ old('email') }}" required>
        @error('email')
            <div class="text-danger mt-1 text-sm">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" required>
        @error('password')
            <div class="text-danger mt-1 text-sm">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-4">
        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" 
               class="form-control" required>
    </div>
    
    <div class="mb-4">
        <label for="role" class="form-label">Role</label>
        <select id="role" name="role" class="form-control" required>
            <option value="">Pilih Role</option>
            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="unit_pemilik_risiko" {{ old('role') == 'unit_pemilik_risiko' ? 'selected' : '' }}>
                Unit Pemilik Risiko
            </option>
            <option value="auditor" {{ old('role') == 'auditor' ? 'selected' : '' }}>Auditor</option>
        </select>
        @error('role')
            <div class="text-danger mt-1 text-sm">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-6">
        <label for="organization_id" class="form-label">Organisasi (Opsional)</label>
        <input type="number" id="organization_id" name="organization_id" 
               class="form-control" value="{{ old('organization_id') }}" 
               placeholder="ID Organisasi">
        <small class="text-gray-500">Isi ID organisasi jika tersedia</small>
    </div>
    
    <button type="submit" class="btn btn-primary w-full py-3">
        <i data-feather="user-plus" class="w-5 h-5 mr-2 inline"></i> Buat Akun
    </button>
    
    <div class="mt-4 text-center">
        <a href="{{ route('login') }}" class="text-primary hover:underline">
            <i data-feather="arrow-left" class="w-4 h-4 inline mr-1"></i> Kembali ke Login
        </a>
    </div>
</form>

@push('scripts')
<script>
    // Inisialisasi feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
</script>
@endpush
@endsection