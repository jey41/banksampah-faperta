@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Detail Setoran #{{ $deposit->id }}" subtitle="Transaksi" />

{{-- Action bar --}}
<div class="mb-4 flex flex-wrap items-center gap-2">
    <x-admin.btn-secondary href="{{ route('cms.deposits.index') }}">← Kembali</x-admin.btn-secondary>
    @if ($deposit->status === 'pending' && auth()->user()->can('reject', $deposit))
        <form method="POST" action="{{ route('cms.deposits.reject', $deposit) }}" onsubmit="return confirm('Tolak setoran ini?')">
            @csrf
            <button type="submit" class="rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">✕ Tolak</button>
        </form>
    @endif
    @if ($deposit->status !== 'pending')
        <x-admin.btn-secondary href="{{ route('admin.deposit.print', $deposit) }}" target="_blank">🖨 Cetak Struk</x-admin.btn-secondary>
    @endif
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- Info setoran --}}
    <div class="lg:col-span-1">
        <x-admin.card title="Info Setoran">
            <div class="space-y-3 text-sm">
                <div><span class="text-slate-500">Nasabah</span><p class="font-medium text-slate-800">{{ $deposit->user?->name ?? '-' }}</p></div>
                <div><span class="text-slate-500">Status</span><p><x-admin.badge :value="$deposit->status" /></p></div>
                <div><span class="text-slate-500">Kategori</span><p class="text-slate-800">{{ $deposit->is_donation ? 'Donasi' : 'Tabungan' }}</p></div>
                <div><span class="text-slate-500">Total Berat</span><p class="text-lg font-bold text-slate-800">{{ number_format($deposit->weight_total ?? 0, 2) }} kg/L</p></div>
                <div><span class="text-slate-500">Total Nilai</span><p class="text-lg font-bold text-primary-600">Rp {{ number_format($deposit->total_price ?? 0, 0, ',', '.') }}</p></div>
                @if ($deposit->validator)
                    <div><span class="text-slate-500">Diverifikasi</span><p class="text-slate-800">{{ $deposit->validator->name }}</p></div>
                @endif
                <div><span class="text-slate-500">Tanggal</span><p class="text-slate-800">{{ $deposit->created_at->translatedFormat('d M Y H:i') }}</p></div>
                @if ($deposit->notes)
                    <div><span class="text-slate-500">Catatan</span><p class="text-slate-600">{{ $deposit->notes }}</p></div>
                @endif
            </div>
        </x-admin.card>
    </div>

    {{-- Items --}}
    <div class="lg:col-span-2">
        <x-admin.card title="Item Sampah"
            icon='<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'>
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="pb-2 font-medium text-slate-600">Jenis Sampah</th>
                        <th class="pb-2 font-medium text-slate-600">Harga/kg</th>
                        <th class="pb-2 font-medium text-slate-600">Berat</th>
                        <th class="pb-2 font-medium text-slate-600">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach ($deposit->items as $item)
                    <tr>
                        <td class="py-2 text-slate-800">{{ $item->trashPrice?->name ?? '?' }}</td>
                        <td class="py-2 text-slate-600">Rp {{ number_format($item->price_per_unit ?? 0, 0, ',', '.') }}</td>
                        <td class="py-2 text-slate-600">{{ number_format($item->weight, 2) }} kg</td>
                        <td class="py-2 text-slate-700 font-medium">Rp {{ number_format($item->total_price ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </x-admin.card>

        {{-- Approve modal form --}}
        @if ($deposit->status === 'pending' && auth()->user()->can('approve', $deposit))
        <x-admin.card title="Timbang & Setujui" class="mt-6">
            <form method="POST" action="{{ route('cms.deposits.approve', $deposit) }}">
                @csrf
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="pb-2 pr-2 font-medium text-slate-600">Jenis</th>
                            <th class="pb-2 px-2 font-medium text-slate-600">Berat Awal</th>
                            <th class="pb-2 pl-2 font-medium text-slate-600">Berat Riil <span class="text-red-500">*</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($deposit->items as $item)
                        <tr>
                            <td class="py-2 pr-2 text-slate-800">{{ $item->trashPrice?->name ?? '?' }}</td>
                            <td class="py-2 px-2 text-slate-600">{{ number_format($item->weight, 2) }} kg</td>
                            <td class="py-2 pl-2">
                                <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                <input type="number" step="0.01" min="0.01" name="items[{{ $loop->index }}][weight]"
                                    value="{{ old('items.'.$loop->index.'.weight', $item->weight) }}" required
                                    class="w-28 rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    <x-admin.btn type="submit" color="success">✓ Setujui & Tambahkan Saldo</x-admin.btn>
                </div>
            </form>
        </x-admin.card>
        @endif
    </div>
</div>
@endsection