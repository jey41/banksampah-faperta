@php
    $navActive = fn($pattern) => request()->routeIs($pattern)
        ? 'bg-primary-600 text-white shadow-sm'
        : 'text-slate-300 hover:bg-slate-700/60 hover:text-white';
@endphp

<aside class="fixed inset-y-0 left-0 z-30 w-64 -translate-x-full overflow-y-auto bg-slate-900 transition-transform duration-200 peer-checked:translate-x-0 lg:translate-x-0">
    {{-- Brand --}}
    <div class="flex h-16 items-center gap-2 border-b border-slate-800 px-6">
        <span class="text-2xl">♻️</span>
        <div class="leading-tight">
            <p class="text-sm font-bold text-white">Bank Sampah</p>
            <p class="text-xs text-primary-400">Faperta CMS</p>
        </div>
    </div>

    <nav class="space-y-6 px-3 py-5 text-sm">
        {{-- Dashboard --}}
        <div>
            <a href="{{ route('cms.dashboard') }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2 font-medium {{ $navActive('cms.dashboard') }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
        </div>

        {{-- Master Data --}}
        <div>
            <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Master Data</p>
            <div class="space-y-1">
                @can('viewAny', App\Models\TrashPrice::class)
                <a href="{{ route('cms.trash-prices.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $navActive('cms.trash-prices.*') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    Harga Sampah
                </a>
                @endcan
                <a href="{{ route('cms.badges.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $navActive('cms.badges.*') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Lencana
                </a>
            </div>
        </div>

        {{-- Transaksi --}}
        <div>
            <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Transaksi</p>
            <div class="space-y-1">
                @can('viewAny', App\Models\Deposit::class)
                <a href="{{ route('cms.deposits.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $navActive('cms.deposits.*') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Setoran Sampah
                </a>
                @endcan
                @can('viewAny', App\Models\Withdrawal::class)
                <a href="{{ route('cms.withdrawals.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $navActive('cms.withdrawals.*') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8v-1a3 3 0 013-3h10a3 3 0 013 3v1m-4 4l-4-4m0 0l-4 4m4-4v12"/></svg>
                    Penarikan Saldo
                </a>
                @endcan
                @can('viewAny', App\Models\PickupRequest::class)
                <a href="{{ route('cms.pickup-requests.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $navActive('cms.pickup-requests.*') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/></svg>
                    Permintaan Jemput
                </a>
                @endcan
            </div>
        </div>

        {{-- Konten --}}
        <div>
            <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Manajemen Konten</p>
            <div class="space-y-1">
                @can('viewAny', App\Models\Article::class)
                <a href="{{ route('cms.articles.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $navActive('cms.articles.*') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    Artikel
                </a>
                @endcan
            </div>
        </div>

        {{-- Landing Page --}}
        <div>
            <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Landing Page</p>
            <div class="space-y-1">
                <a href="{{ route('cms.site-settings.hero') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $navActive('cms.site-settings.hero*') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Hero Section
                </a>
                <a href="{{ route('cms.site-settings.workflow') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $navActive('cms.site-settings.workflow*') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Alur Kerja
                </a>
                <a href="{{ route('cms.site-settings.schedule') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $navActive('cms.site-settings.schedule*') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Jadwal Operasional
                </a>
                <a href="{{ route('cms.site-settings.partners') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $navActive('cms.site-settings.partners*') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm8-4a4 4 0 10-4-4 4 4 0 004 4z"/></svg>
                    Mitra Kerja Sama
                </a>
            </div>
        </div>

        {{-- Pengguna --}}
        <div>
            <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Manajemen Pengguna</p>
            <div class="space-y-1">
                @can('viewAny', App\Models\User::class)
                <a href="{{ route('cms.users.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $navActive('cms.users.*') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z"/></svg>
                    Pengguna
                </a>
                @endcan
            </div>
        </div>

        {{-- Sistem --}}
        <div>
            <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Sistem</p>
            <div class="space-y-1">
                @can('viewAny', App\Models\ActivityLog::class)
                <a href="{{ route('cms.activity-logs.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $navActive('cms.activity-logs.*') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Log Aktivitas
                </a>
                @endcan
                <a href="{{ route('cms.profile.edit') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ $navActive('cms.profile.*') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profil
                </a>
            </div>
        </div>
    </nav>
</aside>
