<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('admin_assets/images/logo.png') }}" rel="shortcut icon">
    <title>@yield('title', 'SIMR - Sistem Informasi Manajemen Risiko')</title>
    
    <!-- BEGIN: CSS Assets-->
    <link rel="stylesheet" href="{{ asset('admin_assets/css/app.css') }}">
    @stack('styles')
    <!-- END: CSS Assets-->
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
        @yield('content')
    </div>
    <!-- END: Content -->
    
    <!-- BEGIN: JS Assets-->
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <script src="{{ asset('admin_assets/js/app.js') }}"></script>
    
    <!-- Initialize Icons & Dropdowns -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- ICONS INIT ---
        if (typeof feather !== 'undefined') feather.replace();
        if (typeof lucide !== 'undefined') lucide.createIcons();

        // --- DROPDOWN HANDLING (NOTIFICATION & ACCOUNT) ---
        const dropdowns = document.querySelectorAll('.dropdown');

        dropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            const menu = dropdown.querySelector('.dropdown-menu');

            if (toggle && menu) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Tutup dropdown lain yang terbuka
                    document.querySelectorAll('.dropdown-menu.show').forEach(openMenu => {
                        if (openMenu !== menu) {
                            openMenu.classList.remove('show');
                        }
                    });

                    // Toggle dropdown aktif
                    menu.classList.toggle('show');
                });
            }
        });

        // Tutup dropdown saat klik di luar area
        document.addEventListener('click', function(e) {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                if (!menu.parentElement.contains(e.target)) {
                    menu.classList.remove('show');
                }
            });
        });

        // --- GLOBAL SEARCH ---
        const searchInput = document.getElementById('global-search');
        const searchResults = document.getElementById('search-results');
        let searchTimeout;

        if (searchInput && searchResults) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length < 2) {
                    searchResults.classList.add('hidden');
                    return;
                }

                searchTimeout = setTimeout(() => {
                    fetch(`/search/autocomplete?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length === 0) {
                                searchResults.innerHTML = '<div class="p-3 text-center text-gray-500">Tidak ada hasil</div>';
                            } else {
                                searchResults.innerHTML = data.map(item => `
                                    <a href="${item.url}" class="block p-3 hover:bg-gray-50 dark:hover:bg-dark-3 border-b border-gray-100 dark:border-dark-5">
                                        <div class="font-medium text-gray-800 dark:text-gray-200">${item.title}</div>
                                        <div class="text-xs text-gray-500 mt-1">${item.subtitle}</div>
                                    </a>
                                `).join('');
                            }
                            searchResults.classList.remove('hidden');
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                        });
                }, 300);
            });

            // Tutup hasil pencarian jika klik di luar
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add('hidden');
                }
            });

            // Enter → langsung menuju halaman hasil
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    window.location.href = `/search?q=${encodeURIComponent(this.value)}`;
                }
            });
        }
    });
    </script>    
    @stack('scripts')
    <!-- END: JS Assets-->
</body>
</html>