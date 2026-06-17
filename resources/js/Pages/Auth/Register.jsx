import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import InputError from '@/Components/InputError';

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        phone: '',
        address: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <>
            <Head title="Daftar Akun - Bank Sampah Digital" />
            
            <div className="min-h-screen bg-background flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans antialiased">
                <div className="sm:mx-auto sm:w-full sm:max-w-md">
                    <div className="flex justify-center text-primary">
                        <span className="material-symbols-outlined text-[48px]" style={{ fontVariationSettings: "'FILL' 1" }}>recycling</span>
                    </div>
                    <h2 className="mt-4 text-center text-[28px] font-bold tracking-tight text-primary">
                        Daftar Akun Baru
                    </h2>
                    <p className="mt-2 text-center text-sm text-on-surface-variant max-w">
                        Bergabunglah menjadi nasabah dan mulailah menabung kebaikan
                    </p>
                </div>

                <div className="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                    <div className="mb-4 px-4 sm:px-0 text-left">
                        <Link
                            href="/"
                            className="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:text-secondary transition-colors"
                        >
                            <span className="material-symbols-outlined text-[18px]">arrow_back</span>
                            Kembali ke Beranda
                        </Link>
                    </div>
                    <div className="bg-white py-8 px-4 shadow-sm border border-outline-variant/30 sm:rounded-2xl sm:px-10">
                        <form onSubmit={submit} className="space-y-5">
                            <div>
                                <label htmlFor="name" className="block text-sm font-bold text-on-surface">
                                    Nama Lengkap
                                </label>
                                <div className="mt-1">
                                    <input
                                        id="name"
                                        type="text"
                                        name="name"
                                        value={data.name}
                                        required
                                        onChange={(e) => setData('name', e.target.value)}
                                        className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface"
                                        placeholder="Nama lengkap Anda"
                                    />
                                </div>
                                <InputError message={errors.name} className="mt-1" />
                            </div>

                            <div>
                                <label htmlFor="email" className="block text-sm font-bold text-on-surface">
                                    Alamat Email
                                </label>
                                <div className="mt-1">
                                    <input
                                        id="email"
                                        type="email"
                                        name="email"
                                        value={data.email}
                                        required
                                        onChange={(e) => setData('email', e.target.value)}
                                        className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface"
                                        placeholder="nama@email.com"
                                    />
                                </div>
                                <InputError message={errors.email} className="mt-1" />
                            </div>

                            <div>
                                <label htmlFor="phone" className="block text-sm font-bold text-on-surface">
                                    Nomor Telepon / WhatsApp
                                </label>
                                <div className="mt-1">
                                    <input
                                        id="phone"
                                        type="tel"
                                        name="phone"
                                        value={data.phone}
                                        onChange={(e) => setData('phone', e.target.value)}
                                        className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface"
                                        placeholder="0812xxxxxxxx"
                                    />
                                </div>
                                <InputError message={errors.phone} className="mt-1" />
                            </div>

                            <div>
                                <label htmlFor="address" className="block text-sm font-bold text-on-surface">
                                    Alamat Rumah
                                </label>
                                <div className="mt-1">
                                    <textarea
                                        id="address"
                                        name="address"
                                        value={data.address}
                                        onChange={(e) => setData('address', e.target.value)}
                                        className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface"
                                        placeholder="Alamat lengkap tempat tinggal Anda"
                                        rows="2"
                                    />
                                </div>
                                <InputError message={errors.address} className="mt-1" />
                            </div>

                            <div>
                                <label htmlFor="password" className="block text-sm font-bold text-on-surface">
                                    Kata Sandi
                                </label>
                                <div className="mt-1">
                                    <input
                                        id="password"
                                        type="password"
                                        name="password"
                                        value={data.password}
                                        required
                                        onChange={(e) => setData('password', e.target.value)}
                                        className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface"
                                        placeholder="Minimal 8 karakter"
                                    />
                                </div>
                                <InputError message={errors.password} className="mt-1" />
                            </div>

                            <div>
                                <label htmlFor="password_confirmation" className="block text-sm font-bold text-on-surface">
                                    Konfirmasi Kata Sandi
                                </label>
                                <div className="mt-1">
                                    <input
                                        id="password_confirmation"
                                        type="password"
                                        name="password_confirmation"
                                        value={data.password_confirmation}
                                        required
                                        onChange={(e) => setData('password_confirmation', e.target.value)}
                                        className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface"
                                        placeholder="Ketik ulang kata sandi"
                                    />
                                </div>
                                <InputError message={errors.password_confirmation} className="mt-1" />
                            </div>

                            <div className="pt-sm">
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="w-full flex justify-center py-2 px-4 border border-transparent rounded-full shadow-sm text-sm font-bold text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 transition-colors"
                                >
                                    Daftar Akun
                                </button>
                            </div>
                        </form>

                        <div className="mt-6 text-center text-sm text-on-surface-variant">
                            Sudah memiliki akun?{' '}
                            <Link href="/login" className="font-bold text-primary hover:text-secondary hover:underline">
                                Masuk di sini
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
