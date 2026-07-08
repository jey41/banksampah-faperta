@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Detail Permintaan Jemput #{{ $pickupRequest->id }}" subtitle="Transaksi" :actions='[
    "<a href=\"" . route("cms.pickup-requests.edit", $pickupRequest) . "\" class=\"inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700\">Kelola</a>"
]' />

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-1">
        <x-admin.card title="Info Permintaan">
            <div class="space-y-3 text-sm">
                <div><span class="text-slate-500">Nasabah</span><p class="font-medium">{{ $pickupRequest->user?->name ?? '-' }}</p></div>
                <div><span class="text-slate-500">Status</span><p><x-admin.badge :value="$pickupRequest->status" /></p></div>
                <div><span class="text-slate-500">Alamat</span><p class="text-slate-600">{{ $pickupRequest->pickup_address }}</p></div>
                <div><span class="text-slate-500">Telepon</span><p class="text-slate-800">{{ $pickupRequest->pickup_phone ?? '-' }}</p></div>
                <div><span class="text-slate-500">Tanggal/Jam</span><p class="text-slate-800">{{ $pickupRequest->pickup_date?->translatedFormat('d M Y') ?? '-' }} {{ $pickupRequest->pickup_time ?? '' }}</p></div>
                <div><span class="text-slate-500">Petugas</span><p class="text-slate-800">{{ $pickupRequest->assignedPetugas?->name ?? '-' }}</p></div>
                <div><span class="text-slate-500">Jarak</span><p class="text-slate-800">{{ $pickupRequest->estimated_distance ? $pickupRequest->estimated_distance.' km' : '-' }}</p></div>
                @if ($pickupRequest->notes)<div><span class="text-slate-500">Catatan</span><p class="text-slate-600">{{ $pickupRequest->notes }}</p></div>@endif
            </div>
        </x-admin.card>
    </div>
    <div class="lg:col-span-2">
        <x-admin.card title="Lokasi">
            @if ($pickupRequest->latitude && $pickupRequest->longitude)
                <div id="map" style="height:300px" class="rounded-lg bg-slate-100"></div>
                <a href="https://www.google.com/maps?q={{ $pickupRequest->latitude }},{{ $pickupRequest->longitude }}" target="_blank"
                    class="mt-3 inline-flex items-center gap-1 text-sm text-primary-600 hover:underline">Buka di Google Maps →</a>
            @else
                <p class="text-sm text-slate-400">Tidak ada data koordinat.</p>
            @endif
        </x-admin.card>
    </div>
</div>
@endsection

@if ($pickupRequest->latitude && $pickupRequest->longitude)
@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush
@push('scripts')
<script>
var map = L.map('map').setView([{{ $pickupRequest->latitude }}, {{ $pickupRequest->longitude }}], 15);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
}).addTo(map);
L.marker([{{ $pickupRequest->latitude }}, {{ $pickupRequest->longitude }}])
 .addTo(map)
 .bindPopup('{{ addslashes($pickupRequest->pickup_address) }}');
</script>
@endpush
@endif