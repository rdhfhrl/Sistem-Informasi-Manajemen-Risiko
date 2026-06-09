<div class="grid grid-cols-12 gap-6 mt-5">
    <!-- Total Users -->
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <i data-feather="users" class="report-box__icon text-primary"></i>
                </div>
                <div class="text-3xl font-medium leading-8 mt-6">{{ $stats['total_users'] ?? 0 }}</div>
                <div class="text-base text-slate-500 mt-1">Total Pengguna</div>
                <div class="mt-2 text-xs text-slate-500">
                    <i data-feather="user-check" class="w-4 h-4 mr-1"></i> 
                    {{ $stats['active_users'] ?? 0 }} Aktif
                </div>
            </div>
        </div>
    </div>

    <!-- Total Organizations -->
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <i data-feather="home" class="report-box__icon text-success"></i>
                </div>
                <div class="text-3xl font-medium leading-8 mt-6">{{ $stats['total_organizations'] ?? 0 }}</div>
                <div class="text-base text-slate-500 mt-1">Organisasi</div>
                <div class="mt-2 text-xs text-slate-500">
                    <i data-feather="layers" class="w-4 h-4 mr-1"></i> 
                    {{ $stats['uptd_count'] ?? 0 }} UPTD
                </div>
            </div>
        </div>
    </div>

    <!-- Active Projects -->
    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <i data-feather="alert-triangle" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="text-3xl font-medium leading-8 mt-6">{{ $stats['total_risks'] ?? 0 }}</div>
                <div class="text-base text-slate-500 mt-1">Total Risiko</div>
                <div class="mt-2 text-xs text-slate-500">
                    <i data-feather="activity" class="w-4 h-4 mr-1"></i> 
                    {{ $stats['total_risks'] ?? 0 }} Risiko Terdaftar
                </div>
            </div>
        </div>
    </div>
</div>