@php use App\Models\Article; @endphp
@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Artikel" subtitle="Manajemen Konten" :actions='[
    auth()->user()->can("create", Article::class)
        ? "<a href=\"" . route("cms.articles.create") . "\" class=\"inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700\">+ Tambah Artikel</a>"
        : ""
]' />

<div class="mb-4">
    <form method="GET" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul..."
            class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
        <select name="status" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">Semua Status</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Terbit</option>
        </select>
        <button type="submit" class="rounded-lg bg-slate-100 px-4 py-2 text-sm text-slate-600 hover:bg-slate-200">Filter</button>
    </form>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($articles as $a)
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        @if ($a->image_url)
            <img src="{{ $a->image_url }}" alt="{{ $a->title }}" class="h-40 w-full object-cover">
        @else
            <div class="flex h-40 items-center justify-center bg-slate-100 text-slate-300">
                <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        @endif
        <div class="p-4">
            <div class="mb-2"><x-admin.badge :value="$a->status" /></div>
            <h4 class="text-sm font-semibold text-slate-800 line-clamp-2">{{ $a->title }}</h4>
            <p class="mt-1 text-xs text-slate-500">{{ $a->created_at->translatedFormat('d M Y') }}</p>
            <div class="mt-3 flex items-center gap-2">
                <a href="{{ route('cms.articles.edit', $a) }}"
                    class="rounded-lg px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50">Edit</a>
                @can('delete', $a)
                <form id="art-del-{{ $a->id }}" method="POST" action="{{ route('cms.articles.destroy', $a) }}" class="inline">
                    @csrf @method('DELETE')
                    <button type="button" onclick="confirmDelete('art-del-{{ $a->id }}', 'Hapus artikel {{ $a->title }}?')"
                        class="rounded-lg px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50">Hapus</button>
                </form>
                @endcan
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full py-12 text-center text-slate-400">Belum ada artikel.</div>
    @endforelse
</div>

{{ $articles->links('components.admin.pagination', ['paginator' => $articles]) }}
@endsection