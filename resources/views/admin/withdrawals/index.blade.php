@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Penarikan Saldo" subtitle="Transaksi" />

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
        <button type="submit" class="rounded-lg bg-slate-100 px-4 py-2 text-sm text-slate-600 hover:bg-slate-200">Filter</button>
    </form>
</div>

<x-admin.table-wrapper>
    <table class="datatable w-full text-left text-sm">
        <thead>
            <tr>
                <th class="px-4 py-3 font-medium text-slate-600">Nasabah</th>
                <th class="px-4 py-3 font-medium text-slate-600">Jumlah</th>
                <th class="px-4 py-3 font-medium text-slate-600">Metode</th>
                <th class="px-4 py-3 font-medium text-slate-600">Status</th>
                <th class="px-4 py-3 font-medium text-slate-600">Admin Fee</th>
                <th class="px-4 py-3 font-medium text-slate-600">Tanggal</th>
                <th class="no-sort px-4 py-3 font-medium text-slate-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($withdrawals as $w)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-800">{{ $w->user?->name ?? '-' }}</td>
                <td class="px-4 py-3 text-slate-700 font-medium">Rp {{ number_format($w->amount, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $w->withdrawal_method === 'tunai' ? 'Tunai' : 'Transfer' }}</td>
                <td class="px-4 py-3"><x-admin.badge :value="$w->status" /></td>
                <td class="px-4 py-3 text-slate-600">{{ $w->admin_fee ? 'Rp '.number_format($w->admin_fee,0,',','.') : '-' }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $w->created_at->translatedFormat('d M Y, H:i') }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('cms.withdrawals.show', $w) }}"
                        class="rounded-lg px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50">Detail</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">Belum ada penarikan.</td></tr>
            @endforelse
        </tbody>
    </table>
</x-admin.table-wrapper>
{{ $withdrawals->links('components.admin.pagination', ['paginator' => $withdrawals]) }}
@endsection