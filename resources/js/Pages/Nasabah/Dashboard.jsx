import React, { useState } from 'react';
import { Head, Link, usePage, useForm, router } from '@inertiajs/react';
import NasabahLayout from '@/Layouts/NasabahLayout';

export default function Dashboard({ transactions = [], totalDeposited = 0, totalWithdrawn = 0, targets = [] }) {
    const { auth } = usePage().props;

    const { data, setData, post, reset, processing, errors } = useForm({
        title: '',
        target_amount: '',
    });
    const [showAddForm, setShowAddForm] = useState(false);

    const handleAddTarget = (e) => {
        e.preventDefault();
        post(route('nasabah.target.store'), {
            onSuccess: () => {
                reset();
                setShowAddForm(false);
            },
        });
    };

    const handleDeleteTarget = (targetId) => {
        if (confirm('Apakah Anda yakin ingin menghapus target tabungan ini?')) {
            router.delete(route('nasabah.target.delete', targetId));
        }
    };

    return (
        <NasabahLayout>
            <Head title="Beranda Nasabah - Bank Sampah Faperta" />

            {/* Welcome banner at the top */}
            <div className="bg-gradient-to-r from-primary to-primary-container text-white rounded-3xl p-md md:p-lg border border-primary/10 shadow-md flex justify-between items-center relative overflow-hidden">
                <div className="relative z-10 space-y-1">
                    <p className="text-[11px] font-bold text-secondary-container tracking-wider uppercase">Selamat Datang Kembali</p>
                    <h1 className="text-[22px] md:text-[28px] font-bold tracking-tight">{auth.user.name}</h1>
                    <p className="text-[13px] text-white/95 flex items-center gap-xs">
                        No. Rekening: 
                        <span className="font-mono bg-white/10 px-2 py-0.5 rounded text-white font-bold text-[12px] tracking-wide">
                            {auth.user.account_no || 'Menunggu Verifikasi'}
                        </span>
                    </p>
                </div>
                <div className="relative z-10 hidden md:block text-right">
                    <p className="text-[11px] text-white/80 font-medium">Peran Pengguna</p>
                    <p className="text-[15px] font-bold text-secondary-container">Nasabah Aktif</p>
                </div>
                <div className="absolute right-0 bottom-0 opacity-10 translate-y-1/4 translate-x-1/4 scale-150">
                    <span className="material-symbols-outlined text-[150px]">eco</span>
                </div>
            </div>

            {/* Account Status Alert if pending */}
            {auth.user.status === 'pending' && (
                <div className="bg-amber-50 border border-amber-200 rounded-2xl p-md flex items-start gap-xs text-[13px] text-amber-800 shadow-sm">
                    <span className="material-symbols-outlined text-[20px] text-amber-600 shrink-0">info</span>
                    <div>
                        <p className="font-bold">Akun Menunggu Verifikasi</p>
                        <p className="mt-xs text-amber-700">Akun Anda sedang dalam proses verifikasi oleh tim Admin. Anda dapat mengajukan setor sampah, tetapi fitur penarikan saldo baru akan aktif setelah akun diverifikasi.</p>
                    </div>
                </div>
            )}

            {/* Main Grid: Split on Desktop */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-md md:gap-lg">
                
                {/* Left Column (spans 2 on desktop) */}
                <div className="lg:col-span-2 space-y-md md:space-y-lg">
                    
                    {/* Top Stats Cards Row */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-md">
                        {/* Saldo Tabungan Card */}
                        <div className="bg-gradient-to-br from-primary-container to-primary text-white rounded-3xl p-md md:p-lg shadow-md relative overflow-hidden flex flex-col justify-between h-44 group">
                            <div className="absolute -right-4 -top-4 w-24 h-24 bg-white/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                            <div>
                                <div className="flex justify-between items-center">
                                    <span className="text-[12px] text-white/80 font-bold tracking-wide uppercase">Total Saldo Tabungan</span>
                                    <div className="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                                        <span className="material-symbols-outlined text-white text-sm" style={{ fontVariationSettings: "'FILL' 1" }}>account_balance_wallet</span>
                                    </div>
                                </div>
                                <p className="text-[28px] font-extrabold text-white mt-sm tracking-tight">Rp {new Intl.NumberFormat('id-ID').format(auth.user.saldo)}</p>
                            </div>
                            <div className="flex items-center gap-xs text-[11px] text-[#E8F5E9] bg-white/10 px-sm py-1 rounded-xl w-fit font-semibold">
                                <span className="material-symbols-outlined text-[14px]">payments</span>
                                <span>Total disetor: Rp {new Intl.NumberFormat('id-ID').format(totalDeposited)}</span>
                            </div>
                        </div>

                        {/* Quick Transaction Action Card */}
                        <div className="bg-white rounded-3xl p-md md:p-lg border border-outline-variant/30 shadow-sm flex flex-col justify-between h-44">
                            <div>
                                <span className="text-[12px] font-bold text-on-surface-variant uppercase tracking-wide">Aksi Cepat</span>
                                <p className="text-xs text-on-surface-variant mt-1">Lakukan penyetoran sampah atau tarik hasil saldo tabungan Anda di sini.</p>
                            </div>
                            <div className="grid grid-cols-2 gap-sm">
                                <Link
                                    href="/nasabah/setor"
                                    className="flex flex-col items-center justify-center gap-xs bg-primary/5 text-primary hover:bg-primary/10 rounded-2xl py-3 border border-primary/15 transition-all active:scale-95 font-bold text-[13px]"
                                >
                                    <span className="material-symbols-outlined text-[20px]" style={{ fontVariationSettings: "'FILL' 1" }}>recycling</span>
                                    <span>Setor Sampah</span>
                                </Link>
                                <Link
                                    href="/nasabah/tarik"
                                    className="flex flex-col items-center justify-center gap-xs bg-secondary-container/10 text-on-secondary-container hover:bg-secondary-container/20 rounded-2xl py-3 border border-secondary/15 transition-all active:scale-95 font-bold text-[13px]"
                                >
                                    <span className="material-symbols-outlined text-[20px]" style={{ fontVariationSettings: "'FILL' 1" }}>payments</span>
                                    <span>Tarik Saldo</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    {/* Green Impact Banner / Gamified stats */}
                    <div className="bg-white rounded-3xl border border-outline-variant/30 p-md md:p-lg shadow-sm space-y-md">
                        <div className="flex items-center justify-between">
                            <h3 className="text-[16px] font-bold text-on-surface flex items-center gap-xs">
                                <span className="material-symbols-outlined text-primary text-[20px]">eco</span>
                                Kontribusi Hijau Anda
                            </h3>
                            <span className="text-[11px] font-bold bg-primary/10 text-primary px-sm py-0.5 rounded-full">Level: Penyelamat Lingkungan</span>
                        </div>
                        <div className="grid grid-cols-3 gap-sm text-center">
                            <div className="bg-background/40 border border-outline-variant/15 rounded-2xl p-sm">
                                <span className="material-symbols-outlined text-primary text-[24px]">forest</span>
                                <p className="text-[16px] font-extrabold text-on-surface mt-xs">{(totalDeposited / 50).toFixed(1)}</p>
                                <p className="text-[10px] text-on-surface-variant font-medium">Pohon Diselamatkan</p>
                            </div>
                            <div className="bg-background/40 border border-outline-variant/15 rounded-2xl p-sm">
                                <span className="material-symbols-outlined text-primary text-[24px]">co2</span>
                                <p className="text-[16px] font-extrabold text-on-surface mt-xs">{(totalDeposited * 1.2).toFixed(1)} kg</p>
                                <p className="text-[10px] text-on-surface-variant font-medium">Emisi CO2 Ditekan</p>
                            </div>
                            <div className="bg-background/40 border border-outline-variant/15 rounded-2xl p-sm">
                                <span className="material-symbols-outlined text-primary text-[24px]">workspace_premium</span>
                                <p className="text-[16px] font-extrabold text-on-surface mt-xs">{(totalDeposited * 2.5).toFixed(0)}</p>
                                <p className="text-[10px] text-on-surface-variant font-medium">Poin Eco-Credits</p>
                            </div>
                        </div>
                    </div>

                </div>

                {/* Right Column (spans 1 on desktop) */}
                <div className="space-y-md md:space-y-lg">
                    {/* Ringkasan Aktivitas Terakhir */}
                    <div className="bg-white rounded-3xl border border-outline-variant/30 shadow-sm overflow-hidden flex flex-col justify-between">
                        <div className="p-md border-b border-outline-variant/10 flex justify-between items-center">
                            <h3 className="text-[15px] font-bold text-on-surface flex items-center gap-xs">
                                <span className="material-symbols-outlined text-primary text-[20px]">history</span>
                                Aktivitas Terakhir
                            </h3>
                            <Link href="/nasabah/riwayat" className="text-[12px] font-bold text-primary hover:text-secondary transition-colors">Lihat Semua</Link>
                        </div>
                        <div className="divide-y divide-outline-variant/10">
                            {transactions.length > 0 ? (
                                transactions.slice(0, 4).map((tx, idx) => {
                                    const isDeposit = tx.type === 'deposit';
                                    return (
                                        <div key={`${tx.type}-${tx.id}-${idx}`} className="flex items-center gap-sm p-md hover:bg-surface-container-low/30 transition-colors">
                                            <div className={`w-8 h-8 rounded-full flex items-center justify-center shrink-0 ${
                                                isDeposit ? 'bg-primary/10 text-primary' : 'bg-red-50 text-red-600'
                                            }`}>
                                                <span className="material-symbols-outlined text-[18px]">{isDeposit ? 'recycling' : 'payments'}</span>
                                            </div>
                                            <div className="flex-1 min-w-0">
                                                <p className="text-[13px] font-bold text-on-surface truncate">{tx.title}</p>
                                                <p className="text-[10px] text-on-surface-variant truncate">
                                                    {new Date(tx.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })}
                                                    {' • '}
                                                    <span className={`font-bold uppercase tracking-wider text-[9px] ${
                                                        tx.status === 'approved' ? 'text-green-600' : tx.status === 'pending' ? 'text-amber-500' : 'text-red-500'
                                                    }`}>{tx.status === 'approved' ? 'Sukses' : tx.status === 'pending' ? 'Pending' : 'Ditolak'}</span>
                                                </p>
                                            </div>
                                            <div className="text-right shrink-0">
                                                <p className={`text-[13px] font-bold ${isDeposit ? 'text-primary' : 'text-on-surface'}`}>
                                                    {isDeposit ? '+' : '-'}Rp {new Intl.NumberFormat('id-ID').format(tx.amount)}
                                                </p>
                                            </div>
                                        </div>
                                    );
                                })
                            ) : (
                                <div className="text-center py-xl text-on-surface-variant text-[13px]">
                                    Belum ada aktivitas transaksi.
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Target Tabungan Card */}
                    <div className="bg-white rounded-3xl border border-outline-variant/30 shadow-sm p-md md:p-lg space-y-md flex flex-col">
                        <div className="flex justify-between items-center border-b border-outline-variant/10 pb-sm">
                            <h3 className="text-[15px] font-bold text-on-surface flex items-center gap-xs">
                                <span className="material-symbols-outlined text-primary text-[20px]">track_changes</span>
                                Target Tabungan
                            </h3>
                            <button
                                onClick={() => setShowAddForm(!showAddForm)}
                                className="text-[12px] font-bold text-primary hover:text-secondary flex items-center gap-[2px] transition-colors"
                            >
                                <span className="material-symbols-outlined text-[16px]">{showAddForm ? 'close' : 'add'}</span>
                                {showAddForm ? 'Batal' : 'Tambah'}
                            </button>
                        </div>

                        {showAddForm && (
                            <form onSubmit={handleAddTarget} className="bg-background border border-outline-variant/20 rounded-2xl p-sm space-y-xs transition-all duration-300">
                                <div>
                                    <label className="block text-[10px] font-bold text-on-surface-variant mb-xs">Nama Target</label>
                                    <input
                                        type="text"
                                        value={data.title}
                                        onChange={e => setData('title', e.target.value)}
                                        placeholder="Contoh: Beli Sepeda Lipat"
                                        className="block w-full border border-outline-variant/50 rounded-xl px-2 py-1.5 text-xs focus:ring-primary focus:border-primary text-on-surface bg-white"
                                        required
                                    />
                                    {errors.title && <p className="text-red-500 text-[10px] mt-xs">{errors.title}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-bold text-on-surface-variant mb-xs">Jumlah Target (Rp)</label>
                                    <input
                                        type="number"
                                        value={data.target_amount}
                                        onChange={e => setData('target_amount', e.target.value)}
                                        placeholder="Min. Rp 10.000"
                                        className="block w-full border border-outline-variant/50 rounded-xl px-2 py-1.5 text-xs focus:ring-primary focus:border-primary text-on-surface bg-white"
                                        required
                                    />
                                    {errors.target_amount && <p className="text-red-500 text-[10px] mt-xs">{errors.target_amount}</p>}
                                </div>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="w-full bg-primary hover:bg-secondary text-white text-[11px] font-bold py-1.5 rounded-full transition-all duration-200 active:scale-95 disabled:opacity-50"
                                >
                                    {processing ? 'Menyimpan...' : 'Simpan Target'}
                                </button>
                            </form>
                        )}

                        <div className="space-y-sm max-h-[300px] overflow-y-auto pr-xs">
                            {targets.length > 0 ? (
                                targets.map((target) => {
                                    const progressPercent = Math.min(100, Math.round((auth.user.saldo / target.target_amount) * 100));
                                    const isAchieved = auth.user.saldo >= target.target_amount;
                                    return (
                                        <div key={target.id} className="group relative bg-[#F9FAF9] border border-outline-variant/20 rounded-2xl p-sm shadow-sm transition-all hover:border-primary/20">
                                            <button
                                                onClick={() => handleDeleteTarget(target.id)}
                                                className="absolute right-sm top-xs text-red-500 hover:bg-red-50 p-1 rounded-full transition-all opacity-0 group-hover:opacity-100 focus:opacity-100"
                                                title="Hapus Target"
                                            >
                                                <span className="material-symbols-outlined text-[16px]">delete</span>
                                            </button>

                                            <div className="space-y-xs pr-sm">
                                                <div className="flex justify-between items-start gap-xs">
                                                    <h4 className="text-[12px] font-bold text-on-surface truncate max-w-[120px]">{target.title}</h4>
                                                    <span className={`text-[9px] font-bold px-1.5 py-0.5 rounded-full whitespace-nowrap ${
                                                        isAchieved ? 'bg-primary/10 text-primary' : 'bg-blue-50 text-blue-600'
                                                    }`}>
                                                        {isAchieved ? 'Tercapai 🎉' : 'Proses'}
                                                    </span>
                                                </div>
                                                
                                                <div className="flex justify-between items-center text-[9px] text-on-surface-variant font-medium">
                                                    <span>Rp {new Intl.NumberFormat('id-ID').format(auth.user.saldo)} / Rp {new Intl.NumberFormat('id-ID').format(target.target_amount)}</span>
                                                    <span>{progressPercent}%</span>
                                                </div>

                                                <div className="w-full bg-outline-variant/20 h-1.5 rounded-full overflow-hidden">
                                                    <div 
                                                        className={`h-full rounded-full transition-all duration-500 ${
                                                            isAchieved ? 'bg-gradient-to-r from-primary to-secondary' : 'bg-gradient-to-r from-blue-500 to-indigo-500'
                                                        }`}
                                                        style={{ width: `${progressPercent}%` }}
                                                    ></div>
                                                </div>
                                            </div>
                                        </div>
                                    );
                                })
                            ) : (
                                <div className="text-center py-sm text-on-surface-variant text-[11px] space-y-1">
                                    <span className="material-symbols-outlined text-[24px] text-primary/30">track_changes</span>
                                    <p className="font-semibold text-on-surface/85">Belum Ada Target</p>
                                    <p className="text-[10px] text-on-surface-variant">Buat target untuk memantau tabungan!</p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Educational Banner */}
                    <div className="bg-gradient-to-br from-secondary-container/20 to-primary/5 rounded-3xl p-md border border-outline-variant/30 shadow-sm space-y-xs relative overflow-hidden">
                        <div className="w-8 h-8 rounded-full bg-secondary-container/25 flex items-center justify-center text-on-secondary-container mb-xs">
                            <span className="material-symbols-outlined text-[18px]">lightbulb</span>
                        </div>
                        <h4 className="text-[14px] font-bold text-primary">Tips Eco-Living</h4>
                        <p className="text-[12px] text-on-surface-variant leading-relaxed">
                            Bilas botol plastik atau wadah makanan bekas sebelum disetorkan. Wadah yang bersih meningkatkan efisiensi daur ulang dan dihargai lebih tinggi!
                        </p>
                    </div>
                </div>

            </div>
        </NasabahLayout>
    );
}

