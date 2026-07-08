@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Setoran Sampah" subtitle="Transaksi" />

<div class="mb-4">
    <form method="GET" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nasabah..."
            class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
        <select name="status" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <select name="donation_category" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">Semua Kategori</option>
            <option value="umum" {{ request('donation_category') === 'umum' ? 'selected' : '' }}>Tabungan</option>
            <option value="donasi" {{ request('donation_category') === 'donasi' ? 'selected' : '' }}>Donasi</option>
        </select>
        <button type="submit" class="rounded-lg bg-slate-100 px-4 py-2 text-sm text-slate-600 hover:bg-slate-200">Filter</button>
    </form>
</div>

<x-admin.table-wrapper>
    <table class="datatable w-full text-left text-sm">
        <thead>
            <tr>
                <th class="px-4 py-3 font-medium text-slate-600">Nasabah</th>
                <th class="px-4 py-3 font-medium text-slate-600">Total Uang</th>
                <th class="px-4 py-3 font-medium text-slate-600">Berat</th>
                <th class="px-4 py-3 font-medium text-slate-600">Kategori</th>
                <th class="px-4 py-3 font-medium text-slate-600">Status</th>
                <th class="px-4 py-3 font-medium text-slate-600">Tgl Masuk</th>
                <th class="no-sort px-4 py-3 font-medium text-slate-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($deposits as $d)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-800">{{ $d->user?->name ?? '-' }}</td>
                <td class="px-4 py-3 text-slate-700">Rp {{ number_format($d->total_price ?? 0, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-slate-600">{{ number_format($d->weight_total ?? 0, 2) }} kg/L</td>
                <td class="px-4 py-3">
                    @if ($d->is_donation)
                        <span class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-semibold text-rose-700">Donasi</span>
                    @else
                        <span class="text-xs text-slate-500">Tabungan</span>
                    @endif
                </td>
                <td class="px-4 py-3"><x-admin.badge :value="$d->status" /></td>
                <td class="px-4 py-3 text-slate-500">{{ $d->created_at->translatedFormat('d M Y, H:i') }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('cms.deposits.show', $d) }}"
                        class="rounded-lg px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50">Detail</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">Belum ada setoran.</td></tr>
            @endforelse
        </tbody>
    </table>
</x-admin.table-wrapper>
{{ $deposits->links('components.admin.pagination', ['paginator' => $deposits]) }}
@endsection