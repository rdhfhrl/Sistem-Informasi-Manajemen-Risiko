<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('admin_assets/images/logo.png') }}" rel="shortcut icon">
    <title>@yield('title', 'Dashboard - SIMR')</title>
    
    <!-- BEGIN: CSS Assets-->
    <link rel="stylesheet" href="{{ asset('admin_assets/css/app.css') }}">
    
    <!-- Chart.js untuk dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Role-specific CSS -->
    @auth
        @if(Auth::user()->role === 'admin')
            <style>
                .admin-dashboard .stat-card {
                    border-left: 4px solid #3b82f6;
                }
                .admin-dashboard .quick-action {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                }
            </style>
        @elseif(Auth::user()->role === 'unit_pemilik_risiko')
            <style>
                .upr-dashboard .stat-card {
                    border-left: 4px solid #10b981;
                }
                .upr-dashboard .my-risk {
                    background: #fef3c7;
                    border: 1px solid #f59e0b;
                }
            </style>
        @elseif(Auth::user()->role === 'auditor')
            <style>
                .auditor-dashboard .stat-card {
                    border-left: 4px solid #8b5cf6;
                }
                .auditor-dashboard .evaluation-item {
                    background: #e0e7ff;
                    border: 1px solid #6366f1;
                }
            </style>
        @endif
    @endauth
    
    @stack('styles')
</head>
<body class="main">
    <!-- BEGIN: Top Bar -->
    @include('layouts.top-bar')
    <!-- END: Top Bar -->
    
    <!-- BEGIN: Top Menu -->
    @include('layouts.top-menu')
    <!-- END: Top Menu -->
    
    <!-- BEGIN: Content -->
    <div class="content">
        <!-- Role-specific dashboard class -->
        @auth
            @if(Auth::user()->role === 'admin')
                <div class="admin-dashboard">
            @elseif(Auth::user()->role === 'unit_pemilik_risiko')
                <div class="upr-dashboard">
            @elseif(Auth::user()->role === 'auditor')
                <div class="auditor-dashboard">
            @else
                <div>
            @endif
        @endauth
        
        @yield('content')
        
        @auth
            </div>
        @endauth
    </div>
    <!-- END: Content -->
    
    <!-- BEGIN: JS Assets-->
    <!-- Feather Icons JS (Local) -->
    <script src="{{ asset('admin_assets/js/feather.min.js') }}"></script>
    
    <script src="{{ asset('admin_assets/js/app.js') }}"></script>

    <!-- Dashboard-specific JS -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Feather Icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        // Auto-refresh dashboard data every 5 minutes
        setInterval(function() {
            refreshDashboardData();
        }, 300000);
        
        // Function to refresh dashboard data
        function refreshDashboardData() {
            // Implement AJAX refresh if needed
            console.log('Dashboard auto-refresh triggered');
        }
        
        // Role-based dashboard initialization
        @auth
            @if(Auth::user()->role === 'admin')
                initAdminDashboard();
            @elseif(Auth::user()->role === 'unit_pemilik_risiko')
                initUprDashboard();
            @elseif(Auth::user()->role === 'auditor')
                initAuditorDashboard();
            @endif
        @endauth
    });
    
    function initAdminDashboard() {
        // Admin-specific JS initialization
        console.log('Admin dashboard initialized');
        // Load admin charts, etc.
    }
    
    function initUprDashboard() {
        // UPR-specific JS initialization
        console.log('UPR dashboard initialized');
        // Load UPR-specific data
    }
    
    function initAuditorDashboard() {
        // Auditor-specific JS initialization
        console.log('Auditor dashboard initialized');
        // Load auditor-specific data
    }
    </script>
    
    @stack('scripts')
</body>
</html>