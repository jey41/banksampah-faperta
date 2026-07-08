@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Kelola Jemput #{{ $pickupRequest->id }}" subtitle="Transaksi" />

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-1">
        <x-admin.card title="Info Permintaan">
            <div class="space-y-3 text-sm">
                <div><span class="text-slate-500">Nasabah</span><p class="font-medium text-slate-800">{{ $pickupRequest->user?->name ?? '-' }}</p></div>
                <div><span class="text-slate-500">No. Telepon</span><p class="text-slate-800">{{ $pickupRequest->pickup_phone ?? '-' }}</p></div>
                <div><span class="text-slate-500">Alamat</span><p class="text-slate-600">{{ $pickupRequest->pickup_address }}</p></div>
                <div><span class="text-slate-500">Tanggal/Jam</span><p class="text-slate-800">{{ $pickupRequest->pickup_date?->translatedFormat('d M Y') ?? '-' }} {{ $pickupRequest->pickup_time ?? '' }}</p></div>
                <div><span class="text-slate-500">Estimasi Jarak</span><p class="text-slate-800">{{ $pickupRequest->estimated_distance ? $pickupRequest->estimated_distance.' km' : '-' }}</p></div>
                @if ($pickupRequest->latitude && $pickupRequest->longitude)
                    <div><span class="text-slate-500">Koordinat</span><p class="text-slate-600 text-xs">{{ $pickupRequest->latitude }}, {{ $pickupRequest->longitude }}</p></div>
                    <a href="https://www.google.com/maps?q={{ $pickupRequest->latitude }},{{ $pickupRequest->longitude }}" target="_blank"
                        class="inline-flex items-center gap-1 text-sm text-primary-600 hover:underline">Lihat di Google Maps</a>
                @endif
                @if ($pickupRequest->notes)
                    <div><span class="text-slate-500">Catatan</span><p class="text-slate-600">{{ $pickupRequest->notes }}</p></div>
                @endif
            </div>
        </x-admin.card>
    </div>

    <div class="lg:col-span-2">
        <x-admin.card title="Update Status & Penugasan">
            <form method="POST" action="{{ route('cms.pickup-requests.update', $pickupRequest) }}">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Status <span class="text-red-500">*</span></label>
                        <select name="status" required
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                            <option value="pending" {{ $pickupRequest->status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="assigned" {{ $pickupRequest->status === 'assigned' ? 'selected' : '' }}>Ditugaskan</option>
                            <option value="completed" {{ $pickupRequest->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ $pickupRequest->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Petugas Ditugaskan</label>
                        <select name="assigned_to"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                            <option value="">— Pilih Petugas —</option>
                            @foreach ($petugas as $p)
                                <option value="{{ $p->id }}" {{ $pickupRequest->assigned_to == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6">
                    <x-admin.btn type="submit">Simpan Perubahan</x-admin.btn>
                </div>
            </form>
        </x-admin.card>
    </div>
</div>
@endsection