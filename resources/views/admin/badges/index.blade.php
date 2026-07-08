@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Lencana (Badge)" subtitle="Master Data" />

<div class="mb-6">
    <x-admin.card title="Level Eco-Points">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 lg:grid-cols-5">
            @foreach ($levels as $level)
            <div class="rounded-lg border border-slate-200 p-4 text-center">
                <span class="text-3xl">{{ $level['emoji'] }}</span>
                <p class="mt-2 text-sm font-semibold text-slate-800">{{ $level['name'] }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ number_format($level['min_points']) }} poin</p>
            </div>
            @endforeach
        </div>
    </x-admin.card>
</div>

<x-admin.card title="Katalog Lencana">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($badges as $badge)
        <div class="rounded-lg border border-slate-200 p-4">
            <div class="flex items-start gap-3">
                <span class="text-3xl">{{ $badge['icon'] }}</span>
                <div>
                    <h4 class="text-sm font-semibold text-slate-800">{{ $badge['name'] }}</h4>
                    <p class="text-xs text-slate-500">{{ $badge['description'] }}</p>
                    <p class="mt-1 text-xs text-slate-400">Syarat: {{ $badge['requirement'] }}</p>
                    <p class="mt-1 text-xs font-medium text-primary-600">{{ $badge['unlocked_count'] }} nasabah telah membuka</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</x-admin.card>
@endsection