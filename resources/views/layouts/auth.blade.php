<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('admin_assets/images/logo_simr.png') }}" rel="shortcut icon">
    <title>@yield('title', 'Login - SIMR')</title>
    
    <!-- BEGIN: CSS Assets-->
    <link rel="stylesheet" href="{{ asset('admin_assets/css/app.css') }}">
    
    <style>
        .auth-page {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .auth-box {
            background: white;
            border-radius: 10px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }
        
        .auth-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .auth-body {
            padding: 30px;
        }
        
        .auth-footer {
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="auth-page">
        <div class="auth-box">
            <!-- Header -->
            <div class="auth-header">
                <div class="flex items-center justify-center mb-4">
                    <img src="{{ asset('admin_assets/images/logo_simr.png') }}" alt="SIMR" class="w-10 h-10 mr-3">
                    <h1 class="text-2xl font-bold">SIMR PUPR</h1>
                </div>
                <p class="text-blue-100 opacity-90">Sistem Informasi Manajemen Risiko</p>
                <p class="text-blue-100 opacity-80 text-sm mt-1">Dinas PUPR Provinsi Sumatera Utara</p>
            </div>
            
            <!-- Content -->
            <div class="auth-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible show flex items-center mb-4" role="alert">
                    <i data-feather="check-circle" class="w-5 h-5 mr-2"></i> 
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                        <i data-feather="x" class="w-4 h-4"></i>
                    </button>
                </div>
                @endif
                
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible show flex items-center mb-4" role="alert">
                    <i data-feather="alert-octagon" class="w-5 h-5 mr-2"></i> 
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                        <i data-feather="x" class="w-4 h-4"></i>
                    </button>
                </div>
                @endif
                
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible show flex items-center mb-4" role="alert">
                    <i data-feather="alert-octagon" class="w-5 h-5 mr-2"></i>
                    <div class="ml-1">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                        <i data-feather="x" class="w-4 h-4"></i>
                    </button>
                </div>
                @endif
                
                @yield('content')
            </div>
            
            <!-- Footer -->
            <div class="auth-footer">
                <p>© {{ date('Y') }} Sistem Informasi Manajemen Risiko</p>
            </div>
        </div>
    </div>
    
    <!-- BEGIN: JS Assets-->
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
    @stack('scripts')
</body>
</html>