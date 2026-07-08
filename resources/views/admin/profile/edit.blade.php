@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Profil Saya" subtitle="Sistem" />

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <x-admin.card title="Informasi Profil">
        <form method="POST" action="{{ route('cms.profile.update') }}">
            @csrf @method('PATCH')

            <div class="space-y-4">
                <x-admin.form.input name="name" label="Nama Lengkap" :value="$user->name" required />
                <x-admin.form.input name="email" type="email" label="Email" :value="$user->email" required />
                <x-admin.form.input name="phone" label="No. Telepon" :value="$user->phone ?? ''" />
            </div>

            <div class="mt-6">
                <x-admin.btn type="submit">Simpan Profil</x-admin.btn>
            </div>
        </form>
    </x-admin.card>

    <x-admin.card title="Ubah Kata Sandi">
        <form method="POST" action="{{ route('cms.profile.password') }}">
            @csrf @method('PUT')

            <div class="space-y-4">
                <x-admin.form.input name="current_password" type="password" label="Kata Sandi Saat Ini" required />
                <x-admin.form.input name="password" type="password" label="Kata Sandi Baru" required />
                <x-admin.form.input name="password_confirmation" type="password" label="Konfirmasi Kata Sandi Baru" required />
            </div>

            <div class="mt-6">
                <x-admin.btn type="submit" color="warning">Perbarui Kata Sandi</x-admin.btn>
            </div>
        </form>
    </x-admin.card>
</div>
@endsection