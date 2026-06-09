@extends('layouts.master')

@section('title', 'Tambah Pengguna - SIMR')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">
        Tambah Pengguna Baru
    </h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
            <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
        </a>
    </div>
</div>

<div class="intro-y box mt-5">
    <div class="flex items-center p-5 border-b border-slate-200/60">
        <h2 class="font-medium text-base mr-auto">Form Tambah Pengguna</h2>
    </div>
    <div class="p-5">
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12 sm:col-span-6">
                    <label for="name" class="form-label">Nama Lengkap *</label>
                    <input type="text" id="name" name="name" class="form-control" 
                           value="{{ old('name') }}" required>
                    @error('name')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-span-12 sm:col-span-6">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="{{ old('email') }}" required>
                    @error('email')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-span-12 sm:col-span-6">
                    <label for="password" class="form-label">Password *</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    @error('password')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-span-12 sm:col-span-6">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" 
                           class="form-control" required>
                </div>
                
                <div class="col-span-12 sm:col-span-6">
                    <label for="role" class="form-label">Role *</label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="">Pilih Role</option>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}" {{ old('role') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-span-12 sm:col-span-6">
                    <label for="avatar" class="form-label">Foto Profil</label>
                    <input type="file" id="avatar" name="avatar" class="form-control" 
                           accept="image/jpeg,image/png,image/jpg,image/gif">
                    <div class="text-xs text-gray-500 mt-2">Maksimal 2MB. Format: JPG, PNG, GIF</div>
                    @error('avatar')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mt-8 flex justify-end">
                <button type="button" onclick="window.history.back()" 
                        class="btn btn-outline-secondary w-24 mr-2">Batal</button>
                <button type="submit" class="btn btn-primary w-24">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection