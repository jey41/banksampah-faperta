import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import NasabahLayout from '@/Layouts/NasabahLayout';
import InputError from '@/Components/InputError';

export default function Withdraw({ saldo = 0 }) {
    const [withdrawalMethod, setWithdrawalMethod] = useState('transfer_bank');
    const [bankType, setBankType] = useState('lainnya');

    const { data, setData, post, processing, errors } = useForm({
        amount: '',
        bank_name: '',
        account_number: '',
        account_name: '',
        notes: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('nasabah.withdraw.store'));
    };

    const getAdminFee = () => {
        if (withdrawalMethod === 'tunai') return 0;
        if (bankType === 'btn') return 0;
        return 2500;
    };

    const adminFee = getAdminFee();
    const totalDeduction = parseInt(data.amount || 0) + adminFee;
    const isSaldoSufficient = saldo >= totalDeduction;

    return (
        <NasabahLayout>
            <Head title="Tarik Saldo - Bank Sampah Faperta" />

            <div className="flex items-center gap-xs mt-sm text-primary">
                <Link href="/nasabah/dashboard" className="flex items-center hover:underline font-bold text-[14px]">
                    <span className="material-symbols-outlined text-[20px]">arrow_back</span>
                    Kembali ke Dasbor
                </Link>
            </div>

            <div className="space-y-xs">
                <h1 className="text-[24px] font-bold text-primary tracking-tight">Penarikan Saldo</h1>
                <p className="text-sm text-on-surface-variant leading-relaxed">
                    Cairkan uang hasil tabungan sampah Anda dengan metode yang Anda pilih.
                </p>
            </div>

            <form onSubmit={submit}>
                {/* Method Selector */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-md">
                    <button
                        type="button"
                        onClick={() => { setWithdrawalMethod('tunai'); setData('bank_name', ''); setData('account_number', ''); setData('account_name', ''); }}
                        className={`relative rounded-3xl p-md border-2 transition-all text-left ${
                            withdrawalMethod === 'tunai'
                            ? 'border-success bg-success/5 shadow-md'
                            : 'border-outline-variant/30 bg-white hover:border-success/40 shadow-sm'
                        }`}
                    >
                        <div className="flex items-start gap-sm">
                            <div className={`w-12 h-12 rounded-full flex items-center justify-center shrink-0 ${
                                withdrawalMethod === 'tunai' ? 'bg-success text-white' : 'bg-surface-container-low text-on-surface-variant'
                            }`}>
                                <span className="material-symbols-outlined text-[24px]">payments</span>
                            </div>
                            <div className="flex-1">
                                <h3 className={`text-[15px] font-bold ${withdrawalMethod === 'tunai' ? 'text-success' : 'text-on-surface'}`}>
                                    Uang Tunai
                                </h3>
                                <p className="text-[11px] text-on-surface-variant leading-relaxed mt-1">
                                    Ambil langsung uang tunai di kantor Bank Sampah Faperta.
                                </p>
                                <span className="inline-block mt-2 text-[10px] font-bold bg-success/10 text-success px-2 py-0.5 rounded-full">
                                    Bebas biaya admin
                                </span>
                            </div>
                            {withdrawalMethod === 'tunai' && (
                                <span className="material-symbols-outlined text-success text-[20px]">check_circle</span>
                            )}
                        </div>
                    </button>

                    <button
                        type="button"
                        onClick={() => setWithdrawalMethod('transfer_bank')}
                        className={`relative rounded-3xl p-md border-2 transition-all text-left ${
                            withdrawalMethod === 'transfer_bank'
                            ? 'border-primary bg-primary/5 shadow-md'
                            : 'border-outline-variant/30 bg-white hover:border-primary/40 shadow-sm'
                        }`}
                    >
                        <div className="flex items-start gap-sm">
                            <div className={`w-12 h-12 rounded-full flex items-center justify-center shrink-0 ${
                                withdrawalMethod === 'transfer_bank' ? 'bg-primary text-white' : 'bg-surface-container-low text-on-surface-variant'
                            }`}>
                                <span className="material-symbols-outlined text-[24px]">account_balance</span>
                            </div>
                            <div className="flex-1">
                                <h3 className={`text-[15px] font-bold ${withdrawalMethod === 'transfer_bank' ? 'text-primary' : 'text-on-surface'}`}>
                                    Transfer Bank Konvensional
                                </h3>
                                <p className="text-[11px] text-on-surface-variant leading-relaxed mt-1">
                                    Transfer ke rekening bank konvensional pilihan Anda.
                                </p>
                            </div>
                            {withdrawalMethod === 'transfer_bank' && (
                                <span className="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                            )}
                        </div>
                    </button>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-md md:gap-lg items-start mt-md">
                    
                    {/* Left Column: Form Inputs */}
                    <div className="lg:col-span-2 space-y-md">
                        <div className="bg-white rounded-3xl border border-outline-variant/30 p-md md:p-lg shadow-sm space-y-md">
                            <h3 className="text-[15px] font-bold text-on-surface border-b border-outline-variant/10 pb-sm flex items-center gap-xs">
                                <span className="material-symbols-outlined text-primary text-[20px]">account_balance</span>
                                Detail {withdrawalMethod === 'tunai' ? 'Penarikan Tunai' : 'Transfer Bank'}
                            </h3>

                            {withdrawalMethod === 'transfer_bank' && (
                                <>
                                    {/* Nama Bank */}
                                    <div>
                                        <label htmlFor="bank_name" className="block text-[12px] font-bold text-on-surface mb-xs">
                                            Nama Bank
                                        </label>
                                        <select
                                            id="bank_name"
                                            value={data.bank_name}
                                            required
                                            onChange={(e) => {
                                                setData('bank_name', e.target.value);
                                                // Auto-detect bank type
                                                const val = e.target.value.toLowerCase();
                                                if (val.includes('btn')) {
                                                    setBankType('btn');
                                                } else if (val) {
                                                    setBankType('lainnya');
                                                } else {
                                                    setBankType('lainnya');
                                                }
                                            }}
                                            className="block w-full border border-outline-variant/50 rounded-xl px-sm py-2 text-[13px] focus:ring-primary focus:border-primary text-on-surface bg-white"
                                        >
                                            <option value="">-- Pilih Bank --</option>
                                            <option value="Bank BTN">Bank BTN</option>
                                            <option value="BCA">BCA</option>
                                            <option value="Bank Mandiri">Bank Mandiri</option>
                                            <option value="BRI">BRI</option>
                                            <option value="BNI">BNI</option>
                                        </select>
                                        <InputError message={errors.bank_name} className="mt-xs" />
                                    </div>

                                    {/* Nomor Rekening */}
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-md">
                                        <div>
                                            <label htmlFor="account_number" className="block text-[12px] font-bold text-on-surface mb-xs">
                                                Nomor Rekening
                                            </label>
                                            <input
                                                id="account_number"
                                                type="text"
                                                value={data.account_number}
                                                required
                                                placeholder="Masukkan nomor rekening"
                                                onChange={(e) => setData('account_number', e.target.value)}
                                                className="block w-full border border-outline-variant/50 rounded-xl px-sm py-2 text-[13px] focus:ring-primary focus:border-primary text-on-surface bg-white"
                                            />
                                            <InputError message={errors.account_number} className="mt-xs" />
                                        </div>

                                        <div>
                                            <label htmlFor="account_name" className="block text-[12px] font-bold text-on-surface mb-xs">
                                                Nama Pemilik Rekening
                                            </label>
                                            <input
                                                id="account_name"
                                                type="text"
                                                value={data.account_name}
                                                required
                                                placeholder="Nama sesuai buku tabungan"
                                                onChange={(e) => setData('account_name', e.target.value)}
                                                className="block w-full border border-outline-variant/50 rounded-xl px-sm py-2 text-[13px] focus:ring-primary focus:border-primary text-on-surface bg-white"
                                            />
                                            <InputError message={errors.account_name} className="mt-xs" />
                                        </div>
                                    </div>
                                </>
                            )}

                            {withdrawalMethod === 'tunai' && (
                                <div className="bg-success/5 border border-success/20 rounded-2xl p-md flex items-start gap-sm">
                                    <span className="material-symbols-outlined text-success text-[20px]">info</span>
                                    <div>
                                        <p className="text-[13px] font-bold text-success">Penarikan Tunai</p>
                                        <p className="text-[12px] text-success/80 mt-1">
                                            Penarikan tunai dapat dilakukan di kantor Bank Sampah Faperta pada jam operasional. 
                                            Bawa kartu identitas (KTP) dan buku tabungan untuk proses pencairan.
                                        </p>
                                    </div>
                                </div>
                            )}

                            {/* Jumlah Penarikan */}
                            <div>
                                <label htmlFor="amount" className="block text-[12px] font-bold text-on-surface mb-xs">
                                    Jumlah Penarikan (Rp)
                                </label>
                                <input
                                    id="amount"
                                    type="number"
                                    min="10000"
                                    max={saldo}
                                    value={data.amount}
                                    required
                                    placeholder="Minimal Rp 10.000"
                                    onChange={(e) => setData('amount', e.target.value)}
                                    className="block w-full border border-outline-variant/50 rounded-xl px-sm py-2 text-[13px] focus:ring-primary focus:border-primary text-on-surface bg-white"
                                />
                                <p className="text-[10px] text-on-surface-variant mt-xs font-semibold">Minimal penarikan Rp 10.000. Harus kelipatan Rp 1.000.</p>
                                <InputError message={errors.amount} className="mt-xs" />
                            </div>

                            {/* Catatan / Keterangan */}
                            <div>
                                <label htmlFor="notes" className="block text-[12px] font-bold text-on-surface mb-xs">
                                    Catatan Tambahan (Opsional)
                                </label>
                                <textarea
                                    id="notes"
                                    rows="2"
                                    value={data.notes}
                                    onChange={(e) => setData('notes', e.target.value)}
                                    className="block w-full border border-outline-variant/50 rounded-2xl px-sm py-xs text-[13px] focus:ring-primary focus:border-primary text-on-surface bg-white"
                                    placeholder="Contoh: Mohon segera diproses untuk keperluan sekolah."
                                />
                                <InputError message={errors.notes} className="mt-xs" />
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={processing || saldo < 10000 || !isSaldoSufficient}
                            className="w-full flex justify-center py-3 px-4 border border-transparent rounded-full shadow-md text-sm font-bold text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 transition-colors"
                        >
                            {processing ? 'Memproses...' : 'Kirim Pengajuan Penarikan'}
                        </button>
                    </div>

                    {/* Right Column: Balance Box and Guidelines */}
                    <div className="lg:col-span-1 space-y-md">
                        {/* Saldo Info Box */}
                        <div className="bg-gradient-to-br from-primary-container to-primary text-white rounded-3xl p-md md:p-lg shadow-md relative overflow-hidden flex flex-col justify-between h-36">
                            <div className="absolute -right-4 -top-4 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
                            <div>
                                <p className="text-[11px] text-[#E8F5E9] font-bold tracking-wide uppercase">Saldo Tersedia</p>
                                <p className="text-[28px] font-extrabold mt-sm">Rp {new Intl.NumberFormat('id-ID').format(saldo)}</p>
                            </div>
                            <div className="w-8 h-8 rounded-full bg-white/25 flex items-center justify-center self-end">
                                <span className="material-symbols-outlined text-white text-[20px]">payments</span>
                            </div>
                        </div>

                        {/* Admin Fee Info */}
                        <div className={`rounded-3xl p-md border shadow-sm ${
                            adminFee > 0 
                            ? 'bg-amber-50 border-amber-200' 
                            : 'bg-green-50 border-green-200'
                        }`}>
                            <div className="flex items-start gap-sm">
                                <span className={`material-symbols-outlined text-[20px] ${
                                    adminFee > 0 ? 'text-amber-600' : 'text-green-600'
                                }`}>
                                    {adminFee > 0 ? 'info' : 'check_circle'}
                                </span>
                                <div>
                                    <p className={`text-[12px] font-bold ${
                                        adminFee > 0 ? 'text-amber-800' : 'text-green-800'
                                    }`}>
                                        Informasi Biaya Admin
                                    </p>
                                    <p className={`text-[11px] mt-1 ${
                                        adminFee > 0 ? 'text-amber-700' : 'text-green-700'
                                    }`}>
                                        {withdrawalMethod === 'tunai' 
                                            ? 'Penarikan tunai bebas biaya admin ✓'
                                            : bankType === 'btn'
                                                ? 'Transfer ke Bank BTN bebas biaya admin ✓'
                                                : 'Biaya admin Rp 2.500 akan dipotong dari saldo Anda untuk transfer ke bank non-BTN.'
                                        }
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Calculation Summary when amount is entered */}
                        {data.amount && parseInt(data.amount) > 0 && (
                            <div className="bg-white rounded-3xl border border-outline-variant/30 p-md shadow-sm space-y-sm">
                                <h4 className="text-[12px] font-bold text-on-surface-variant uppercase tracking-wide">Rincian Penarikan</h4>
                                <div className="space-y-1 text-[12px]">
                                    <div className="flex justify-between">
                                        <span className="text-on-surface-variant">Jumlah Penarikan</span>
                                        <span className="font-bold">Rp {new Intl.NumberFormat('id-ID').format(data.amount)}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-on-surface-variant">Biaya Admin</span>
                                        <span className={`font-bold ${adminFee > 0 ? 'text-amber-600' : 'text-green-600'}`}>
                                            {adminFee > 0 ? `Rp ${new Intl.NumberFormat('id-ID').format(adminFee)}` : 'Gratis'}
                                        </span>
                                    </div>
                                    <div className="border-t border-outline-variant/20 pt-1 flex justify-between">
                                        <span className="font-bold">Total Potongan</span>
                                        <span className={`font-bold ${isSaldoSufficient ? 'text-on-surface' : 'text-red-600'}`}>
                                            Rp {new Intl.NumberFormat('id-ID').format(totalDeduction)}
                                        </span>
                                    </div>
                                    {!isSaldoSufficient && (
                                        <p className="text-[10px] text-red-600 font-semibold mt-1">
                                            Saldo tidak mencukupi untuk total potongan!
                                        </p>
                                    )}
                                </div>
                            </div>
                        )}

                        {/* Rules Card */}
                        <div className="bg-white rounded-3xl border border-outline-variant/30 p-md md:p-lg shadow-sm space-y-sm">
                            <h4 className="text-[13px] font-bold text-on-surface flex items-center gap-xs">
                                <span className="material-symbols-outlined text-primary text-[18px]">verified_user</span>
                                Ketentuan Pencairan
                            </h4>
                            <ul className="text-[12px] text-on-surface-variant space-y-xs list-disc pl-md leading-relaxed">
                                <li>Batas penarikan saldo minimum adalah <span className="font-bold text-on-surface">Rp 10.000</span>.</li>
                                <li>Setiap penarikan harus kelipatan <span className="font-bold text-on-surface">Rp 1.000</span>.</li>
                                <li>Pengajuan penarikan hanya dilayani pada <span className="font-bold text-on-surface">jam 08:00 - 16:00</span>.</li>
                                <li>Proses pencairan memerlukan waktu <span className="font-bold text-on-surface">H-2</span> dari hari pengajuan.</li>
                                <li>Transfer ke bank non-BTN dikenakan biaya admin <span className="font-bold text-on-surface">Rp 2.500</span>.</li>
                                <li>Penarikan tunai dan transfer ke Bank BTN <span className="font-bold text-on-surface">bebas biaya admin</span>.</li>
                            </ul>
                        </div>
                    </div>

                </div>
            </form>
        </NasabahLayout>
    );
}
