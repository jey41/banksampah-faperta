import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import NasabahLayout from '@/Layouts/NasabahLayout';

export default function History({ transactions = [] }) {
    const [filter, setFilter] = useState('all'); // all, deposit, withdrawal

    const filteredTransactions = transactions.filter(tx => {
        if (filter === 'all') return true;
        return tx.type === filter;
    });

    return (
        <NasabahLayout>
            <Head title="Riwayat Transaksi - Bank Sampah Faperta" />

            <div className="flex items-center gap-xs mt-sm text-primary">
                <Link href="/nasabah/dashboard" className="flex items-center hover:underline font-bold text-[14px]">
                    <span className="material-symbols-outlined text-[20px]">arrow_back</span>
                    Kembali ke Dasbor
                </Link>
            </div>

            <div className="space-y-xs">
                <h1 className="text-[24px] font-bold text-primary tracking-tight">Riwayat Transaksi</h1>
                <p className="text-sm text-on-surface-variant leading-relaxed">
                    Daftar lengkap seluruh pengajuan setoran sampah dan penarikan saldo Anda.
                </p>
            </div>

            {/* Filter Tabs */}
            <div className="flex bg-white rounded-2xl border border-outline-variant/30 p-1 shadow-sm max-w-md">
                <button
                    onClick={() => setFilter('all')}
                    className={`flex-1 py-2 text-center rounded-xl text-[13px] font-bold transition-all ${
                        filter === 'all' 
                        ? 'bg-primary text-white shadow-sm' 
                        : 'text-on-surface-variant hover:text-primary'
                    }`}
                >
                    Semua
                </button>
                <button
                    onClick={() => setFilter('deposit')}
                    className={`flex-1 py-2 text-center rounded-xl text-[13px] font-bold transition-all ${
                        filter === 'deposit' 
                        ? 'bg-primary text-white shadow-sm' 
                        : 'text-on-surface-variant hover:text-primary'
                    }`}
                >
                    Setoran
                </button>
                <button
                    onClick={() => setFilter('withdrawal')}
                    className={`flex-1 py-2 text-center rounded-xl text-[13px] font-bold transition-all ${
                        filter === 'withdrawal' 
                        ? 'bg-primary text-white shadow-sm' 
                        : 'text-on-surface-variant hover:text-primary'
                    }`}
                >
                    Penarikan
                </button>
            </div>

            {/* Desktop Table View (Hidden on Mobile) */}
            <div className="hidden md:block bg-white rounded-3xl border border-outline-variant/30 overflow-hidden shadow-sm">
                <table className="w-full text-left border-collapse">
                    <thead>
                        <tr className="bg-[#F9FAF9] border-b border-outline-variant/25 text-[11px] font-bold uppercase tracking-wider text-on-surface-variant">
                            <th className="py-md px-lg">Tanggal</th>
                            <th className="py-md px-lg">Jenis Transaksi</th>
                            <th className="py-md px-lg">Keterangan / Detail</th>
                            <th className="py-md px-lg text-center">Timbangan (kg/L)</th>
                            <th className="py-md px-lg text-right">Nominal</th>
                            <th className="py-md px-lg text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-outline-variant/10 text-[13px]">
                        {filteredTransactions.length > 0 ? (
                            filteredTransactions.map((tx, idx) => {
                                const isDeposit = tx.type === 'deposit';
                                return (
                                    <tr key={`desktop-${tx.type}-${tx.id}-${idx}`} className="hover:bg-surface-container-low/20 transition-colors">
                                        <td className="py-md px-lg text-on-surface-variant font-medium">
                                            {new Date(tx.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                                        </td>
                                        <td className="py-md px-lg">
                                            <div className="flex items-center gap-sm">
                                                <div className={`w-8 h-8 rounded-full flex items-center justify-center shrink-0 ${
                                                    isDeposit ? 'bg-primary/10 text-primary' : 'bg-red-50 text-red-600'
                                                }`}>
                                                    <span className="material-symbols-outlined text-[18px]">{isDeposit ? 'recycling' : 'payments'}</span>
                                                </div>
                                                <span className="font-bold">{isDeposit ? 'Setoran Sampah' : 'Penarikan Saldo'}</span>
                                            </div>
                                        </td>
                                        <td className="py-md px-lg max-w-[250px] truncate">
                                            <p className="font-semibold text-on-surface">{tx.title}</p>
                                            {tx.notes && <p className="text-[11px] text-on-surface-variant italic mt-0.5">"{tx.notes}"</p>}
                                        </td>
                                        <td className="py-md px-lg text-center font-semibold text-on-surface-variant">
                                            {isDeposit && tx.weight ? `${tx.weight} kg/L` : '-'}
                                        </td>
                                        <td className="py-md px-lg text-right font-bold">
                                            <span className={isDeposit ? 'text-primary' : 'text-on-surface'}>
                                                {isDeposit ? '+' : '-'}Rp {new Intl.NumberFormat('id-ID').format(tx.amount)}
                                            </span>
                                        </td>
                                        <td className="py-md px-lg text-center">
                                            <span className={`inline-block px-3 py-1 rounded-full text-[10px] font-bold border ${
                                                tx.status === 'approved' 
                                                ? 'bg-green-50 text-green-600 border-green-200' 
                                                : tx.status === 'pending' 
                                                ? 'bg-yellow-50 text-yellow-600 border-yellow-200' 
                                                : 'bg-red-50 text-red-600 border-red-200'
                                            }`}>
                                                {tx.status === 'approved' ? 'Berhasil' : tx.status === 'pending' ? 'Diproses' : 'Ditolak'}
                                            </span>
                                        </td>
                                    </tr>
                                );
                            })
                        ) : (
                            <tr>
                                <td colSpan="6" className="text-center py-xl text-on-surface-variant text-[14px]">
                                    Belum ada riwayat transaksi kategori ini.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            {/* Mobile Cards View (Hidden on Desktop) */}
            <div className="md:hidden bg-white rounded-2xl border border-outline-variant/30 overflow-hidden shadow-sm">
                <div className="flex flex-col divide-y divide-outline-variant/10">
                    {filteredTransactions.length > 0 ? (
                        filteredTransactions.map((tx, idx) => {
                            const isDeposit = tx.type === 'deposit';
                            return (
                                <div key={`mobile-${tx.type}-${tx.id}-${idx}`} className="p-md hover:bg-surface-container-low/20 transition-colors space-y-sm">
                                    <div className="flex items-center gap-md">
                                        <div className={`w-10 h-10 rounded-full flex items-center justify-center shrink-0 ${
                                            isDeposit ? 'bg-[#E8F5E9] text-primary' : 'bg-red-50 text-red-600'
                                        }`}>
                                            <span className="material-symbols-outlined text-[20px]">{isDeposit ? 'recycling' : 'payments'}</span>
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <div className="flex justify-between items-start">
                                                <p className="text-[14px] font-bold text-on-surface truncate">{tx.title}</p>
                                                <p className={`text-[14px] font-bold ${isDeposit ? 'text-primary' : 'text-on-surface'}`}>
                                                    {isDeposit ? '+' : '-'}Rp {new Intl.NumberFormat('id-ID').format(tx.amount)}
                                                </p>
                                            </div>
                                            <div className="flex justify-between items-center mt-xs">
                                                <p className="text-[11px] text-on-surface-variant truncate">
                                                    {new Date(tx.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                                                </p>
                                                <span className={`inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border ${
                                                    tx.status === 'approved' 
                                                    ? 'bg-green-50 text-green-600 border-green-200' 
                                                    : tx.status === 'pending' 
                                                    ? 'bg-yellow-50 text-yellow-600 border-yellow-200' 
                                                    : 'bg-red-50 text-red-600 border-red-200'
                                                }`}>
                                                    {tx.status === 'approved' ? 'Berhasil' : tx.status === 'pending' ? 'Diproses' : 'Ditolak'}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {(tx.weight || tx.notes) && (
                                        <div className="bg-background/40 border border-outline-variant/10 rounded-xl p-sm text-[11px] text-on-surface-variant space-y-xs">
                                            {isDeposit && tx.weight && (
                                                <p><span className="font-bold">Total Timbangan:</span> {tx.weight} kg/L</p>
                                            )}
                                            {tx.notes && (
                                                <p><span className="font-bold">Catatan:</span> "{tx.notes}"</p>
                                            )}
                                        </div>
                                    )}
                                </div>
                            );
                        })
                    ) : (
                        <div className="text-center py-xl text-on-surface-variant text-[14px]">
                            Belum ada riwayat transaksi kategori ini.
                        </div>
                    )}
                </div>
            </div>
        </NasabahLayout>
    );
}

