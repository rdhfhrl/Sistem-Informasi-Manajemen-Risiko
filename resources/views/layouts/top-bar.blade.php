<!-- BEGIN: Top Bar -->
<div class="border-b border-theme-29 -mt-10 md:-mt-5 -mx-3 sm:-mx-8 px-3 sm:px-8 pt-3 md:pt-0 mb-10">
    <div class="top-bar-boxed flex items-center">
        <!-- BEGIN: Logo -->
        <a href="{{ route('dashboard') }}" class="-intro-x hidden md:flex">
            <img alt="SIMR Logo" class="w-20" src="{{ asset('admin_assets/images/logo_simr.png') }}">
        </a>
        <!-- END: Logo -->
        
        <!-- BEGIN: Breadcrumb -->
        <div class="-intro-x breadcrumb breadcrumb--light mr-auto">
            <a href="{{ route('dashboard') }}" class="text-white hover:text-white/80">Sistem Informasi Manajemen Risiko</a>
            <i data-feather="chevron-right" class="breadcrumb__icon text-white/50"></i>
            @if(isset($breadcrumbs))
                @foreach($breadcrumbs as $breadcrumb)
                    @if(!$loop->last && isset($breadcrumb['url']))
                        <a href="{{ $breadcrumb['url'] }}" class="text-white hover:text-white/80">{{ $breadcrumb['label'] }}</a>
                        <i data-feather="chevron-right" class="breadcrumb__icon text-white/50"></i>
                    @else
                        <a href="" class="breadcrumb--active text-white">{{ $breadcrumb['label'] }}</a>
                    @endif
                @endforeach
            @else
                <a href="" class="breadcrumb--active text-white">{{ $title ?? 'Dashboard' }}</a>
            @endif
        </div>
        <!-- END: Breadcrumb -->
        
        <!-- BEGIN: Search -->
        <div class="intro-x relative mr-3 sm:mr-6">
            <div class="search hidden sm:block">
                <input type="text" 
                       class="search__input form-control dark:bg-dark-1 border-transparent placeholder-theme-13" 
                       placeholder="Cari risiko, proyek, laporan..."
                       id="global-search">
                <i data-feather="search" class="search__icon dark:text-gray-300"></i>
                <div id="search-results" class="search-result absolute hidden w-full mt-1 bg-white dark:bg-dark-6 rounded-md shadow-lg z-50 max-h-96 overflow-y-auto"></div>
            </div>
            <a class="notification notification--light sm:hidden" href="javascript:;" data-tw-toggle="modal" data-tw-target="#mobile-search-modal">
                <i data-feather="search" class="notification__icon dark:text-gray-300"></i>
            </a>
        </div>
        <!-- END: Search -->
        
        <!-- BEGIN: Notifications -->
        <div class="intro-x dropdown mr-4 sm:mr-6">
            @php
                // Initialize notification counts
                $overdueMitigations = 0;
                $highRiskNoMitigation = 0;
                $pendingApprovals = 0;
                $dueSchedules = 0;
                $risksDueMonitoring = 0;
                
                try {
                    // Check if models exist before querying
                    if (class_exists(\App\Models\RiskMitigation::class)) {
                        $overdueMitigations = \App\Models\RiskMitigation::where('deadline', '<', now())
                            ->whereNotIn('status', ['selesai', 'dibatalkan', 'completed'])
                            ->count();
                    }
                    
                    if (class_exists(\App\Models\Risk::class)) {
                        $highRiskNoMitigation = \App\Models\Risk::whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
                            ->doesntHave('mitigations')
                            ->count();
                    }
                    
                    if (class_exists(\App\Models\Report::class)) {
                        $pendingApprovals = \App\Models\Report::where('status', 'pending_approval')->count();
                    }
                    
                    if (class_exists(\App\Models\ReportSchedule::class)) {
                        $dueSchedules = \App\Models\ReportSchedule::active()
                            ->autoGenerate()
                            ->where('generation_time', '<=', now()->addDay())
                            ->count();
                    }
                    
                    if (class_exists(\App\Models\Risk::class)) {
                        $risksDueMonitoring = \App\Models\Risk::where(function($query) {
                                $query->whereNull('last_monitoring_date')
                                    ->orWhere('last_monitoring_date', '<', now()->subDays(30));
                            })
                            ->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
                            ->where('risk_status', '!=', 'closed')
                            ->count();
                    }
                    
                    $totalNotifications = $overdueMitigations + $highRiskNoMitigation + 
                                        $pendingApprovals + $dueSchedules + $risksDueMonitoring;
                } catch (\Exception $e) {
                    $totalNotifications = 0;
                }
            @endphp
            
            <div class="dropdown-toggle notification notification--light notification--bullet cursor-pointer" 
                role="button" 
                aria-expanded="false"
                data-tw-toggle="dropdown">
                <i data-feather="bell" class="notification__icon dark:text-gray-300"></i>
                @if($totalNotifications > 0)
                    <div class="notification__bullet bg-theme-9"></div>
                @endif
            </div>
            <div class="notification-content pt-2 dropdown-menu">
                <div class="notification-content__box dropdown-menu__content box dark:bg-dark-6">
                    <div class="notification-content__title">Notifikasi</div>
                    
                    <!-- Overdue Mitigations -->
                    @if($overdueMitigations > 0)
                    <div class="cursor-pointer relative flex items-center mb-3">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                            <i data-feather="alert-triangle" class="w-4 h-4 text-red-600"></i>
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                @if(class_exists(\App\Models\RiskMitigation::class))
                                <a href="{{ route('risk-mitigations.index', ['status' => 'overdue']) }}" class="font-medium truncate mr-5">
                                    Mitigasi Terlambat
                                </a>
                                @else
                                <span class="font-medium truncate mr-5">Mitigasi Terlambat</span>
                                @endif
                                <div class="text-xs text-gray-500 ml-auto whitespace-nowrap">
                                    {{ $overdueMitigations }} item
                                </div>
                            </div>
                            <div class="w-full truncate text-gray-600 mt-0.5 text-xs">
                                Mitigasi melewati deadline
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- High Risk Without Mitigation -->
                    @if($highRiskNoMitigation > 0)
                    <div class="cursor-pointer relative flex items-center mb-3">
                        <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center mr-3">
                            <i data-feather="alert-circle" class="w-4 h-4 text-orange-600"></i>
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                @if(class_exists(\App\Models\Risk::class))
                                <a href="{{ route('risks.index', ['risk_level' => 'tinggi']) }}" class="font-medium truncate mr-5">
                                    Risiko Tinggi
                                </a>
                                @else
                                <span class="font-medium truncate mr-5">Risiko Tinggi</span>
                                @endif
                                <div class="text-xs text-gray-500 ml-auto whitespace-nowrap">
                                    {{ $highRiskNoMitigation }} item
                                </div>
                            </div>
                            <div class="w-full truncate text-gray-600 mt-0.5 text-xs">
                                Risiko tinggi tanpa mitigasi
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Pending Approvals -->
                    @if($pendingApprovals > 0)
                    <div class="cursor-pointer relative flex items-center mb-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i data-feather="clock" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                @if(class_exists(\App\Models\Report::class))
                                <a href="{{ route('reports.index', ['status' => 'pending_approval']) }}" class="font-medium truncate mr-5">
                                    Persetujuan Laporan
                                </a>
                                @else
                                <span class="font-medium truncate mr-5">Persetujuan Laporan</span>
                                @endif
                                <div class="text-xs text-gray-500 ml-auto whitespace-nowrap">
                                    {{ $pendingApprovals }} item
                                </div>
                            </div>
                            <div class="w-full truncate text-gray-600 mt-0.5 text-xs">
                                Menunggu persetujuan
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Due Schedules -->
                    @if($dueSchedules > 0)
                    <div class="cursor-pointer relative flex items-center mb-3">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                            <i data-feather="calendar" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                @if(class_exists(\App\Models\ReportSchedule::class))
                                <a href="{{ route('report-schedules.index') }}" class="font-medium truncate mr-5">
                                    Jadwal Mendatang
                                </a>
                                @else
                                <span class="font-medium truncate mr-5">Jadwal Mendatang</span>
                                @endif
                                <div class="text-xs text-gray-500 ml-auto whitespace-nowrap">
                                    {{ $dueSchedules }} jadwal
                                </div>
                            </div>
                            <div class="w-full truncate text-gray-600 mt-0.5 text-xs">
                                Akan berjalan dalam 24 jam
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Risks Due for Monitoring -->
                    @if($risksDueMonitoring > 0)
                    <div class="cursor-pointer relative flex items-center mb-3">
                        <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center mr-3">
                            <i data-feather="eye" class="w-4 h-4 text-teal-600"></i>
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                @if(class_exists(\App\Models\Risk::class))
                                <a href="{{ route('risks.index', ['filter' => 'due_monitoring']) }}" class="font-medium truncate mr-5">
                                    Risiko Perlu Monitor
                                </a>
                                @else
                                <span class="font-medium truncate mr-5">Risiko Perlu Monitor</span>
                                @endif
                                <div class="text-xs text-gray-500 ml-auto whitespace-nowrap">
                                    {{ $risksDueMonitoring }} item
                                </div>
                            </div>
                            <div class="w-full truncate text-gray-600 mt-0.5 text-xs">
                                Risiko tinggi perlu pemantauan
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- If no notifications -->
                    @if($totalNotifications == 0)
                    <div class="text-center py-4">
                        <i data-feather="check-circle" class="w-8 h-8 text-green-500 mx-auto mb-2"></i>
                        <p class="text-gray-600 text-sm">Tidak ada notifikasi</p>
                    </div>
                    @endif
                    
                    <!-- Footer Links -->
                    <div class="p-2 border-t border-gray-200 dark:border-dark-3 mt-2">
                        @if(Route::has('notifications.index'))
                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary w-full py-1 text-xs">
                            <i data-feather="list" class="w-4 h-4 mr-1"></i> Lihat Semua
                        </a>
                        @else
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-full py-1 text-xs">
                            <i data-feather="eye" class="w-4 h-4 mr-1"></i> Dashboard
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- END: Notifications -->
        
        <!-- BEGIN: Account Menu -->
        <div class="intro-x dropdown w-8 h-8">
            @php
                $user = auth()->user();
                $userName = $user ? $user->name : 'Guest';
                $userRole = $user ? $user->role : 'guest';
                $roleNames = [
                    'admin' => 'Administrator',
                    'unit_pemilik_risiko' => 'Unit Pemilik Risiko',
                    'auditor' => 'Auditor',
                    'guest' => 'Guest'
                ];
                $displayRole = $roleNames[$userRole] ?? 'Pengguna';
            @endphp
            
            <div class="dropdown-toggle w-8 h-8 rounded-full overflow-hidden shadow-lg image-fit zoom-in scale-110 cursor-pointer" 
                 role="button" 
                 aria-expanded="false"
                 data-tw-toggle="dropdown">
                @if($user && $user->avatar && file_exists(public_path('storage/' . $user->avatar)))
                    <img alt="{{ $userName }}" 
                         src="{{ asset('storage/' . $user->avatar) }}"
                         class="w-full h-full object-cover"
                         onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\"w-full h-full bg-theme-1 flex items-center justify-center text-white font-bold text-sm\">' + '{{ strtoupper(substr($userName, 0, 2)) }}' + '</div>';">
                @else
                    <div class="w-full h-full bg-theme-1 flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr($userName, 0, 2)) }}
                    </div>
                @endif
            </div>
            <div class="dropdown-menu w-56">
                <div class="dropdown-menu__content box bg-theme-26 dark:bg-dark-6 text-white">
                    <div class="p-4 border-b border-theme-27 dark:border-dark-3">
                        <div class="font-medium">{{ $userName }}</div>
                        <div class="text-xs text-theme-28 mt-0.5 dark:text-gray-600">
                            {{ $displayRole }}
                            @if($user && $user->email)
                            <div class="truncate">{{ $user->email }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="p-2">
                        @if($user)
                            @if(Route::has('profile.show'))
                            <a href="{{ route('profile.show') }}" 
                               class="flex items-center block p-2 transition duration-300 ease-in-out hover:bg-theme-1 dark:hover:bg-dark-3 rounded-md">
                                <i data-feather="user" class="w-4 h-4 mr-2"></i> Profil
                            </a>
                            @endif
                            
                            @if(Route::has('profile.edit'))
                            <a href="{{ route('profile.edit') }}" 
                               class="flex items-center block p-2 transition duration-300 ease-in-out hover:bg-theme-1 dark:hover:bg-dark-3 rounded-md">
                                <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit Profil
                            </a>
                            @endif
                            
                            @if($userRole === 'admin' && Route::has('admin.dashboard'))
                            <a href="{{ route('admin.dashboard') }}" 
                               class="flex items-center block p-2 transition duration-300 ease-in-out hover:bg-theme-1 dark:hover:bg-dark-3 rounded-md">
                                <i data-feather="settings" class="w-4 h-4 mr-2"></i> Admin Panel
                            </a>
                            @endif
                        @endif
                    </div>
                    <div class="p-2 border-t border-theme-27 dark:border-dark-3">
                        @if($user)
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                            <a href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); 
                                        if(confirm('Apakah Anda yakin ingin keluar?')) { 
                                            document.getElementById('logout-form').submit(); 
                                        }" 
                               class="flex items-center block p-2 transition duration-300 ease-in-out hover:bg-theme-1 dark:hover:bg-dark-3 rounded-md">
                                <i data-feather="log-out" class="w-4 h-4 mr-2"></i> Keluar
                            </a>
                        @else
                            <a href="{{ route('login') }}" 
                               class="flex items-center block p-2 transition duration-300 ease-in-out hover:bg-theme-1 dark:hover:bg-dark-3 rounded-md">
                                <i data-feather="log-in" class="w-4 h-4 mr-2"></i> Login
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- END: Account Menu -->
    </div>
</div>
<!-- END: Top Bar -->

<!-- Mobile Search Modal -->
<div class="modal" id="mobile-search-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Pencarian</h2>
                <button data-tw-dismiss="modal" class="btn btn-outline-secondary">
                    <i data-feather="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="relative">
                    <input type="text" 
                           class="form-control w-full" 
                           placeholder="Cari risiko, proyek, laporan..."
                           id="mobile-search-input">
                    <i data-feather="search" class="absolute right-3 top-3 text-gray-400"></i>
                    <div id="mobile-search-results" class="search-result absolute w-full mt-1 bg-white dark:bg-dark-6 rounded-md shadow-lg z-50 max-h-96 overflow-y-auto"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Global Search Functionality
    const globalSearch = document.getElementById('global-search');
    const searchResults = document.getElementById('search-results');
    const mobileSearchInput = document.getElementById('mobile-search-input');
    const mobileSearchResults = document.getElementById('mobile-search-results');
    const mobileSearchModal = document.getElementById('mobile-search-modal');
    
    let searchTimeout;

    // Function to perform search
    function performSearch(query, resultsContainer) {
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            resultsContainer.innerHTML = '';
            resultsContainer.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`/api/search?q=${encodeURIComponent(query)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.length === 0) {
                        resultsContainer.innerHTML = `
                            <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                                <i data-feather="search" class="w-5 h-5 mx-auto mb-2"></i>
                                <p class="text-sm">Tidak ditemukan hasil untuk "${query}"</p>
                            </div>`;
                    } else {
                        resultsContainer.innerHTML = data.map(item => `
                            <a href="${item.url}" class="block p-3 hover:bg-gray-50 dark:hover:bg-dark-3 border-b border-gray-100 dark:border-dark-5 last:border-b-0 transition-colors">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3 ${item.icon_bg}">
                                        <i data-feather="${item.icon}" class="w-4 h-4 ${item.icon_color}"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-700 dark:text-gray-300">${item.title}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">${item.subtitle}</div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">${item.type}</div>
                                    </div>
                                    <i data-feather="chevron-right" class="w-4 h-4 text-gray-400"></i>
                                </div>
                            </a>
                        `).join('');
                    }
                    resultsContainer.classList.remove('hidden');
                    
                    // Replace Feather icons in search results
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    resultsContainer.innerHTML = `
                        <div class="p-4 text-center text-red-500">
                            <i data-feather="alert-circle" class="w-5 h-5 mx-auto mb-2"></i>
                            <p class="text-sm">Gagal melakukan pencarian</p>
                        </div>`;
                    resultsContainer.classList.remove('hidden');
                });
        }, 300);
    }

    // Desktop search
    if (globalSearch && searchResults) {
        globalSearch.addEventListener('input', function() {
            performSearch(this.value.trim(), searchResults);
        });

        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!globalSearch.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });

        // Enter key to go to search page
        globalSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && this.value.trim().length > 0) {
                window.location.href = `/search?q=${encodeURIComponent(this.value.trim())}`;
            }
        });
    }

    // Mobile search
    if (mobileSearchInput && mobileSearchResults) {
        mobileSearchInput.addEventListener('input', function() {
            performSearch(this.value.trim(), mobileSearchResults);
        });
        
        // Reset search when modal closes
        if (mobileSearchModal) {
            mobileSearchModal.addEventListener('hidden.tw.modal', function() {
                mobileSearchInput.value = '';
                mobileSearchResults.innerHTML = '';
                mobileSearchResults.classList.add('hidden');
            });
        }
    }

    // Dark Mode Toggle
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    if (darkModeToggle) {
        const toggleLabel = darkModeToggle.querySelector('.toggle-label');
        
        // Check initial theme
        const isDarkMode = localStorage.getItem('dark-mode') === 'true' || 
                          (!localStorage.getItem('dark-mode') && window.matchMedia('(prefers-color-scheme: dark)').matches);
        
        if (isDarkMode) {
            document.documentElement.classList.add('dark');
            if (toggleLabel) toggleLabel.textContent = 'Mode Terang';
        }
        
        darkModeToggle.addEventListener('click', function() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('dark-mode', isDark);
            
            if (toggleLabel) {
                toggleLabel.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
            }
            
            // Update feather icons for dark mode
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    }

    // Notification dropdown close on click outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.intro-x.dropdown')) {
            const dropdowns = document.querySelectorAll('.dropdown-menu');
            dropdowns.forEach(dropdown => {
                if (dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            });
        }
    });
});
</script>

<style>
.search-result {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.1);
}

.dark .search-result {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    border-color: rgba(255, 255, 255, 0.1);
}

.notification__bullet {
    position: absolute;
    top: -2px;
    right: -2px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(var(--color-theme-9), 0.7);
    }
    
    70% {
        transform: scale(1);
        box-shadow: 0 0 0 10px rgba(var(--color-theme-9), 0);
    }
    
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(var(--color-theme-9), 0);
    }
}

/* Improve dropdown animations */
.dropdown-menu {
    transition: opacity 0.2s ease, transform 0.2s ease;
    opacity: 0;
    transform: translateY(-10px);
    display: none;
}

.dropdown-menu.show {
    opacity: 1;
    transform: translateY(0);
    display: block;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .top-bar-boxed {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    .intro-x.mr-3,
    .intro-x.mr-4 {
        margin-right: 0.5rem;
    }
    
    .breadcrumb {
        font-size: 0.75rem;
    }
}
</style>
@endpush