@extends('layouts.admin')

@section('content')
<x-admin.page-header title="{{ isset($price) && $price->exists ? 'Edit Harga Sampah' : 'Tambah Harga Sampah' }}" subtitle="Master Data" />

<x-admin.card>
    <form method="POST" action="{{ isset($price) && $price->exists ? route('cms.trash-prices.update', $price) : route('cms.trash-prices.store') }}">
        @csrf
        @isset($price) @method('PUT') @endisset

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-admin.form.input name="name" label="Nama Kategori" :value="$price->name ?? ''" required />
            <x-admin.form.input name="category" label="Kategori (grup)" :value="$price->category ?? ''" required placeholder="Contoh: plastik, kertas, logam" />
            <div class="space-y-1">
                <label for="category_type" class="block text-sm font-medium text-slate-700">Tipe <span class="text-red-500">*</span></label>
                <select name="category_type" id="category_type" required
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    <option value="umum" {{ ($price->category_type ?? 'umum') === 'umum' ? 'selected' : '' }}>Umum</option>
                    <option value="donasi" {{ ($price->category_type ?? '') === 'donasi' ? 'selected' : '' }}>Donasi</option>
                </select>
            </div>
            <x-admin.form.input name="price_buy" type="number" label="Harga Beli (Rp)" :value="$price->price_buy ?? 0" required />
            <x-admin.form.input name="price_sell" type="number" label="Harga Jual (Rp)" :value="$price->price_sell ?? 0" />
            <x-admin.form.input name="unit" label="Satuan" :value="$price->unit ?? 'kg'" required />
            <x-admin.form.input name="carbon_factor" type="number" step="0.01" label="Faktor Karbon (CO₂e/kg)" :value="$price->carbon_factor ?? 0" />
        </div>

        <div class="mt-6 flex items-center gap-3">
            <x-admin.btn type="submit">{{ isset($price) && $price->exists ? 'Simpan Perubahan' : 'Simpan' }}</x-admin.btn>
            <x-admin.btn-secondary href="{{ route('cms.trash-prices.index') }}">Batal</x-admin.btn-secondary>
        </div>
    </form>
</x-admin.card>
@endsection