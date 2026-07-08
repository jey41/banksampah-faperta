@extends('layouts.admin')

@section('title', 'Jadwal Operasional')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Jadwal Operasional</h1>
        <p class="text-sm text-slate-500">Kelola informasi jam kerja dan gambar fasilitas depo di landing page.</p>
    </div>

    @if(session('success'))
    <div class="rounded-xl bg-green-50 p-4 text-sm text-green-800 border border-green-200">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('cms.site-settings.schedule.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl overflow-hidden">
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                    <textarea name="schedule_description" rows="3" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">{{ $settings['schedule_description'] ?? '' }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Hari Layanan</label>
                        <input type="text" name="schedule_days" value="{{ $settings['schedule_days'] ?? '' }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="cth: Senin - Sabtu">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Jam Layanan</label>
                        <input type="text" name="schedule_hours" value="{{ $settings['schedule_hours'] ?? '' }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="cth: 08:00 - 16:00 WITA">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
                    <input type="text" name="schedule_note" value="{{ $settings['schedule_note'] ?? '' }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="cth: Tutup pada hari Minggu dan Hari Libur Nasional.">
                </div>
                <div class="pt-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Gambar Fasilitas Depo</label>
                    @if(isset($settings['schedule_image']))
                    <div class="mb-3">
                        <img src="{{ $settings['schedule_image'] }}" alt="Schedule Image" class="h-40 w-auto object-cover rounded-lg border border-slate-200">
                    </div>
                    @endif
                    <input type="file" name="schedule_image_file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    <p class="text-xs text-slate-500 mt-1">Biarkan kosong jika tidak ingin mengubah gambar.</p>
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
