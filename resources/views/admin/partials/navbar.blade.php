<header class="fixed top-0 z-10 flex h-16 items-center justify-between border-b border-slate-200 bg-white/95 backdrop-blur-sm px-4 sm:px-6 left-0 right-0 lg:left-64 shadow-sm">
    <div class="flex items-center gap-3">
        {{-- Mobile menu button --}}
        <label for="sidebar-toggle" class="cursor-pointer rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </label>
        <h1 class="text-base font-semibold text-slate-800">@yield('title', 'Dashboard')</h1>
    </div>

    <div class="flex items-center gap-3">
        <a href="{{ url('/') }}" target="_blank"
           class="hidden items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm text-slate-500 hover:bg-slate-100 sm:flex">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            Lihat Situs
        </a>

        {{-- User dropdown (pure CSS via group/focus) --}}
        <div class="group relative">
            <button class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-slate-100">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 text-sm font-semibold text-white">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
                <span class="hidden text-left sm:block">
                    <span class="block text-sm font-medium text-slate-800">{{ auth()->user()->name }}</span>
                    <span class="block text-xs capitalize text-slate-400">{{ auth()->user()->role }}</span>
                </span>
            </button>
            <div class="invisible absolute right-0 mt-1 w-48 rounded-lg border border-slate-200 bg-white py-1 opacity-0 shadow-lg transition-all group-focus-within:visible group-focus-within:opacity-100">
                <a href="{{ route('cms.profile.edit') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Profil Saya</a>
                <div class="my-1 border-t border-slate-100"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</header>
