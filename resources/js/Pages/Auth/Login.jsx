import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import InputError from '@/Components/InputError';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <>
            <Head title="Masuk - Bank Sampah Digital" />
            
            <div className="min-h-screen bg-background flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans antialiased">
                <div className="sm:mx-auto sm:w-full sm:max-w-md">
                    <div className="flex justify-center text-primary">
                        <span className="material-symbols-outlined text-[48px]" style={{ fontVariationSettings: "'FILL' 1" }}>recycling</span>
                    </div>
                    <h2 className="mt-4 text-center text-[28px] font-bold tracking-tight text-primary">
                        Masuk ke Akun Anda
                    </h2>
                    <p className="mt-2 text-center text-sm text-on-surface-variant max-w">
                        Silakan masuk untuk melanjutkan pengelolaan tabungan sampah
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
                        {status && (
                            <div className="mb-4 text-sm font-semibold text-green-600">
                                {status}
                            </div>
                        )}

                        <form onSubmit={submit} className="space-y-6">
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
                                        autoComplete="email"
                                        required
                                        onChange={(e) => setData('email', e.target.value)}
                                        className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface"
                                        placeholder="nama@email.com"
                                    />
                                </div>
                                <InputError message={errors.email} className="mt-1" />
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
                                        autoComplete="current-password"
                                        required
                                        onChange={(e) => setData('password', e.target.value)}
                                        className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface"
                                        placeholder="••••••••"
                                    />
                                </div>
                                <InputError message={errors.password} className="mt-1" />
                            </div>

                            <div className="flex items-center justify-between">
                                <div className="flex items-center">
                                    <input
                                        id="remember"
                                        name="remember"
                                        type="checkbox"
                                        checked={data.remember}
                                        onChange={(e) => setData('remember', e.target.checked)}
                                        className="h-4 w-4 text-primary focus:ring-primary border-outline-variant rounded"
                                    />
                                    <label htmlFor="remember" className="ml-2 block text-sm font-medium text-on-surface-variant">
                                        Ingat saya
                                    </label>
                                </div>

                                <div className="text-sm">
                                    {canResetPassword && (
                                        <Link
                                            href={route('password.request')}
                                            className="font-semibold text-primary hover:text-secondary hover:underline"
                                        >
                                            Lupa kata sandi?
                                        </Link>
                                    )}
                                </div>
                            </div>

                            <div>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="w-full flex justify-center py-2 px-4 border border-transparent rounded-full shadow-sm text-sm font-bold text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 transition-colors"
                                >
                                    Masuk
                                </button>
                            </div>
                        </form>

                        <div className="mt-6 text-center text-sm text-on-surface-variant">
                            Belum memiliki akun nasabah?{' '}
                            <Link href="/register" className="font-bold text-primary hover:text-secondary hover:underline">
                                Daftar Sekarang
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
