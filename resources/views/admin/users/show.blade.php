@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Detail Pengguna" subtitle="Manajemen Pengguna" :actions='[
    "<a href=\"" . route("cms.users.edit", $user) . "\" class=\"inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700\">Edit</a>"
]' />

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-1">
        <x-admin.card title="Profil">
            <div class="space-y-3 text-sm">
                <div><span class="text-slate-500">Nama</span><p class="font-medium text-slate-800">{{ $user->name }}</p></div>
                <div><span class="text-slate-500">Email</span><p class="text-slate-800">{{ $user->email }}</p></div>
                <div><span class="text-slate-500">Role</span><p><x-admin.badge :value="$user->role" /></p></div>
                <div><span class="text-slate-500">Status</span><p><x-admin.badge :value="$user->status" /></p></div>
                <div><span class="text-slate-500">Telepon</span><p class="text-slate-800">{{ $user->phone ?? '-' }}</p></div>
                <div><span class="text-slate-500">No. Rekening</span><p class="text-slate-800">{{ $user->account_no ?? '-' }}</p></div>
                <div><span class="text-slate-500">Saldo</span><p class="text-lg font-bold text-primary-600">Rp {{ number_format($user->saldo, 0, ',', '.') }}</p></div>
            </div>
        </x-admin.card>
    </div>
    <div class="lg:col-span-2">
        <x-admin.card title="Statistik">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="rounded-lg bg-slate-50 p-4">
                    <p class="text-2xl font-bold text-slate-800">{{ $user->deposits_count }}</p>
                    <p class="text-xs text-slate-500">Setoran</p>
                </div>
                <div class="rounded-lg bg-slate-50 p-4">
                    <p class="text-2xl font-bold text-slate-800">{{ $user->withdrawals_count }}</p>
                    <p class="text-xs text-slate-500">Penarikan</p>
                </div>
                <div class="rounded-lg bg-slate-50 p-4">
                    <p class="text-2xl font-bold text-slate-800">{{ $user->pickup_requests_count }}</p>
                    <p class="text-xs text-slate-500">Jemput</p>
                </div>
            </div>
        </x-admin.card>

        @if ($user->alamat || $user->gender || $user->umur)
        <x-admin.card title="Informasi Tambahan" class="mt-6">
            <div class="grid grid-cols-2 gap-4 text-sm">
                @if ($user->umur)<div><span class="text-slate-500">Umur</span><p class="text-slate-800">{{ $user->umur }} tahun</p></div>@endif
                @if ($user->gender)<div><span class="text-slate-500">Gender</span><p class="text-slate-800">{{ $user->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p></div>@endif
                @if ($user->status_pekerjaan)<div><span class="text-slate-500">Pekerjaan</span><p class="text-slate-800">{{ str_replace('_',' ',$user->status_pekerjaan) }}</p></div>@endif
                @if ($user->universitas)<div><span class="text-slate-500">Universitas</span><p class="text-slate-800">{{ $user->universitas }}</p></div>@endif
                @if ($user->fakultas)<div><span class="text-slate-500">Fakultas</span><p class="text-slate-800">{{ $user->fakultas }}</p></div>@endif
                @if ($user->address)<div class="col-span-2"><span class="text-slate-500">Alamat</span><p class="text-slate-800">{{ $user->address }}</p></div>@endif
            </div>
        </x-admin.card>
        @endif
    </div>
</div>
@endsection