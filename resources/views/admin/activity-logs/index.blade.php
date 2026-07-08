@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Log Aktivitas" subtitle="Sistem" />

<div class="mb-4">
    <form method="GET" class="flex flex-wrap gap-3">
        <select name="action" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">Semua Aksi</option>
            @foreach ($actions as $a)
                <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
        </select>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari deskripsi..."
            class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
        <button type="submit" class="rounded-lg bg-slate-100 px-4 py-2 text-sm text-slate-600 hover:bg-slate-200">Filter</button>
    </form>
</div>

<x-admin.card>
    <div class="divide-y divide-slate-100">
        @forelse ($logs as $log)
        <div class="flex items-start gap-3 py-3 text-sm">
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-500">
                {{ strtoupper(substr($log->user?->name ?? '?', 0, 1)) }}
            </span>
            <div>
                <p class="text-slate-700">{{ $log->description }}</p>
                <p class="text-xs text-slate-400">
                    {{ $log->user?->name ?? 'System' }} • {{ $log->created_at->translatedFormat('d M Y H:i:s') }}
                </p>
            </div>
        </div>
        @empty
        <p class="py-8 text-center text-sm text-slate-400">Belum ada log aktivitas.</p>
        @endforelse
    </div>
</x-admin.card>

{{ $logs->links('components.admin.pagination', ['paginator' => $logs]) }}
@endsection