import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import NasabahLayout from '@/Layouts/NasabahLayout';
import InputError from '@/Components/InputError';

export default function Withdraw({ saldo = 0 }) {
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
                    Cairkan uang hasil tabungan sampah Anda ke rekening bank atau dompet digital Anda dengan mudah.
                </p>
            </div>

            <form onSubmit={submit}>
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-md md:gap-lg items-start">
                    
                    {/* Left Column: Form Inputs */}
                    <div className="lg:col-span-2 space-y-md">
                        <div className="bg-white rounded-3xl border border-outline-variant/30 p-md md:p-lg shadow-sm space-y-md">
                            <h3 className="text-[15px] font-bold text-on-surface border-b border-outline-variant/10 pb-sm flex items-center gap-xs">
                                <span className="material-symbols-outlined text-primary text-[20px]">account_balance</span>
                                Rekening Tujuan &amp; Nominal
                            </h3>

                            {/* Nama Bank / E-Wallet */}
                            <div>
                                <label htmlFor="bank_name" className="block text-[12px] font-bold text-on-surface mb-xs">
                                    Nama Bank / E-Wallet
                                </label>
                                <select
                                    id="bank_name"
                                    value={data.bank_name}
                                    required
                                    onChange={(e) => setData('bank_name', e.target.value)}
                                    className="block w-full border border-outline-variant/50 rounded-xl px-sm py-2 text-[13px] focus:ring-primary focus:border-primary text-on-surface bg-white"
                                >
                                    <option value="">-- Pilih Tujuan Pencairan --</option>
                                    <option value="BCA">BCA</option>
                                    <option value="Bank Mandiri">Bank Mandiri</option>
                                    <option value="BRI">BRI</option>
                                    <option value="BNI">BNI</option>
                                    <option value="GOPAY">GOPAY</option>
                                    <option value="OVO">OVO</option>
                                    <option value="ShopeePay">ShopeePay</option>
                                    <option value="DANA">DANA</option>
                                </select>
                                <InputError message={errors.bank_name} className="mt-xs" />
                            </div>

                            {/* Nomor Rekening */}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-md">
                                <div>
                                    <label htmlFor="account_number" className="block text-[12px] font-bold text-on-surface mb-xs">
                                        Nomor Rekening / Nomor HP E-Wallet
                                    </label>
                                    <input
                                        id="account_number"
                                        type="text"
                                        value={data.account_number}
                                        required
                                        placeholder="Masukkan nomor rekening atau nomor HP"
                                        onChange={(e) => setData('account_number', e.target.value)}
                                        className="block w-full border border-outline-variant/50 rounded-xl px-sm py-2 text-[13px] focus:ring-primary focus:border-primary text-on-surface bg-white"
                                    />
                                    <InputError message={errors.account_number} className="mt-xs" />
                                </div>

                                {/* Nama Pemilik Rekening */}
                                <div>
                                    <label htmlFor="account_name" className="block text-[12px] font-bold text-on-surface mb-xs">
                                        Nama Pemilik Rekening / Akun E-Wallet
                                    </label>
                                    <input
                                        id="account_name"
                                        type="text"
                                        value={data.account_name}
                                        required
                                        placeholder="Nama penerima transfer sesuai buku tabungan"
                                        onChange={(e) => setData('account_name', e.target.value)}
                                        className="block w-full border border-outline-variant/50 rounded-xl px-sm py-2 text-[13px] focus:ring-primary focus:border-primary text-on-surface bg-white"
                                    />
                                    <InputError message={errors.account_name} className="mt-xs" />
                                </div>
                            </div>

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
                            disabled={processing || saldo < 10000}
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

                        {/* Rules Card */}
                        <div className="bg-white rounded-3xl border border-outline-variant/30 p-md md:p-lg shadow-sm space-y-sm">
                            <h4 className="text-[13px] font-bold text-on-surface flex items-center gap-xs">
                                <span className="material-symbols-outlined text-primary text-[18px]">verified_user</span>
                                Ketentuan Pencairan
                            </h4>
                            <ul className="text-[12px] text-on-surface-variant space-y-xs list-disc pl-md leading-relaxed">
                                <li>Batas penarikan saldo minimum adalah <span className="font-bold text-on-surface">Rp 10.000</span>.</li>
                                <li>Setiap penarikan harus kelipatan <span className="font-bold text-on-surface">Rp 1.000</span>.</li>
                                <li>Proses transfer ke bank atau e-wallet memerlukan waktu <span className="font-bold text-on-surface">1-3 hari kerja</span> setelah disetujui Admin.</li>
                                <li>Pastikan nomor rekening / nomor e-wallet yang diinput adalah valid dan atas nama Anda.</li>
                            </ul>
                        </div>
                    </div>

                </div>
            </form>
        </NasabahLayout>
    );
}

