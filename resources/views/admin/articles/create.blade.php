@extends('layouts.admin')

@section('content')
<x-admin.page-header title="{{ $article->exists ? 'Edit Artikel' : 'Tambah Artikel' }}" subtitle="Manajemen Konten" />

<x-admin.card>
    <form method="POST" action="{{ $article->exists ? route('cms.articles.update', $article) : route('cms.articles.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($article->exists) @method('PUT') @endif

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-admin.form.input name="title" label="Judul Artikel" :value="$article->title ?? ''" required />
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-700">Status <span class="text-red-500">*</span></label>
                <select name="status" required
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    <option value="draft" {{ ($article->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ ($article->status ?? '') === 'published' ? 'selected' : '' }}>Terbit</option>
                </select>
            </div>
        </div>

        <div class="mt-4 space-y-1">
            <label class="block text-sm font-medium text-slate-700">Konten Artikel <span class="text-red-500">*</span></label>
            <textarea name="content" rows="12" required
                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-mono focus:border-primary-500 focus:ring-1 focus:ring-primary-500">{{ old('content', $article->content ?? '') }}</textarea>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-slate-700">Gambar Sampul</label>
            @if ($article->exists && $article->image_url)
                <div class="mb-2">
                    <img src="{{ $article->image_url }}" class="h-32 w-56 rounded-lg object-cover">
                    <p class="mt-1 text-xs text-slate-400">Gambar saat ini. Upload gambar baru untuk mengganti.</p>
                </div>
            @endif
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                class="block w-full text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100">
            <p class="mt-1 text-xs text-slate-400">Maks 5MB, format JPG/PNG/WebP</p>
            @error('image') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mt-6 flex items-center gap-3">
            <x-admin.btn type="submit">{{ $article->exists ? 'Simpan Perubahan' : 'Simpan' }}</x-admin.btn>
            <x-admin.btn-secondary href="{{ route('cms.articles.index') }}">Batal</x-admin.btn-secondary>
        </div>
    </form>
</x-admin.card>
@endsection