import React, { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import NasabahLayout from '@/Layouts/NasabahLayout';
import InputError from '@/Components/InputError';

export default function Deposit({ pricesUmum = [], pricesDonasi = [] }) {
    const [rows, setRows] = useState([{ id: Date.now(), trash_price_id: '', weight: '' }]);
    const [processing, setProcessing] = useState(false);
    const [errors, setErrors] = useState({});
    const [notes, setNotes] = useState('');
    const [donationCategory, setDonationCategory] = useState('umum');

    const activePrices = donationCategory === 'umum' ? pricesUmum : pricesDonasi;

    const addRow = () => {
        setRows([...rows, { id: Date.now(), trash_price_id: '', weight: '' }]);
    };

    const removeRow = (id) => {
        if (rows.length === 1) return;
        setRows(rows.filter(row => row.id !== id));
    };

    const handleRowChange = (index, field, value) => {
        const updatedRows = [...rows];
        updatedRows[index][field] = value;
        setRows(updatedRows);
    };

    // Calculate estimated total price
    const calculateTotal = () => {
        return rows.reduce((sum, row) => {
            const trash = activePrices.find(p => p.id === parseInt(row.trash_price_id));
            const w = parseFloat(row.weight);
            if (trash && w) {
                return sum + (w * trash.price_buy);
            }
            return sum;
        }, 0);
    };

    const calculateWeightTotal = () => {
        return rows.reduce((sum, row) => {
            const w = parseFloat(row.weight);
            return sum + (w || 0);
        }, 0);
    };

    const submit = (e) => {
        e.preventDefault();
        
        // Map rows to request structure
        const formattedItems = rows
            .filter(r => r.trash_price_id && r.weight)
            .map(r => ({
                trash_price_id: parseInt(r.trash_price_id),
                weight: parseFloat(r.weight),
            }));

        if (formattedItems.length === 0) {
            alert('Silakan pilih minimal satu item sampah dengan berat yang valid!');
            return;
        }

        setProcessing(true);
        router.post(route('nasabah.deposit.store'), {
            items: formattedItems,
            notes: notes,
            donation_category: donationCategory,
        }, {
            preserveScroll: true,
            onError: (errs) => setErrors(errs),
            onFinish: () => setProcessing(false),
        });
    };

    return (
        <NasabahLayout>
            <Head title="Setoran Donasi - Bank Sampah Faperta" />

            <div className="flex items-center gap-xs mt-sm text-primary">
                <Link href="/nasabah/dashboard" className="flex items-center hover:underline font-bold text-[14px]">
                    <span className="material-symbols-outlined text-[20px]">arrow_back</span>
                    Kembali ke Dasbor
                </Link>
            </div>

            <div className="space-y-xs">
                <h1 className="text-[24px] font-bold text-primary tracking-tight">Setoran Donasi Sampah</h1>
                <p className="text-sm text-on-surface-variant leading-relaxed">
                    Donasikan sampah Anda untuk kebaikan lingkungan dan sosial. Pilih kategori donasi dan jenis sampah yang ingin disetorkan.
                </p>
            </div>

            <form onSubmit={submit}>
                {/* Donation Category Selector */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-md">
                    <button
                        type="button"
                        onClick={() => { setDonationCategory('umum'); setRows([{ id: Date.now(), trash_price_id: '', weight: '' }]); }}
                        className={`relative rounded-3xl p-md border-2 transition-all text-left ${
                            donationCategory === 'umum'
                            ? 'border-primary bg-primary/5 shadow-md'
                            : 'border-outline-variant/30 bg-white hover:border-primary/40 shadow-sm'
                        }`}
                    >
                        <div className="flex items-start gap-sm">
                            <div className={`w-12 h-12 rounded-full flex items-center justify-center shrink-0 ${
                                donationCategory === 'umum' ? 'bg-primary text-white' : 'bg-surface-container-low text-on-surface-variant'
                            }`}>
                                <span className="material-symbols-outlined text-[24px]">delete</span>
                            </div>
                            <div className="flex-1">
                                <h3 className={`text-[15px] font-bold ${donationCategory === 'umum' ? 'text-primary' : 'text-on-surface'}`}>
                                    Sampah Umum
                                </h3>
                                <p className="text-[11px] text-on-surface-variant leading-relaxed mt-1">
                                    Sampah anorganik umum seperti plastik, kertas, kaca, dan logam untuk didaur ulang.
                                </p>
                                <span className="inline-block mt-2 text-[10px] font-bold bg-primary/10 text-primary px-2 py-0.5 rounded-full">
                                    {pricesUmum.length} jenis sampah
                                </span>
                            </div>
                            {donationCategory === 'umum' && (
                                <span className="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                            )}
                        </div>
                    </button>

                    <button
                        type="button"
                        onClick={() => { setDonationCategory('donasi'); setRows([{ id: Date.now(), trash_price_id: '', weight: '' }]); }}
                        className={`relative rounded-3xl p-md border-2 transition-all text-left ${
                            donationCategory === 'donasi'
                            ? 'border-secondary bg-secondary/5 shadow-md'
                            : 'border-outline-variant/30 bg-white hover:border-secondary/40 shadow-sm'
                        }`}
                    >
                        <div className="flex items-start gap-sm">
                            <div className={`w-12 h-12 rounded-full flex items-center justify-center shrink-0 ${
                                donationCategory === 'donasi' ? 'bg-secondary text-white' : 'bg-surface-container-low text-on-surface-variant'
                            }`}>
                                <span className="material-symbols-outlined text-[24px]">volunteer_activism</span>
                            </div>
                            <div className="flex-1">
                                <h3 className={`text-[15px] font-bold ${donationCategory === 'donasi' ? 'text-secondary' : 'text-on-surface'}`}>
                                    Sampah Donasi
                                </h3>
                                <p className="text-[11px] text-on-surface-variant leading-relaxed mt-1">
                                    Sampah khusus untuk program donasi sosial dan pemberdayaan masyarakat.
                                </p>
                                <span className="inline-block mt-2 text-[10px] font-bold bg-secondary/10 text-secondary px-2 py-0.5 rounded-full">
                                    {pricesDonasi.length} jenis sampah
                                </span>
                            </div>
                            {donationCategory === 'donasi' && (
                                <span className="material-symbols-outlined text-secondary text-[20px]">check_circle</span>
                            )}
                        </div>
                    </button>
                </div>

                {/* Responsive Grid layout for desktop */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-md md:gap-lg items-start mt-md">
                    
                    {/* Left Column - Form fields */}
                    <div className="lg:col-span-2 space-y-md">
                        <div className="bg-white rounded-3xl border border-outline-variant/30 p-md md:p-lg shadow-sm space-y-md">
                            <div className="flex justify-between items-center border-b border-outline-variant/10 pb-sm">
                                <h3 className="text-[15px] font-bold text-on-surface flex items-center gap-xs">
                                    <span className="material-symbols-outlined text-primary text-[20px]">inventory_2</span>
                                    Daftar Item {donationCategory === 'umum' ? 'Sampah Umum' : 'Sampah Donasi'}
                                </h3>
                                <span className="text-[11px] font-semibold text-on-surface-variant bg-surface-container-low px-sm py-0.5 rounded-full">
                                    {rows.length} Item
                                </span>
                            </div>

                            <div className="space-y-md">
                                {rows.map((row, index) => {
                                    const selectedPrice = activePrices.find(p => p.id === parseInt(row.trash_price_id));
                                    const itemEstValue = selectedPrice && parseFloat(row.weight) 
                                        ? parseFloat(row.weight) * selectedPrice.price_buy 
                                        : 0;

                                    return (
                                        <div key={row.id} className="relative bg-[#F9FAF9] border border-outline-variant/20 rounded-2xl p-md shadow-sm transition-all hover:border-primary/20">
                                            {rows.length > 1 && (
                                                <button
                                                    type="button"
                                                    onClick={() => removeRow(row.id)}
                                                    className="absolute right-sm top-sm text-red-500 hover:bg-red-50 p-1.5 rounded-full transition-all active:scale-95"
                                                    title="Hapus baris"
                                                >
                                                    <span className="material-symbols-outlined text-[20px]">delete</span>
                                                </button>
                                            )}

                                            <div className="grid grid-cols-1 md:grid-cols-2 gap-md pr-md">
                                                {/* Kategori Sampah */}
                                                <div>
                                                    <label className="block text-[12px] font-bold text-on-surface-variant mb-xs">
                                                        Kategori &amp; Nama Sampah
                                                    </label>
                                                    <select
                                                        value={row.trash_price_id}
                                                        required
                                                        onChange={(e) => handleRowChange(index, 'trash_price_id', e.target.value)}
                                                        className="block w-full border border-outline-variant/50 rounded-xl px-sm py-2 text-[13px] focus:ring-primary focus:border-primary text-on-surface bg-white"
                                                    >
                                                        <option value="">-- Pilih Jenis Sampah --</option>
                                                        {activePrices.length === 0 && (
                                                            <option value="" disabled>Tidak ada data harga sampah untuk kategori ini</option>
                                                        )}
                                                        {activePrices.map((p) => (
                                                            <option key={p.id} value={p.id}>
                                                                {p.name} (Rp {new Intl.NumberFormat('id-ID').format(p.price_buy)} / {p.unit})
                                                            </option>
                                                        ))}
                                                    </select>
                                                </div>

                                                {/* Berat / Volume */}
                                                <div className="flex gap-sm items-end">
                                                    <div className="flex-1">
                                                        <label className="block text-[12px] font-bold text-on-surface-variant mb-xs">
                                                            Estimasi Berat / Volume ({selectedPrice?.unit || 'kg'})
                                                        </label>
                                                        <input
                                                            type="number"
                                                            step="0.01"
                                                            value={row.weight}
                                                            required
                                                            placeholder="0.00"
                                                            onChange={(e) => handleRowChange(index, 'weight', e.target.value)}
                                                            className="block w-full border border-outline-variant/50 rounded-xl px-sm py-2 text-[13px] focus:ring-primary focus:border-primary text-on-surface bg-white"
                                                        />
                                                    </div>
                                                    {itemEstValue > 0 && (
                                                        <div className="text-right shrink-0 pb-1.5">
                                                            <p className="text-[10px] text-on-surface-variant font-medium">Estimasi Nilai</p>
                                                            <p className="text-[14px] font-bold text-primary">Rp {new Intl.NumberFormat('id-ID').format(itemEstValue)}</p>
                                                        </div>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>

                            <button
                                type="button"
                                onClick={addRow}
                                className="w-full flex items-center justify-center gap-xs py-sm border border-dashed border-primary/40 rounded-2xl text-primary font-bold text-[13px] hover:bg-primary/5 active:scale-95 transition-all"
                            >
                                <span className="material-symbols-outlined text-[18px]">add_circle</span>
                                Tambah Item Sampah
                            </button>
                        </div>

                        {/* Notes Card */}
                        <div className="bg-white rounded-3xl border border-outline-variant/30 p-md md:p-lg shadow-sm space-y-sm">
                            <label htmlFor="notes" className="block text-[13px] font-bold text-on-surface flex items-center gap-xs">
                                <span className="material-symbols-outlined text-on-surface-variant text-[18px]">notes</span>
                                Catatan Tambahan (Opsional)
                            </label>
                            <textarea
                                id="notes"
                                rows="3"
                                value={notes}
                                onChange={(e) => setNotes(e.target.value)}
                                className="block w-full border border-outline-variant/50 rounded-2xl px-sm py-sm text-[13px] focus:ring-primary focus:border-primary text-on-surface"
                                placeholder="Contoh: Plastik sudah dibilas bersih, kertas diikat rapi."
                            />
                            <InputError message={errors.notes} />
                        </div>
                    </div>

                    {/* Right Column - Sticky Calculations Summary */}
                    <div className="lg:col-span-1 lg:sticky lg:top-20 space-y-md">
                        <div className="bg-white rounded-3xl border border-outline-variant/30 p-md md:p-lg shadow-sm space-y-md">
                            <h3 className="text-[15px] font-bold text-on-surface border-b border-outline-variant/10 pb-sm flex items-center gap-xs">
                                <span className="material-symbols-outlined text-primary text-[20px]">receipt_long</span>
                                Ringkasan Donasi
                            </h3>

                            {/* Item breakdown list */}
                            <div className="space-y-sm max-h-48 overflow-y-auto pr-xs divide-y divide-outline-variant/10">
                                {rows.map((row, index) => {
                                    const selectedPrice = activePrices.find(p => p.id === parseInt(row.trash_price_id));
                                    if (!selectedPrice) return null;
                                    const w = parseFloat(row.weight) || 0;
                                    return (
                                        <div key={`summary-${row.id}`} className="flex justify-between items-center py-2 text-[12px]">
                                            <div className="min-w-0">
                                                <p className="font-bold text-on-surface truncate">{selectedPrice.name}</p>
                                                <p className="text-[10px] text-on-surface-variant">{w} {selectedPrice.unit} x Rp {new Intl.NumberFormat('id-ID').format(selectedPrice.price_buy)}</p>
                                            </div>
                                            <p className="font-bold text-primary shrink-0">
                                                Rp {new Intl.NumberFormat('id-ID').format(w * selectedPrice.price_buy)}
                                            </p>
                                        </div>
                                    );
                                })}
                                {rows.filter(r => r.trash_price_id).length === 0 && (
                                    <p className="text-center text-[12px] text-on-surface-variant py-sm">Belum ada item yang dipilih</p>
                                )}
                            </div>

                            {/* Totals */}
                            <div className="bg-gradient-to-r from-primary/10 to-secondary/15 rounded-2xl p-md space-y-sm">
                                <div className="flex justify-between text-[12px] font-bold text-on-surface-variant">
                                    <span>Total Berat</span>
                                    <span>{calculateWeightTotal().toFixed(2)} kg/L</span>
                                </div>
                                <div className="border-t border-outline-variant/20 pt-sm flex justify-between items-end">
                                    <span className="text-[12px] font-extrabold text-primary uppercase">Total Estimasi</span>
                                    <span className="text-[20px] font-extrabold text-primary leading-none">
                                        Rp {new Intl.NumberFormat('id-ID').format(calculateTotal())}
                                    </span>
                                </div>
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full flex justify-center py-3 px-4 border border-transparent rounded-full shadow-md text-sm font-bold text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 transition-colors"
                            >
                                {processing ? 'Memproses...' : 'Kirim Donasi Sampah'}
                            </button>

                            <div className="bg-green-50 border border-green-100 rounded-xl p-sm flex items-start gap-xs text-[11px] text-green-800">
                                <span className="material-symbols-outlined text-[15px] text-green-600 shrink-0">volunteer_activism</span>
                                <span>Dengan mengirimkan donasi ini, Anda turut berkontribusi untuk lingkungan dan program sosial Bank Sampah Faperta.</span>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </NasabahLayout>
    );
}
