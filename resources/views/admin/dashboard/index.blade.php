@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Dashboard" subtitle="Overview">
    <x-slot name="actions">
        <form action="{{ route('cms.visits.cleanup') }}" method="POST" class="flex items-center gap-2" onsubmit="return confirm('Yakin ingin membersihkan data kunjungan?');">
            @csrf
            <select name="period" class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-emerald-600 sm:text-sm sm:leading-6">
                <option value="1_month">1 Bulan</option>
                <option value="3_months">3 Bulan</option>
                <option value="6_months" selected>6 Bulan</option>
                <option value="1_year">1 Tahun</option>
            </select>
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-transparent bg-red-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Clean Up
            </button>
        </form>
    </x-slot>
</x-admin.page-header>

{{-- Grafik Pengunjung --}}
<div class="mb-6">
    <x-admin.card title="Statistik Pengunjung"
        icon='<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>'>
        <div class="flex justify-end mb-4">
            <select id="visitorPeriodFilter" class="block w-48 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                <option value="daily">7 Hari Terakhir</option>
                <option value="weekly">8 Minggu Terakhir</option>
                <option value="monthly">6 Bulan Terakhir</option>
            </select>
        </div>
        <canvas id="visitorChart" height="80"></canvas>
    </x-admin.card>
</div>

{{-- Stat Cards --}}
<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
    <x-admin.stat-card
        label="Pengunjung Website"
        value="{{ number_format($uniqueVisitorsMonth, 0, ',', '.') }}"
        description="{{ number_format($totalViewsMonth, 0, ',', '.') }} views • {{ number_format($uniqueVisitorsToday, 0, ',', '.') }} hari ini"
        icon='<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>'
        color="sky" />
    <x-admin.stat-card
        label="Keuntungan Donasi"
        value="Rp {{ number_format($totalDonationProfit, 0, ',', '.') }}"
        description="Total nilai donasi/sedekah"
        icon='<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>'
        color="green" />
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between">
            <div class="flex-1 pr-2">
                <div class="flex justify-between items-center mb-1">
                    <p class="text-sm font-medium text-slate-500">Volume Sampah</p>
                    <select id="volumeFilter" class="block w-24 rounded-md border-0 py-0.5 pl-2 pr-6 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-primary-600 text-xs">
                        <option value="all">Semua</option>
                        <option value="donasi">Donasi</option>
                        <option value="tabungan">Tabungan</option>
                    </select>
                </div>
                <p id="volumeValue" class="text-2xl font-bold text-slate-800">{{ number_format($totalWeightAll, 2, ',', '.') }} kg/L</p>
                <p id="volumeDescription" class="mt-1 text-sm text-slate-500">Gabungan tabungan & donasi</p>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
        </div>
    </div>
    <x-admin.stat-card
        label="Saldo Mengendap"
        value="Rp {{ number_format($retainedBalance, 0, ',', '.') }}"
        description="Total tabungan nasabah"
        icon='<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>'
        color="amber" />
    <x-admin.stat-card
        label="Nasabah Aktif"
        value="{{ $activeRatio }}%"
        description="{{ $activeNasabah }} dari {{ $totalNasabah }} nasabah (30 hari)"
        icon='<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'
        color="{{ $activeRatio > 50 ? 'primary' : 'slate' }}" />
</div>

{{-- Row: Today's Summary + Recent Activity --}}
<div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
    <x-admin.card title="Ringkasan Hari Ini"
        icon='<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>'>
        <div class="space-y-3">
            @php $todayRows = [
                ['Setoran Masuk', $depositsToday, route('cms.deposits.index'), 'text-green-600'],
                ['Penarikan', $withdrawalsToday, route('cms.withdrawals.index'), 'text-blue-600'],
                ['Jemput Menunggu', $pendingPickups, route('cms.pickup-requests.index'), 'text-amber-600'],
                ['Setoran Pending', $pendingDeposits, route('cms.deposits.index', ['status' => 'pending']), 'text-amber-600'],
                ['Penarikan Pending', $pendingWithdrawals, route('cms.withdrawals.index', ['status' => 'pending']), 'text-red-600'],
            ]; @endphp
            @foreach ($todayRows as $r)
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600">{{ $r[0] }}</span>
                <a href="{{ $r[2] }}" class="text-lg font-bold {{ $r[3] }} hover:underline">{{ $r[1] }}</a>
            </div>
            @endforeach
        </div>
    </x-admin.card>

    <div class="lg:col-span-2">
        <x-admin.card title="Aktivitas Terbaru"
            icon='<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>'>
            <div class="-mx-6 -mb-6">
                <div class="max-h-72 divide-y divide-slate-100 overflow-y-auto px-6">
                    @forelse ($recentActivities as $log)
                    <div class="flex items-start gap-3 py-3 text-sm">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-500">
                            {{ strtoupper(substr($log->user?->name ?? '?', 0, 1)) }}
                        </span>
                        <div>
                            <p class="text-slate-700">{{ $log->description }}</p>
                            <p class="text-xs text-slate-400">{{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="py-8 text-center text-sm text-slate-400">Belum ada aktivitas.</p>
                    @endforelse
                </div>
            </div>
        </x-admin.card>
    </div>
</div>

{{-- Grafik Setoran & Donasi --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <x-admin.card title="Tren Setoran (7 Hari)"
        icon='<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>'>
        <canvas id="trendChart" height="120"></canvas>
    </x-admin.card>

    <x-admin.card title="Tabungan vs Donasi"
        icon='<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>'>
        <canvas id="pieChart" height="120"></canvas>
    </x-admin.card>

    <x-admin.card title="Perbandingan Jenis Sampah"
        icon='<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>'>
        <div class="flex justify-end mb-4">
            <select id="trashTypeFilter" class="block w-full rounded-md border-0 py-1 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6">
                <option value="all">Seluruh Sampah</option>
                <option value="donasi">Sampah Donasi</option>
                <option value="tabungan">Sampah Tabungan</option>
            </select>
        </div>
        <canvas id="trashTypeChart" height="120"></canvas>
    </x-admin.card>
</div>
@endsection

@push('scripts')
<script>
const visitorData = {
    daily: {
        labels: {!! json_encode($visitorDaily->pluck('label')) !!},
        views: {!! json_encode($visitorDaily->pluck('views')) !!},
        unique: {!! json_encode($visitorDaily->pluck('unique')) !!}
    },
    weekly: {
        labels: {!! json_encode($visitorWeekly->pluck('label')) !!},
        views: {!! json_encode($visitorWeekly->pluck('views')) !!},
        unique: {!! json_encode($visitorWeekly->pluck('unique')) !!}
    },
    monthly: {
        labels: {!! json_encode($visitorMonthly->pluck('label')) !!},
        views: {!! json_encode($visitorMonthly->pluck('views')) !!},
        unique: {!! json_encode($visitorMonthly->pluck('unique')) !!}
    }
};

const ctxVisitor = document.getElementById('visitorChart').getContext('2d');
const visitorChart = new Chart(ctxVisitor, {
    type: 'line',
    data: {
        labels: visitorData.daily.labels,
        datasets: [
            {
                label: 'Total Views',
                data: visitorData.daily.views,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                pointBackgroundColor: '#3b82f6',
                tension: 0.3,
                fill: true
            },
            {
                label: 'Pengunjung Unik',
                data: visitorData.daily.unique,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                pointBackgroundColor: '#10b981',
                tension: 0.3,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#e2e8f0' }, ticks: { precision: 0 } },
            x: { grid: { display: false } }
        }
    }
});

document.getElementById('visitorPeriodFilter').addEventListener('change', function(e) {
    const period = e.target.value;
    const data = visitorData[period];
    visitorChart.data.labels = data.labels;
    visitorChart.data.datasets[0].data = data.views;
    visitorChart.data.datasets[1].data = data.unique;
    visitorChart.update();
});

const trendLabels = {!! json_encode($trend->pluck('label')) !!};
const trendWeights = {!! json_encode($trend->pluck('weight')) !!};

new Chart(document.getElementById('trendChart'), {
    type: 'bar',
    data: {
        labels: trendLabels,
        datasets: [{
            label: 'Berat (kg/L)',
            data: trendWeights,
            backgroundColor: '#10b981',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#e2e8f0' } },
            x: { grid: { display: false } }
        }
    }
});

new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
        labels: ['Tabungan', 'Donasi'],
        datasets: [{
            data: [{{ $weightSavings }}, {{ $weightDonation }}],
            backgroundColor: ['#3b82f6', '#ef4444'],
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

const trashTypeLabels = {!! json_encode($trashTypeComparison->pluck('label')) !!};
const trashTypeWeights = {!! json_encode($trashTypeComparison->pluck('weight')) !!};

const trashChart = new Chart(document.getElementById('trashTypeChart'), {
    type: 'doughnut',
    data: {
        labels: trashTypeLabels,
        datasets: [{
            data: trashTypeWeights,
            backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#64748b'],
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// AJAX Filters
document.getElementById('volumeFilter').addEventListener('change', function(e) {
    const filter = e.target.value;
    fetch('{{ route("cms.dashboard.trash-stats") }}?type=volume&filter=' + filter)
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            document.getElementById('volumeValue').textContent = data.value;
            document.getElementById('volumeDescription').textContent = data.description;
        })
        .catch(error => {
            console.error('Error fetching volume stats:', error);
            document.getElementById('volumeValue').textContent = 'Error';
            document.getElementById('volumeDescription').textContent = 'Gagal memuat data. Silakan coba lagi.';
        });
});

document.getElementById('trashTypeFilter').addEventListener('change', function(e) {
    const filter = e.target.value;
    fetch('{{ route("cms.dashboard.trash-stats") }}?type=comparison&filter=' + filter)
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            trashChart.data.labels = data.labels;
            trashChart.data.datasets[0].data = data.weights;
            trashChart.update();
        })
        .catch(error => {
            console.error('Error fetching trash type stats:', error);
        });
});
</script>
@endpush