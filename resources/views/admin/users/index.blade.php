@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Manajemen Pengguna" subtitle="Pengguna" :actions='[
    auth()->user()->can("create", App\Models\User::class)
        ? "<a href=\"" . route("cms.users.create") . "\" class=\"inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700\">+ Tambah Pengguna</a>"
        : ""
]' />

<div class="mb-4">
    <form method="GET" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/email..."
            class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
        <select name="role" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">Semua Role</option>
            <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
            <option value="petugas" {{ request('role') === 'petugas' ? 'selected' : '' }}>Petugas Bank Sampah</option>
            <option value="nasabah" {{ request('role') === 'nasabah' ? 'selected' : '' }}>Nasabah</option>
        </select>
        <select name="status" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <button type="submit" class="rounded-lg bg-slate-100 px-4 py-2 text-sm text-slate-600 hover:bg-slate-200">Filter</button>
    </form>
</div>

<x-admin.table-wrapper>
    <table class="datatable w-full text-left text-sm">
        <thead>
            <tr>
                <th class="px-4 py-3 font-medium text-slate-600">Nama</th>
                <th class="px-4 py-3 font-medium text-slate-600">Email</th>
                <th class="px-4 py-3 font-medium text-slate-600">Role</th>
                <th class="px-4 py-3 font-medium text-slate-600">Status</th>
                <th class="px-4 py-3 font-medium text-slate-600">Saldo</th>
                <th class="no-sort px-4 py-3 font-medium text-slate-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($users as $u)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-800">{{ $u->name }}
                    @if($u->account_no)
                        <span class="ml-1 text-xs text-slate-400">#{{ $u->account_no }}</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-slate-600">{{ $u->email }}</td>
                <td class="px-4 py-3"><x-admin.badge :value="$u->role" /></td>
                <td class="px-4 py-3"><x-admin.badge :value="$u->status" /></td>
                <td class="px-4 py-3 text-slate-700">Rp {{ number_format($u->saldo, 0, ',', '.') }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('cms.users.show', $u) }}" class="rounded-lg px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-100">Detail</a>
                        @can('update', $u)
                        <a href="{{ route('cms.users.edit', $u) }}" class="rounded-lg px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50">Edit</a>
                        @endcan
                        @can('delete', $u)
                        <form id="del-{{ $u->id }}" method="POST" action="{{ route('cms.users.destroy', $u) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDelete('del-{{ $u->id }}', 'Hapus {{ $u->name }}? Data terkait akan ikut terhapus.')" class="rounded-lg px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50">Hapus</button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada pengguna.</td></tr>
            @endforelse
        </tbody>
    </table>
</x-admin.table-wrapper>
{{ $users->links('components.admin.pagination', ['paginator' => $users]) }}
@endsection