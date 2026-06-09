<!-- BEGIN: Top Menu -->
<nav class="top-nav">
    <ul>
        <!-- Dashboard - Tampil untuk semua role -->
        <li>
            <a href="{{ route('dashboard') }}" class="top-menu {{ Request::routeIs('dashboard') ? 'top-menu--active' : '' }}">
                <div class="top-menu__icon">
                    <i data-feather="home"></i>
                </div>
                <div class="top-menu__title">Dashboard</div>
            </a>
        </li>
        
        @auth
            @if(Auth::user()->role === 'admin')
                <!-- ====================== -->
                <!-- MENU UNTUK ADMIN -->
                <!-- ====================== -->
                
                <!-- Master Data -->
                <li>
                    <a href="javascript:;" class="top-menu {{ 
                        request()->routeIs('organizations.*') || 
                        request()->routeIs('projects.*') || 
                        request()->routeIs('risk-categories.*') || 
                        request()->routeIs('strategic-objectives.*') || 
                        request()->routeIs('business-processes.*') ? 'top-menu--active' : '' 
                    }}">
                        <div class="top-menu__icon">
                            <i data-feather="database"></i>
                        </div>
                        <div class="top-menu__title">
                            Master Data
                            <i data-feather="chevron-down" class="top-menu__sub-icon"></i>
                        </div>
                    </a>
                    <ul class="top-menu__dropdown">
                        <li>
                            <a href="{{ route('organizations.index') }}" class="top-menu {{ request()->routeIs('organizations.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="home"></i>
                                </div>
                                <div class="top-menu__title">Organisasi</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('projects.index') }}" class="top-menu {{ request()->routeIs('projects.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="briefcase"></i>
                                </div>
                                <div class="top-menu__title">Proyek</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('risk-categories.index') }}" class="top-menu {{ request()->routeIs('risk-categories.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="folder"></i>
                                </div>
                                <div class="top-menu__title">Kategori Risiko</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('strategic-objectives.index') }}" class="top-menu {{ request()->routeIs('strategic-objectives.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="target"></i>
                                </div>
                                <div class="top-menu__title">Tujuan Strategis</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('business-processes.index') }}" class="top-menu {{ request()->routeIs('business-processes.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="git-merge"></i>
                                </div>
                                <div class="top-menu__title">Proses Bisnis</div>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Manajemen Risiko (Admin) -->
                <li>
                    <a href="javascript:;" class="top-menu {{ 
                        request()->routeIs('risks.*') || 
                        request()->routeIs('risk-analyses.*') || 
                        request()->routeIs('risk-evaluations.*') ? 'top-menu--active' : '' 
                    }}">
                        <div class="top-menu__icon">
                            <i data-feather="alert-triangle"></i>
                        </div>
                        <div class="top-menu__title">
                            Manajemen Risiko
                            <i data-feather="chevron-down" class="top-menu__sub-icon"></i>
                        </div>
                    </a>
                    <ul class="top-menu__dropdown">
                        <li>
                            <a href="{{ route('risks.index') }}" class="top-menu {{ request()->routeIs('risks.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="list"></i>
                                </div>
                                <div class="top-menu__title">Data Risiko</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('risk-analyses.all') }}" class="top-menu {{ request()->routeIs('risk-analyses.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="bar-chart-2"></i>
                                </div>
                                <div class="top-menu__title">Analisis Risiko</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('risk-evaluations.index') }}" class="top-menu {{ request()->routeIs('risk-evaluations.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="star"></i>
                                </div>
                                <div class="top-menu__title">Evaluasi Risiko</div>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Audit & Laporan (Admin) -->
                <li>
                    <a href="javascript:;" class="top-menu {{ 
                        request()->routeIs('audits.*') || 
                        request()->routeIs('reports.*') || 
                        request()->routeIs('report-schedules.*') ? 'top-menu--active' : '' 
                    }}">
                        <div class="top-menu__icon">
                            <i data-feather="clipboard"></i>
                        </div>
                        <div class="top-menu__title">
                            Audit & Laporan
                            <i data-feather="chevron-down" class="top-menu__sub-icon"></i>
                        </div>
                    </a>
                    <ul class="top-menu__dropdown">
                        <li>
                            <a href="{{ route('audits.index') }}" class="top-menu {{ request()->routeIs('audits.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="check-square"></i>
                                </div>
                                <div class="top-menu__title">Audit Risiko</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.index') }}" class="top-menu {{ request()->routeIs('reports.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="file-text"></i>
                                </div>
                                <div class="top-menu__title">Laporan</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('report-schedules.index') }}" class="top-menu {{ request()->routeIs('report-schedules.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="calendar"></i>
                                </div>
                                <div class="top-menu__title">Jadwal Laporan</div>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Administrasi (Hanya Admin) -->
                <li>
                    <a href="javascript:;" class="top-menu {{ 
                        request()->routeIs('users.*') ? 'top-menu--active' : ''
                    }}">
                        <div class="top-menu__icon">
                            <i data-feather="settings"></i>
                        </div>
                        <div class="top-menu__title">
                            Administrasi
                            <i data-feather="chevron-down" class="top-menu__sub-icon"></i>
                        </div>
                    </a>
                    <ul class="top-menu__dropdown">
                        <li>
                            <a href="{{ route('users.index') }}" class="top-menu {{ request()->routeIs('users.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="users"></i>
                                </div>
                                <div class="top-menu__title">Kelola Pengguna</div>
                            </a>
                        </li>
                    </ul>
                </li>
                
            @elseif(Auth::user()->role === 'unit_pemilik_risiko')
                <!-- =================================== -->
                <!-- MENU UNTUK UNIT PEMILIK RISIKO (UPR) -->
                <!-- =================================== -->
                
                <!-- Proyek (UPR) -->
                <li>
                    <a href="{{ route('projects.index') }}" class="top-menu {{ request()->routeIs('projects.*') ? 'top-menu--active' : '' }}">
                        <div class="top-menu__icon">
                            <i data-feather="briefcase"></i>
                        </div>
                        <div class="top-menu__title">Proyek Saya</div>
                    </a>
                </li>
                
                <!-- Manajemen Risiko (UPR) -->
                <li>
                    <a href="javascript:;" class="top-menu {{ 
                        request()->routeIs('risks.*') || 
                        request()->routeIs('risk-identifications.*') || 
                        request()->routeIs('risk-analyses.*') || 
                        request()->routeIs('risk-mitigations.*') || 
                        request()->routeIs('risk-monitorings.*') ? 'top-menu--active' : '' 
                    }}">
                        <div class="top-menu__icon">
                            <i data-feather="alert-triangle"></i>
                        </div>
                        <div class="top-menu__title">
                            Manajemen Risiko
                            <i data-feather="chevron-down" class="top-menu__sub-icon"></i>
                        </div>
                    </a>
                    <ul class="top-menu__dropdown">
                        <li>
                            <a href="{{ route('risks.index') }}" class="top-menu {{ request()->routeIs('risks.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="list"></i>
                                </div>
                                <div class="top-menu__title">Data Risiko</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('risk-identifications.index') }}" class="top-menu {{ request()->routeIs('risk-identifications.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="search"></i>
                                </div>
                                <div class="top-menu__title">Identifikasi</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('risk-analyses.index') }}" class="top-menu {{ request()->routeIs('risk-analyses.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="bar-chart-2"></i>
                                </div>
                                <div class="top-menu__title">Analisis</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('risk-mitigations.index') }}" class="top-menu {{ request()->routeIs('risk-mitigations.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="shield"></i>
                                </div>
                                <div class="top-menu__title">Mitigasi</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('risk-monitorings.index') }}" class="top-menu {{ request()->routeIs('risk-monitorings.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="eye"></i>
                                </div>
                                <div class="top-menu__title">Pemantauan</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('risk-indicators.index') }}" class="top-menu {{ request()->routeIs('risk-indicators.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="trending-up"></i>
                                </div>
                                <div class="top-menu__title">Indikator</div>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Laporan (UPR) -->
                <li>
                    <a href="{{ route('reports.index') }}" class="top-menu {{ request()->routeIs('reports.*') ? 'top-menu--active' : '' }}">
                        <div class="top-menu__icon">
                            <i data-feather="file-text"></i>
                        </div>
                        <div class="top-menu__title">Laporan Saya</div>
                    </a>
                </li>
                
            @elseif(Auth::user()->role === 'auditor')
                <!-- ====================== -->
                <!-- MENU UNTUK AUDITOR -->
                <!-- ====================== -->
                
                <!-- Review Risiko (Auditor) -->
                <li>
                    <a href="javascript:;" class="top-menu {{ 
                        request()->routeIs('risk-evaluations.*') || 
                        request()->routeIs('audits.*') ? 'top-menu--active' : '' 
                    }}">
                        <div class="top-menu__icon">
                            <i data-feather="check-square"></i>
                        </div>
                        <div class="top-menu__title">
                            Review Risiko
                            <i data-feather="chevron-down" class="top-menu__sub-icon"></i>
                        </div>
                    </a>
                    <ul class="top-menu__dropdown">
                        <li>
                            <a href="{{ route('risk-evaluations.index') }}" class="top-menu {{ request()->routeIs('risk-evaluations.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="star"></i>
                                </div>
                                <div class="top-menu__title">Evaluasi Risiko</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('audits.index') }}" class="top-menu {{ request()->routeIs('audits.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="check-circle"></i>
                                </div>
                                <div class="top-menu__title">Audit Risiko</div>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Laporan (Auditor) -->
                <li>
                    <a href="javascript:;" class="top-menu {{ 
                        request()->routeIs('reports.*') || 
                        request()->routeIs('report-schedules.*') ? 'top-menu--active' : '' 
                    }}">
                        <div class="top-menu__icon">
                            <i data-feather="clipboard"></i>
                        </div>
                        <div class="top-menu__title">
                            Laporan
                            <i data-feather="chevron-down" class="top-menu__sub-icon"></i>
                        </div>
                    </a>
                    <ul class="top-menu__dropdown">
                        <li>
                            <a href="{{ route('reports.index') }}" class="top-menu {{ request()->routeIs('reports.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="file-text"></i>
                                </div>
                                <div class="top-menu__title">Lihat Laporan</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('report-schedules.index') }}" class="top-menu {{ request()->routeIs('report-schedules.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="calendar"></i>
                                </div>
                                <div class="top-menu__title">Jadwal Laporan</div>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Data Master (View Only untuk Auditor) -->
                <li>
                    <a href="javascript:;" class="top-menu {{ 
                        request()->routeIs('projects.*') || 
                        request()->routeIs('risks.*') ? 'top-menu--active' : '' 
                    }}">
                        <div class="top-menu__icon">
                            <i data-feather="database"></i>
                        </div>
                        <div class="top-menu__title">
                            Data Referensi
                            <i data-feather="chevron-down" class="top-menu__sub-icon"></i>
                        </div>
                    </a>
                    <ul class="top-menu__dropdown">
                        <li>
                            <a href="{{ route('projects.index') }}" class="top-menu {{ request()->routeIs('projects.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="briefcase"></i>
                                </div>
                                <div class="top-menu__title">Proyek</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('risks.index') }}" class="top-menu {{ request()->routeIs('risks.*') ? 'top-menu--active' : '' }}">
                                <div class="top-menu__icon">
                                    <i data-feather="alert-triangle"></i>
                                </div>
                                <div class="top-menu__title">Data Risiko</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
        @endauth
    </ul>
</nav>
<!-- END: Top Menu -->