@extends('layouts.admin')

@section('title', 'Alur Kerja')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Alur Kerja (Workflow)</h1>
        <p class="text-sm text-slate-500">Kelola teks tiga langkah mudah di landing page.</p>
    </div>

    @if(session('success'))
    <div class="rounded-xl bg-green-50 p-4 text-sm text-green-800 border border-green-200">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('cms.site-settings.workflow.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl overflow-hidden">
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Judul Section</label>
                    <input type="text" name="workflow_title" value="{{ $settings['workflow_title'] ?? '' }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi Section</label>
                    <textarea name="workflow_description" rows="2" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">{{ $settings['workflow_description'] ?? '' }}</textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-100">
                    {{-- Step 1 --}}
                    <div class="bg-slate-50 rounded-xl p-4 space-y-3">
                        <h3 class="font-bold text-sm text-primary-600 flex items-center gap-2">
                            <span class="w-6 h-6 bg-primary-600 text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                            Langkah 1
                        </h3>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Judul</label>
                            <input type="text" name="workflow_step1_title" value="{{ $settings['workflow_step1_title'] ?? '' }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Deskripsi</label>
                            <textarea name="workflow_step1_desc" rows="3" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">{{ $settings['workflow_step1_desc'] ?? '' }}</textarea>
                        </div>
                    </div>
                    {{-- Step 2 --}}
                    <div class="bg-slate-50 rounded-xl p-4 space-y-3">
                        <h3 class="font-bold text-sm text-primary-600 flex items-center gap-2">
                            <span class="w-6 h-6 bg-primary-600 text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                            Langkah 2
                        </h3>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Judul</label>
                            <input type="text" name="workflow_step2_title" value="{{ $settings['workflow_step2_title'] ?? '' }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Deskripsi</label>
                            <textarea name="workflow_step2_desc" rows="3" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">{{ $settings['workflow_step2_desc'] ?? '' }}</textarea>
                        </div>
                    </div>
                    {{-- Step 3 --}}
                    <div class="bg-slate-50 rounded-xl p-4 space-y-3">
                        <h3 class="font-bold text-sm text-primary-600 flex items-center gap-2">
                            <span class="w-6 h-6 bg-primary-600 text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                            Langkah 3
                        </h3>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Judul</label>
                            <input type="text" name="workflow_step3_title" value="{{ $settings['workflow_step3_title'] ?? '' }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Deskripsi</label>
                            <textarea name="workflow_step3_desc" rows="3" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">{{ $settings['workflow_step3_desc'] ?? '' }}</textarea>
                        </div>
                    </div>
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
