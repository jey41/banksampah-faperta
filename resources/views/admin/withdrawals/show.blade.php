@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Detail Penarikan #{{ $withdrawal->id }}" subtitle="Transaksi" />

{{-- Action bar --}}
<div class="mb-4 flex flex-wrap items-center gap-2">
    <x-admin.btn-secondary href="{{ route('cms.withdrawals.index') }}">← Kembali</x-admin.btn-secondary>
    @if ($withdrawal->status === 'pending' && auth()->user()->can('approve', $withdrawal))
        <form method="POST" action="{{ route('cms.withdrawals.approve', $withdrawal) }}"
              onsubmit="return confirm('Setujui penarikan Rp {{ number_format($withdrawal->amount, 0, ',', '.') }} untuk {{ $withdrawal->user?->name }}? Saldo akan dipotong.')">
            @csrf
            <x-admin.btn type="submit" color="success">✓ Setujui</x-admin.btn>
        </form>
    @endif
    @if ($withdrawal->status === 'pending' && auth()->user()->can('reject', $withdrawal))
        <form method="POST" action="{{ route('cms.withdrawals.reject', $withdrawal) }}" onsubmit="return confirm('Tolak penarikan ini?')">
            @csrf
            <button type="submit" class="rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">✕ Tolak</button>
        </form>
    @endif
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-1">
        <x-admin.card title="Info Penarikan">
            <div class="space-y-3 text-sm">
                <div><span class="text-slate-500">Nasabah</span><p class="font-medium text-slate-800">{{ $withdrawal->user?->name ?? '-' }}</p></div>
                <div><span class="text-slate-500">Status</span><p><x-admin.badge :value="$withdrawal->status" /></p></div>
                <div><span class="text-slate-500">Jumlah</span><p class="text-lg font-bold text-primary-600">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</p></div>
                <div><span class="text-slate-500">Metode</span><p class="text-slate-800">{{ $withdrawal->withdrawal_method === 'tunai' ? 'Tunai (Ambil di Lokasi)' : 'Transfer Bank' }}</p></div>
                @if ($withdrawal->bank_name)
                <div><span class="text-slate-500">Bank/E-Wallet</span><p class="text-slate-800">{{ $withdrawal->bank_name }}</p></div>
                @endif
                @if ($withdrawal->account_name)
                <div><span class="text-slate-500">Nama Penerima</span><p class="text-slate-800">{{ $withdrawal->account_name }}</p></div>
                @endif
                @if ($withdrawal->account_number)
                <div><span class="text-slate-500">No. Rekening</span><p class="text-slate-800">{{ $withdrawal->account_number }}</p></div>
                @endif
                <div><span class="text-slate-500">Admin Fee</span><p class="text-slate-800">{{ $withdrawal->admin_fee ? 'Rp '.number_format($withdrawal->admin_fee,0,',','.') : 'Tidak ada' }}</p></div>
                @if ($withdrawal->validator)
                    <div><span class="text-slate-500">Diverifikasi</span><p class="text-slate-800">{{ $withdrawal->validator->name }}</p></div>
                @endif
                <div><span class="text-slate-500">Tanggal</span><p class="text-slate-800">{{ $withdrawal->created_at->translatedFormat('d M Y H:i') }}</p></div>
                @if ($withdrawal->notes)
                    <div><span class="text-slate-500">Catatan</span><p class="text-slate-600">{{ $withdrawal->notes }}</p></div>
                @endif
            </div>
        </x-admin.card>
    </div>

    <div class="lg:col-span-2">
        @if ($withdrawal->history->count())
        <x-admin.card title="Riwayat Proses">
            <div class="space-y-3">
                @foreach ($withdrawal->history as $h)
                <div class="flex items-start gap-3 border-b border-slate-50 pb-3 text-sm">
                    <span class="mt-0.5">
                        @if ($h->status === 'approved')
                            <span class="text-green-500">✓</span>
                        @elseif ($h->status === 'rejected')
                            <span class="text-red-500">✕</span>
                        @else
                            <span class="text-amber-500">⟳</span>
                        @endif
                    </span>
                    <div>
                        <p class="text-slate-700">
                            {{ $h->status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                            oleh {{ $h->processor?->name ?? 'System' }}
                        </p>
                        @if ($h->notes)<p class="text-xs text-slate-500">{{ $h->notes }}</p>@endif
                        <p class="text-xs text-slate-400">{{ $h->processed_at?->translatedFormat('d M Y H:i') ?? '-' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </x-admin.card>
        @endif
    </div>
</div>
@endsection