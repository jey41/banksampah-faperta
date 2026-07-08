@hasSection('breadcrumbs')
<nav class="mb-4 flex items-center text-sm text-slate-400" aria-label="Breadcrumb">
    <a href="{{ route('cms.dashboard') }}" class="hover:text-primary-600">Dashboard</a>
    @yield('breadcrumbs')
</nav>
@endif
