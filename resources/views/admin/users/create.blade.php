@extends('layouts.admin')

@section('content')
<x-admin.page-header title="{{ isset($user) && $user->exists ? 'Edit Pengguna' : 'Tambah Pengguna' }}" subtitle="Manajemen Pengguna" />

<x-admin.card>
    <form method="POST" action="{{ isset($user) && $user->exists ? route('cms.users.update', $user) : route('cms.users.store') }}">
        @csrf
        @isset($user) @method('PUT') @endisset

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-admin.form.input name="name" label="Nama Lengkap" :value="$user->name ?? ''" required />
            <x-admin.form.input name="email" type="email" label="Email" :value="$user->email ?? ''" required />
            <x-admin.form.input name="password" type="password" label="{{ $user->exists ? 'Password Baru (kosongkan jika tidak diubah)' : 'Password' }}" :required="!$user->exists" />
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-700">Role <span class="text-red-500">*</span></label>
                @if(isset($user) && $user->role === 'super_admin')
                    <input type="hidden" name="role" value="super_admin">
                    <input type="text" disabled value="Super Admin" class="block w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-500">
                @else
                    <select name="role" required class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        <option value="petugas" {{ ($user->role ?? '') === 'petugas' ? 'selected' : '' }}>Petugas Bank Sampah</option>
                        <option value="nasabah" {{ ($user->role ?? '') === 'nasabah' ? 'selected' : '' }}>Nasabah</option>
                    </select>
                @endif
            </div>
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-700">Status <span class="text-red-500">*</span></label>
                <select name="status" required class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    <option value="verified" {{ ($user->status ?? 'verified') === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                    <option value="pending" {{ ($user->status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ ($user->status ?? '') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <x-admin.form.input name="phone" label="No. Telepon" :value="$user->phone ?? ''" />
            <x-admin.form.input name="umur" type="number" label="Umur" :value="$user->umur ?? ''" />
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-700">Jenis Kelamin</label>
                <select name="gender" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    <option value="">—</option>
                    <option value="L" {{ ($user->gender ?? '') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ ($user->gender ?? '') === 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-700">Status Pekerjaan</label>
                <select name="status_pekerjaan" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    <option value="">—</option>
                    @foreach (['bekerja','tidak_bekerja','pelajar','mahasiswa','pensiun','lainnya'] as $opt)
                        <option value="{{ $opt }}" {{ ($user->status_pekerjaan ?? '') === $opt ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $opt)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-700">Pendidikan Terakhir</label>
                <select name="pendidikan_terakhir" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    <option value="">—</option>
                    @foreach (['sd'=>'SD','smp'=>'SMP','sma'=>'SMA/SMK','s1'=>'S1','s2'=>'S2','s3'=>'S3'] as $k => $v)
                        <option value="{{ $k }}" {{ ($user->pendidikan_terakhir ?? '') === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <x-admin.form.input name="universitas" label="Universitas/Instansi" :value="$user->universitas ?? ''" />
            <x-admin.form.input name="fakultas" label="Fakultas/Jurusan" :value="$user->fakultas ?? ''" />
        </div>

        <div class="mt-4">
            <x-admin.form.input name="address" label="Alamat" type="textarea" :value="$user->address ?? ''" />
        </div>

        <div class="mt-6 flex items-center gap-3">
            <x-admin.btn type="submit">{{ $user->exists ? 'Simpan Perubahan' : 'Simpan' }}</x-admin.btn>
            <x-admin.btn-secondary href="{{ route('cms.users.index') }}">Batal</x-admin.btn-secondary>
        </div>
    </form>
</x-admin.card>
@endsection