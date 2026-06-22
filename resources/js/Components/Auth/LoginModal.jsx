import React, { useState } from 'react';
import { useForm, Link } from '@inertiajs/react';
import { Dialog, DialogPanel, DialogTitle } from '@headlessui/react';
import InputError from '@/Components/InputError';

export default function LoginModal({ isOpen, onClose, onSwitchToRegister }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const [showPassword, setShowPassword] = useState(false);

    const submit = (e) => {
        e.preventDefault();
        post(route('login'), {
            onFinish: () => reset('password'),
            onSuccess: () => onClose(),
        });
    };

    const handleClose = () => {
        if (!processing) {
            onClose();
        }
    };

    const canResetPassword = typeof route !== 'undefined' && route().has('password.request');

    return (
        <Dialog 
            open={isOpen} 
            onClose={handleClose} 
            transition 
            className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 transition duration-300 ease-out data-[closed]:opacity-0"
        >
            <DialogPanel 
                transition 
                className="relative w-full max-w-md bg-white rounded-3xl p-xl shadow-xl border border-outline-variant/30 transition duration-300 ease-out data-[closed]:scale-95 data-[closed]:opacity-0"
            >
                {/* Close Button */}
                <button 
                    type="button"
                    onClick={handleClose}
                    className="absolute right-md top-md p-1 rounded-full text-on-surface-variant hover:bg-gray-100 hover:text-primary transition-all z-20"
                >
                    <span className="material-symbols-outlined text-[20px]">close</span>
                </button>

                <div className="text-center mb-lg">
                    <div className="flex justify-center text-primary mb-sm">
                        <span className="material-symbols-outlined text-[40px]" style={{ fontVariationSettings: "'FILL' 1" }}>recycling</span>
                    </div>
                    <DialogTitle as="h2" className="text-[24px] font-bold tracking-tight text-primary">
                        Masuk ke Akun Anda
                    </DialogTitle>
                    <p className="text-xs text-on-surface-variant mt-1">
                        Silakan masuk untuk melanjutkan pengelolaan tabungan sampah
                    </p>
                </div>

                <form onSubmit={submit} className="space-y-md">
                    <div>
                        <label htmlFor="modal_email" className="block text-xs font-bold text-on-surface mb-1">
                            Alamat Email
                        </label>
                        <input
                            id="modal_email"
                            type="email"
                            name="email"
                            value={data.email}
                            required
                            onChange={(e) => setData('email', e.target.value)}
                            className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary text-sm text-on-surface bg-white"
                            placeholder="nama@email.com"
                        />
                        <InputError message={errors.email} className="mt-1 text-xs" />
                    </div>

                    <div>
                        <label htmlFor="modal_password" className="block text-xs font-bold text-on-surface mb-1">
                            Kata Sandi
                        </label>
                        <div className="relative flex items-center">
                            <input
                                id="modal_password"
                                type={showPassword ? 'text' : 'password'}
                                name="password"
                                value={data.password}
                                required
                                onChange={(e) => setData('password', e.target.value)}
                                className="appearance-none block w-full pl-3 pr-10 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary text-sm text-on-surface bg-white"
                                placeholder="••••••••"
                            />
                            <button
                                type="button"
                                onClick={() => setShowPassword(!showPassword)}
                                className="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 bg-transparent border-0"
                            >
                                <span className="material-symbols-outlined select-none text-[18px] text-on-surface-variant/70 hover:text-primary transition-colors cursor-pointer">
                                    {showPassword ? 'visibility_off' : 'visibility'}
                                </span>
                            </button>
                        </div>
                        <InputError message={errors.password} className="mt-1 text-xs" />
                    </div>

                    <div className="flex items-center justify-between text-xs">
                        <div className="flex items-center">
                            <input
                                id="modal_remember"
                                name="remember"
                                type="checkbox"
                                checked={data.remember}
                                onChange={(e) => setData('remember', e.target.checked)}
                                className="h-4 w-4 text-primary focus:ring-primary border-outline-variant rounded"
                            />
                            <label htmlFor="modal_remember" className="ml-2 font-semibold text-on-surface-variant">
                                Ingat saya
                                </label>
                        </div>

                        {canResetPassword && (
                            <Link
                                href={route('password.request')}
                                className="font-semibold text-primary hover:text-secondary hover:underline no-underline"
                            >
                                Lupa kata sandi?
                            </Link>
                        )}
                    </div>

                    <div className="pt-sm">
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full flex justify-center py-2 px-4 border border-transparent rounded-full shadow-sm text-sm font-bold text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 transition-colors cursor-pointer"
                        >
                            {processing ? 'Memproses...' : 'Masuk'}
                        </button>
                    </div>
                </form>

                <div className="mt-6 text-center text-xs text-on-surface-variant">
                    Belum memiliki akun nasabah?{' '}
                    <button 
                        type="button"
                        onClick={onSwitchToRegister}
                        className="font-bold text-primary hover:text-secondary hover:underline bg-transparent border-0 cursor-pointer"
                    >
                        Daftar Sekarang
                    </button>
                </div>
            </DialogPanel>
        </Dialog>
    );
}
