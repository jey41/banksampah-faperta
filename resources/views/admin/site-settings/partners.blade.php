@extends('layouts.admin')

@section('title', 'Mitra Kerja Sama')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Mitra Kerja Sama</h1>
        <p class="text-sm text-slate-500">Kelola logo mitra yang tampil berjalan (infinite scroll) di landing page.</p>
    </div>

    @if(session('success'))
    <div class="rounded-xl bg-green-50 p-4 text-sm text-green-800 border border-green-200">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="rounded-xl bg-red-50 p-4 text-sm text-red-800 border border-red-200">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Daftar Mitra --}}
        <div class="lg:col-span-2">
            <div class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50/50 px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-900">Daftar Mitra ({{ $partners->count() }})</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($partners as $partner)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-20 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-center p-2">
                                <img src="{{ $partner->logo_path }}" alt="{{ $partner->name }}" class="max-h-full max-w-full object-contain">
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-slate-900">{{ $partner->name }}</span>
                                <p class="text-xs text-slate-500">Urutan: {{ $partner->order }}</p>
                            </div>
                        </div>
                        <form action="{{ route('cms.partners.destroy', $partner) }}" method="POST" onsubmit="return confirm('Hapus mitra {{ $partner->name }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 text-xs font-medium text-red-600 hover:text-red-800 px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                    @empty
                    <div class="p-8 text-center text-sm text-slate-500">
                        <svg class="mx-auto h-10 w-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z"/></svg>
                        Belum ada mitra kerja sama.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Tambah Mitra --}}
        <div class="lg:col-span-1">
            <div class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50/50 px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-900">Tambah Mitra Baru</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('cms.partners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Mitra</label>
                            <input type="text" name="name" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="cth: PT Example">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">File Logo</label>
                            <input type="file" name="logo" required accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                            <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG, WebP, SVG. Maks 2MB.</p>
                        </div>
                        <button type="submit" class="w-full inline-flex items-center justify-center rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                            Tambah Mitra
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
