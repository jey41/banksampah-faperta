@php use App\Models\TrashPrice; @endphp
@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Harga Sampah" subtitle="Master Data" :actions='[
    auth()->user()->can("create", TrashPrice::class)
        ? "<a href=\"" . route("cms.trash-prices.create") . "\" class=\"inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700\">+ Tambah Harga</a>"
        : ""
][0]' />

<div class="mb-4">
    <form method="GET" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori..."
            class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
        <select name="category_type" onchange="this.form.submit()"
            class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">Semua Tipe</option>
            <option value="umum" {{ request('category_type') === 'umum' ? 'selected' : '' }}>Umum</option>
            <option value="donasi" {{ request('category_type') === 'donasi' ? 'selected' : '' }}>Donasi</option>
        </select>
        <button type="submit" class="rounded-lg bg-slate-100 px-4 py-2 text-sm text-slate-600 hover:bg-slate-200">Filter</button>
    </form>
</div>

<x-admin.table-wrapper>
    <table class="datatable w-full text-left text-sm">
        <thead>
            <tr>
                <th class="px-4 py-3 font-medium text-slate-600">Nama</th>
                <th class="px-4 py-3 font-medium text-slate-600">Kategori</th>
                <th class="px-4 py-3 font-medium text-slate-600">Tipe</th>
                <th class="px-4 py-3 font-medium text-slate-600">Harga Beli</th>
                <th class="px-4 py-3 font-medium text-slate-600">Satuan</th>
                <th class="px-4 py-3 font-medium text-slate-600">Status</th>
                <th class="no-sort px-4 py-3 font-medium text-slate-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($prices as $price)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-medium text-slate-800">{{ $price->name }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $price->category }}</td>
                <td class="px-4 py-3"><x-admin.badge :value="$price->category_type" /></td>
                <td class="px-4 py-3 text-slate-700">Rp {{ number_format($price->price_buy, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $price->unit }}</td>
                <td class="px-4 py-3">
                    <span class="{{ $price->price_buy > 0 ? 'text-green-600' : 'text-red-500' }} text-xs font-medium">{{ $price->price_buy > 0 ? 'Aktif' : 'Nonaktif' }}</span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('cms.trash-prices.edit', $price) }}"
                            class="rounded-lg px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50">Edit</a>
                        @can('delete', $price)
                        <form id="delete-{{ $price->id }}" method="POST" action="{{ route('cms.trash-prices.destroy', $price) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDelete('delete-{{ $price->id }}', 'Hapus harga {{ $price->name }}?')"
                                class="rounded-lg px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50">Hapus</button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">Belum ada data harga sampah.</td></tr>
            @endforelse
        </tbody>
    </table>
</x-admin.table-wrapper>
<div class="mt-4">
    {{ $prices->links('components.admin.pagination', ['paginator' => $prices]) }}
</div>
@endsection