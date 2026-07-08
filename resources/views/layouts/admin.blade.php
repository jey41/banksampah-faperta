<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Bank Sampah Faperta CMS</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',300:'#6ee7b7',400:'#34d399',
                            500:'#10b981',600:'#059669',700:'#047857',800:'#065f46',900:'#064e3b'
                        }
                    },
                    fontFamily: { sans: ['Plus Jakarta Sans','ui-sans-serif','system-ui','sans-serif'] }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- jQuery + DataTables --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.tailwindcss.min.css">
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    @stack('head')
</head>
<body class="h-full bg-slate-100 font-sans text-slate-700 antialiased">
<div x-data class="min-h-full">
    {{-- Sidebar (mobile toggle via checkbox, no JS framework needed) --}}
    <input type="checkbox" id="sidebar-toggle" class="peer hidden">

    @include('admin.partials.sidebar')

    {{-- Overlay for mobile --}}
    <label for="sidebar-toggle"
           class="fixed inset-0 z-20 hidden bg-slate-900/50 peer-checked:block lg:!hidden"></label>

    <div class="lg:pl-64">
        @include('admin.partials.navbar')

        {{-- Spacer untuk navbar fixed --}}
        <div class="h-24 w-full"></div>

        <main class="p-4 sm:p-6 lg:p-8">
            @include('admin.partials.breadcrumbs')
            @include('admin.partials.alerts')

            @yield('content')
        </main>

        <footer class="px-6 py-4 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} Bank Sampah Faperta — Panel Admin CMS
        </footer>
    </div>
</div>

@stack('modals')

<script>
    // Konfirmasi hapus generik
    function confirmDelete(formId, message) {
        if (window.confirm(message || 'Yakin ingin menghapus data ini? Tindakan tidak dapat dibatalkan.')) {
            document.getElementById(formId).submit();
        }
    }

    // Init DataTables on all tables with class "datatable"
    if (typeof jQuery !== 'undefined' && $.fn.DataTable) {
        $(document).ready(function () {
            $('table.datatable').each(function () {
                const dt = $(this);
                // Skip if already initialized
                if (dt.hasClass('dataTable')) return;
                
                // Fix for DataTables incorrect column count warning on empty tables
                const tbody = dt.find('tbody');
                if (tbody.find('tr').length === 1 && tbody.find('td[colspan]').length === 1) {
                    tbody.empty();
                }
                // Respect "no-sort" class on header th
                const columnDefs = [];
                dt.find('thead th.no-sort').each(function () {
                    columnDefs.push({ orderable: false, targets: $(this).index() });
                });
                dt.DataTable({
                    paging: false,
                    info: false,
                    searching: false,
                    order: [],
                    columnDefs: columnDefs,
                    language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' }
                });
            });
        });
    }
</script>
@stack('scripts')
</body>
</html>
