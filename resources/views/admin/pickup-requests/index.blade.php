@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Permintaan Jemput" subtitle="Transaksi" />

<div class="mb-4">
    <form method="GET" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nasabah..."
            class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
        <select name="status" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
            <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Ditugaskan</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
        <button type="submit" class="rounded-lg bg-slate-100 px-4 py-2 text-sm text-slate-600 hover:bg-slate-200">Filter</button>
    </form>
</div>

<x-admin.table-wrapper>
    <table class="datatable w-full text-left text-sm">
        <thead>
            <tr>
                <th class="px-4 py-3 font-medium text-slate-600">Nasabah</th>
                <th class="px-4 py-3 font-medium text-slate-600">Alamat</th>
                <th class="px-4 py-3 font-medium text-slate-600">Tgl Jemput</th>
                <th class="px-4 py-3 font-medium text-slate-600">Status</th>
                <th class="px-4 py-3 font-medium text-slate-600">Petugas</th>
                <th class="no-sort px-4 py-3 font-medium text-slate-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($pickups as $p)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-800">{{ $p->user?->name ?? '-' }}</td>
                <td class="max-w-xs truncate px-4 py-3 text-slate-600" title="{{ $p->pickup_address }}">{{ $p->pickup_address }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $p->pickup_date?->translatedFormat('d M Y') ?? '-' }} {{ $p->pickup_time ?? '' }}</td>
                <td class="px-4 py-3"><x-admin.badge :value="$p->status" /></td>
                <td class="px-4 py-3 text-slate-600">{{ $p->assignedPetugas?->name ?? '-' }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('cms.pickup-requests.edit', $p) }}"
                        class="rounded-lg px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50">Kelola</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada permintaan jemput.</td></tr>
            @endforelse
        </tbody>
    </table>
</x-admin.table-wrapper>
{{ $pickups->links('components.admin.pagination', ['paginator' => $pickups]) }}
@endsection