import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';

export default function PriceCatalog({ prices = [] }) {
    const [searchQuery, setSearchQuery] = useState('');
    const [selectedCategory, setSelectedCategory] = useState('all');

    const categories = [
        { id: 'all', label: 'Semua Kategori' },
        { id: 'plastik', label: 'Plastik' },
        { id: 'kertas', label: 'Kertas' },
        { id: 'logam', label: 'Logam' },
        { id: 'kaca', label: 'Kaca' },
        { id: 'minyak_jelantah', label: 'Minyak Jelantah' }
    ];

    const filteredPrices = prices.filter((p) => {
        const matchesSearch = p.name.toLowerCase().includes(searchQuery.toLowerCase());
        const matchesCategory = selectedCategory === 'all' || p.category === selectedCategory;
        return matchesSearch && matchesCategory;
    });

    return (
        <>
            <Head title="Katalog Harga Sampah - Bank Sampah Faperta Unmul" />
            
            <div className="py-2xl bg-background min-h-screen">
                <main className="max-w-container-max mx-auto px-lg md:px-xl w-full">
                    {/* Header Title */}
                    <div className="text-center mb-2xl space-y-sm">
                        <h1 className="text-[32px] md:text-[40px] font-bold text-primary">Katalog Harga Sampah</h1>
                        <p className="text-on-surface-variant max-w-2xl mx-auto text-[14px] md:text-[16px] leading-relaxed">
                            Berikut adalah daftar lengkap harga beli resmi per unit untuk setiap sampah terpilah yang kami terima dari Nasabah.
                        </p>
                    </div>

                    {/* Search Bar */}
                    <div className="relative max-w-md mx-auto mb-lg">
                        <span className="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">
                            search
                        </span>
                        <input
                            type="text"
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            placeholder="Cari nama sampah..."
                            className="w-full pl-xl pr-md py-sm bg-white border border-outline-variant/30 rounded-full focus:ring-2 focus:ring-primary/20 focus:border-primary text-[14px] shadow-sm transition-all outline-none"
                        />
                    </div>

                    {/* Category Tabs */}
                    <div className="flex flex-wrap gap-xs md:gap-sm justify-center mb-xl">
                        {categories.map((cat) => (
                            <button
                                key={cat.id}
                                onClick={() => setSelectedCategory(cat.id)}
                                className={`px-lg py-sm rounded-full text-[13px] font-bold transition-all border-0 cursor-pointer shadow-sm ${
                                    selectedCategory === cat.id
                                        ? 'bg-primary text-white'
                                        : 'bg-white text-on-surface-variant hover:bg-surface-container-low'
                                }`}
                            >
                                {cat.label}
                            </button>
                        ))}
                    </div>

                    {/* Table Card */}
                    <div className="bg-white rounded-3xl border border-outline-variant/30 overflow-hidden shadow-sm max-w-4xl mx-auto p-md">
                        <div className="overflow-x-auto">
                            <table className="w-full text-left border-collapse">
                                <thead>
                                    <tr className="bg-background border-b border-outline-variant/20 text-[13px] font-bold text-primary uppercase tracking-wider">
                                        <th className="px-lg py-md">Nama Sampah</th>
                                        <th className="px-lg py-md">Kategori</th>
                                        <th className="px-lg py-md">Satuan</th>
                                        <th className="px-lg py-md text-right">Harga Beli Nasabah</th>
                                    </tr>
                                </thead>
                                <tbody className="text-[14px] divide-y divide-outline-variant/10">
                                    {filteredPrices.length > 0 ? (
                                        filteredPrices.map((p) => (
                                            <tr key={p.id} className="hover:bg-background/40 transition-colors">
                                                <td className="px-lg py-md font-semibold text-on-surface">{p.name}</td>
                                                <td className="px-lg py-md capitalize text-on-surface-variant">
                                                    {p.category.replace('_', ' ')}
                                                </td>
                                                <td className="px-lg py-md text-on-surface-variant font-medium">{p.unit}</td>
                                                <td className="px-lg py-md text-right font-bold text-primary">
                                                    Rp {new Intl.NumberFormat('id-ID').format(p.price_buy)}
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="4" className="px-lg py-xl text-center text-on-surface-variant font-medium">
                                                Tidak ada sampah yang cocok dengan kriteria pencarian/kategori Anda.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </main>
            </div>
        </>
    );
}

PriceCatalog.layout = (page) => <PublicLayout children={page} />;
