import React from 'react';
import { Head, Link } from '@inertiajs/react';

export default function PriceCatalog({ prices = [] }) {
    return (
        <>
            <Head title="Katalog Harga Sampah - Bank Sampah Digital" />
            
            <div className="bg-background text-on-surface font-sans min-h-screen flex flex-col antialiased">
                {/* Header */}
                <header className="bg-white border-b border-outline-variant/30 sticky top-0 z-50 shadow-sm">
                    <div className="flex justify-between items-center w-full px-lg md:px-xl max-w-container-max mx-auto h-16">
                        <Link href="/" className="text-[20px] font-bold text-primary flex items-center gap-xs">
                            <span className="material-symbols-outlined text-[28px]">arrow_back</span>
                            Katalog Harga
                        </Link>
                        <Link
                            href="/"
                            className="text-on-surface-variant hover:text-primary transition-colors font-semibold text-[14px]"
                        >
                            Kembali ke Beranda
                        </Link>
                    </div>
                </header>

                {/* Main Content */}
                <main className="flex-grow py-xl px-lg max-w-4xl mx-auto w-full">
                    <div className="text-center mb-xl space-y-xs">
                        <h1 className="text-[32px] font-bold text-primary">Katalog Harga Sampah Master</h1>
                        <p className="text-on-surface-variant max-w-lg mx-auto">
                            Berikut adalah daftar harga beli resmi dari Bank Sampah Digital untuk setiap sampah terpilah yang Anda setorkan.
                        </p>
                    </div>

                    <div className="bg-white rounded-3xl border border-outline-variant/30 overflow-hidden shadow-sm">
                        <div className="overflow-x-auto">
                            <table className="w-full text-left border-collapse">
                                <thead>
                                    <tr className="bg-background border-b border-outline-variant/30 text-[13px] font-bold text-primary uppercase tracking-wider">
                                        <th className="px-lg py-md">Nama Sampah</th>
                                        <th className="px-lg py-md">Kategori</th>
                                        <th className="px-lg py-md">Satuan</th>
                                        <th className="px-lg py-md text-right">Harga Beli Nasabah</th>
                                    </tr>
                                </thead>
                                <tbody className="text-[14px] divide-y divide-outline-variant/10">
                                    {prices.length > 0 ? (
                                        prices.map((p) => (
                                            <tr key={p.id} className="hover:bg-background/40 transition-colors">
                                                <td className="px-lg py-md font-semibold text-on-surface">{p.name}</td>
                                                <td className="px-lg py-md capitalize text-on-surface-variant">{p.category.replace('_', ' ')}</td>
                                                <td className="px-lg py-md text-on-surface-variant">{p.unit}</td>
                                                <td className="px-lg py-md text-right font-bold text-primary">
                                                    Rp {new Intl.NumberFormat('id-ID').format(p.price_buy)}
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="4" className="px-lg py-xl text-center text-on-surface-variant font-medium">
                                                Katalog harga sampah belum tersedia.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </main>

                {/* Footer */}
                <footer className="bg-white border-t border-outline-variant/20 text-on-surface-variant py-md text-center text-xs mt-auto">
                    <p>© 2026 Bank Sampah Digital. Mengelola Sampah, Menabung Kebaikan.</p>
                </footer>
            </div>
        </>
    );
}
