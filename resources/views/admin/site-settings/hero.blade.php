@extends('layouts.admin')

@section('title', 'Hero Section')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Hero Section</h1>
        <p class="text-sm text-slate-500">Kelola gambar dan teks utama di bagian paling atas landing page.</p>
    </div>

    @if(session('success'))
    <div class="rounded-xl bg-green-50 p-4 text-sm text-green-800 border border-green-200">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('cms.site-settings.hero.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl overflow-hidden">
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Gambar Background Hero</label>
                    @if(isset($settings['hero_image']))
                    <div class="mb-3">
                        <img src="{{ $settings['hero_image'] }}" alt="Hero Image" class="h-40 w-auto object-cover rounded-lg border border-slate-200">
                    </div>
                    @endif
                    <input type="file" name="hero_image_file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    <p class="text-xs text-slate-500 mt-1">Biarkan kosong jika tidak ingin mengubah gambar.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Judul Baris 1</label>
                        <input type="text" name="hero_title_1" value="{{ $settings['hero_title_1'] ?? '' }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Judul Baris 2 <span class="text-slate-400">(warna aksen)</span></label>
                        <input type="text" name="hero_title_2" value="{{ $settings['hero_title_2'] ?? '' }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Subtitle</label>
                    <textarea name="hero_subtitle" rows="3" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">{{ $settings['hero_subtitle'] ?? '' }}</textarea>
                </div>
            </div>
            <div class="border-t border-slate-200 bg-slate-50 px-6 py-4 flex justify-end">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
