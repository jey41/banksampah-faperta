import React, { useState, useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import NasabahLayout from '@/Layouts/NasabahLayout';
import InputError from '@/Components/InputError';
import { MapContainer, TileLayer, Marker, useMapEvents } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

// Fix leaflet icon issue in React
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
    iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
});

// Coordinate of Bank Sampah Faperta UNMUL
const BANK_SAMPAH_COORD = { lat: -0.4660341, lng: 117.1558231 };

function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Radius of the earth in km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = 
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
        Math.sin(dLon/2) * Math.sin(dLon/2)
        ; 
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
    const d = R * c; // Distance in km
    return d;
}

function MapEvents({ onLocationSelect }) {
    useMapEvents({
        click(e) {
            onLocationSelect(e.latlng.lat, e.latlng.lng);
        }
    });
    return null;
}

export default function PickupRequest({ pickupRequests = [], userAddress = '', userPhone = '' }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        pickup_address: userAddress,
        pickup_phone: userPhone,
        pickup_date: '',
        pickup_time: '',
        latitude: null,
        longitude: null,
        estimated_distance: null,
        notes: '',
    });

    const [showForm, setShowForm] = useState(true);

    const timeSlots = [
        { value: '08:00-10:00', label: '08:00 - 10:00 WIB' },
        { value: '10:00-12:00', label: '10:00 - 12:00 WIB' },
        { value: '13:00-15:00', label: '13:00 - 15:00 WIB' },
    ];

    const statusConfig = {
        pending: { label: 'Menunggu', color: 'text-amber-600', bg: 'bg-amber-50', icon: 'schedule' },
        assigned: { label: 'Dijemput', color: 'text-blue-600', bg: 'bg-blue-50', icon: 'local_shipping' },
        completed: { label: 'Selesai', color: 'text-green-600', bg: 'bg-green-50', icon: 'check_circle' },
        cancelled: { label: 'Dibatalkan', color: 'text-red-600', bg: 'bg-red-50', icon: 'cancel' },
    };

    const today = new Date().toISOString().split('T')[0];

    const submit = (e) => {
        e.preventDefault();
        post(route('nasabah.pickup.store'), {
            onSuccess: () => {
                reset();
                setShowForm(false);
            },
        });
    };

    const handleLocationSelect = (lat, lng) => {
        const distance = calculateDistance(lat, lng, BANK_SAMPAH_COORD.lat, BANK_SAMPAH_COORD.lng);
        setData(currentData => ({
            ...currentData,
            latitude: lat,
            longitude: lng,
            estimated_distance: distance.toFixed(2)
        }));
    };

    return (
        <NasabahLayout>
            <Head title="Jemput Sampah - Bank Sampah Faperta" />

            <div className="flex items-center gap-xs mt-sm text-primary">
                <Link href="/nasabah/dashboard" className="flex items-center hover:underline font-bold text-[14px]">
                    <span className="material-symbols-outlined text-[20px]">arrow_back</span>
                    Kembali ke Dasbor
                </Link>
            </div>

            <div className="space-y-xs">
                <h1 className="text-[24px] font-bold text-primary tracking-tight">Minta Jemput Sampah</h1>
                <p className="text-sm text-on-surface-variant leading-relaxed">
                    Ajukan permintaan penjemputan sampah ke rumah Anda. Petugas kami akan datang menimbang dan mencatat setoran sampah Anda langsung di lokasi.
                </p>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-md md:gap-lg items-start">
                {/* Left Column - Form */}
                <div className="lg:col-span-2 space-y-md">
                    <div className="bg-white rounded-3xl border border-outline-variant/30 p-md md:p-lg shadow-sm">
                        <div className="flex justify-between items-center border-b border-outline-variant/10 pb-sm mb-md">
                            <h3 className="text-[15px] font-bold text-on-surface flex items-center gap-xs">
                                <span className="material-symbols-outlined text-primary text-[20px]">local_shipping</span>
                                Formulir Penjemputan
                            </h3>
                        </div>

                        <form onSubmit={submit} className="space-y-md">
                            {/* Alamat */}
                            <div>
                                <label className="block text-[12px] font-bold text-on-surface-variant mb-xs">
                                    Alamat Penjemputan <span className="text-red-500">*</span>
                                </label>
                                <textarea
                                    rows="3"
                                    value={data.pickup_address}
                                    onChange={(e) => setData('pickup_address', e.target.value)}
                                    required
                                    className="block w-full border border-outline-variant/50 rounded-2xl px-sm py-sm text-[13px] focus:ring-primary focus:border-primary text-on-surface"
                                    placeholder="Masukkan alamat lengkap untuk penjemputan..."
                                />
                                <InputError message={errors.pickup_address} />
                            </div>

                            {/* Peta Lokasi */}
                            <div>
                                <label className="block text-[12px] font-bold text-on-surface-variant mb-xs">
                                    Titik Lokasi Penjemputan <span className="text-red-500">*</span>
                                </label>
                                <p className="text-[11px] text-on-surface-variant mb-2">Klik pada peta untuk menentukan lokasi rumah Anda. Jarak akan otomatis terhitung.</p>
                                <div className="h-[300px] w-full rounded-2xl overflow-hidden border border-outline-variant/50 relative z-0">
                                    {typeof window !== 'undefined' && (
                                        <MapContainer center={[BANK_SAMPAH_COORD.lat, BANK_SAMPAH_COORD.lng]} zoom={13} scrollWheelZoom={true} className="h-full w-full">
                                            <TileLayer
                                                attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                                                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                                            />
                                            <MapEvents onLocationSelect={handleLocationSelect} />
                                            {data.latitude !== null && (
                                                <Marker position={[data.latitude, data.longitude]}></Marker>
                                            )}
                                            {/* Marker Bank Sampah (Green) */}
                                            <Marker position={[BANK_SAMPAH_COORD.lat, BANK_SAMPAH_COORD.lng]} 
                                                icon={L.icon({
                                                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
                                                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                                                    iconSize: [25, 41],
                                                    iconAnchor: [12, 41],
                                                    popupAnchor: [1, -34],
                                                    shadowSize: [41, 41]
                                                })}
                                            >
                                            </Marker>
                                        </MapContainer>
                                    )}
                                </div>
                                {data.estimated_distance && (
                                    <div className="mt-2 text-[12px] font-medium text-primary flex items-center gap-1">
                                        <span className="material-symbols-outlined text-[16px]">route</span>
                                        Estimasi jarak dari Bank Sampah Faperta: <strong>{data.estimated_distance} km</strong>
                                    </div>
                                )}
                                {(!data.latitude || !data.longitude) && (
                                    <p className="text-red-500 text-[11px] mt-1">Silakan pilih lokasi penjemputan di peta.</p>
                                )}
                                <InputError message={errors.latitude} />
                            </div>

                            {/* Telepon */}
                            <div>
                                <label className="block text-[12px] font-bold text-on-surface-variant mb-xs">
                                    Nomor Telepon / WhatsApp <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.pickup_phone}
                                    onChange={(e) => setData('pickup_phone', e.target.value)}
                                    required
                                    className="block w-full border border-outline-variant/50 rounded-xl px-sm py-2 text-[13px] focus:ring-primary focus:border-primary text-on-surface bg-white"
                                    placeholder="08xxxxxxxxxx"
                                />
                                <InputError message={errors.pickup_phone} />
                            </div>

                            {/* Tanggal & Jam */}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-md">
                                <div>
                                    <label className="block text-[12px] font-bold text-on-surface-variant mb-xs">
                                        Tanggal Penjemputan <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="date"
                                        value={data.pickup_date}
                                        min={today}
                                        onChange={(e) => setData('pickup_date', e.target.value)}
                                        required
                                        className="block w-full border border-outline-variant/50 rounded-xl px-sm py-2 text-[13px] focus:ring-primary focus:border-primary text-on-surface bg-white"
                                    />
                                    <InputError message={errors.pickup_date} />
                                </div>

                                <div>
                                    <label className="block text-[12px] font-bold text-on-surface-variant mb-xs">
                                        Jam Penjemputan <span className="text-red-500">*</span>
                                    </label>
                                    <div className="space-y-xs">
                                        {timeSlots.map((slot) => (
                                            <button
                                                key={slot.value}
                                                type="button"
                                                onClick={() => setData('pickup_time', slot.value)}
                                                className={`w-full text-left px-sm py-2 rounded-xl border text-[13px] font-semibold transition-all ${
                                                    data.pickup_time === slot.value
                                                        ? 'border-primary bg-primary/5 text-primary shadow-sm'
                                                        : 'border-outline-variant/50 text-on-surface-variant hover:border-primary/40'
                                                }`}
                                            >
                                                <span className="flex items-center gap-xs">
                                                    <span className="material-symbols-outlined text-[16px]">
                                                        {data.pickup_time === slot.value ? 'radio_button_checked' : 'radio_button_unchecked'}
                                                    </span>
                                                    {slot.label}
                                                </span>
                                            </button>
                                        ))}
                                    </div>
                                    <InputError message={errors.pickup_time} />
                                </div>
                            </div>

                            {/* Catatan */}
                            <div>
                                <label className="block text-[12px] font-bold text-on-surface-variant mb-xs">
                                    Catatan Tambahan (Opsional)
                                </label>
                                <textarea
                                    rows="2"
                                    value={data.notes}
                                    onChange={(e) => setData('notes', e.target.value)}
                                    className="block w-full border border-outline-variant/50 rounded-2xl px-sm py-sm text-[13px] focus:ring-primary focus:border-primary text-on-surface"
                                    placeholder="Contoh: Sampah ada di depan pagar, rumah cat biru..."
                                />
                                <InputError message={errors.notes} />
                            </div>

                            {/* Submit */}
                            <button
                                type="submit"
                                disabled={processing || !data.pickup_time || !data.latitude || !data.longitude}
                                className="w-full flex justify-center items-center gap-xs py-3 px-4 border border-transparent rounded-full shadow-md text-sm font-bold text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 transition-colors"
                            >
                                <span className="material-symbols-outlined text-[20px]">local_shipping</span>
                                {processing ? 'Memproses...' : 'Kirim Permintaan Jemput'}
                            </button>

                            {/* Info Card */}
                            <div className="bg-blue-50 border border-blue-100 rounded-xl p-sm flex items-start gap-xs text-[11px] text-blue-800">
                                <span className="material-symbols-outlined text-[15px] text-blue-600 shrink-0">info</span>
                                <span>Petugas akan menghubungi Anda sebelum datang. Pastikan sampah sudah disiapkan dan alamat serta nomor telepon benar.</span>
                            </div>
                        </form>
                    </div>
                </div>

                {/* Right Column - Pickup History */}
                <div className="lg:col-span-1 space-y-md">
                    <div className="bg-white rounded-3xl border border-outline-variant/30 p-md md:p-lg shadow-sm">
                        <h3 className="text-[15px] font-bold text-on-surface border-b border-outline-variant/10 pb-sm mb-md flex items-center gap-xs">
                            <span className="material-symbols-outlined text-primary text-[20px]">history</span>
                            Riwayat Permintaan
                        </h3>

                        <div className="space-y-sm max-h-[400px] overflow-y-auto pr-xs">
                            {pickupRequests.length > 0 ? (
                                pickupRequests.map((req) => {
                                    const sc = statusConfig[req.status] || statusConfig.pending;
                                    return (
                                        <div key={req.id} className={`${sc.bg} border border-outline-variant/15 rounded-2xl p-sm space-y-xs`}>
                                            <div className="flex justify-between items-start">
                                                <div className="flex items-center gap-xs">
                                                    <span className={`material-symbols-outlined text-[16px] ${sc.color}`}>{sc.icon}</span>
                                                    <span className={`text-[11px] font-bold ${sc.color} uppercase tracking-wider`}>{sc.label}</span>
                                                </div>
                                                <span className="text-[10px] text-on-surface-variant">
                                                    {new Date(req.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })}
                                                </span>
                                            </div>
                                            <div className="space-y-0.5">
                                                <p className="text-[11px] text-on-surface font-semibold flex items-start gap-1">
                                                    <span className="material-symbols-outlined text-[13px] text-on-surface-variant shrink-0 mt-[1px]">calendar_today</span>
                                                    {new Date(req.pickup_date).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long' })} • {req.pickup_time}
                                                </p>
                                                <p className="text-[10px] text-on-surface-variant flex items-start gap-1 line-clamp-2">
                                                    <span className="material-symbols-outlined text-[13px] shrink-0 mt-[1px]">location_on</span>
                                                    {req.pickup_address}
                                                </p>
                                                {req.estimated_distance && (
                                                    <p className="text-[10px] text-primary flex items-start gap-1 font-medium">
                                                        <span className="material-symbols-outlined text-[13px] shrink-0 mt-[1px]">route</span>
                                                        Jarak: {req.estimated_distance} km
                                                    </p>
                                                )}
                                            </div>
                                        </div>
                                    );
                                })
                            ) : (
                                <div className="text-center py-lg text-on-surface-variant text-[12px] space-y-xs">
                                    <span className="material-symbols-outlined text-[32px] text-primary/20">local_shipping</span>
                                    <p className="font-semibold text-on-surface/80">Belum Ada Permintaan</p>
                                    <p className="text-[10px]">Buat permintaan jemput pertama Anda!</p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* How it works */}
                    <div className="bg-gradient-to-br from-primary/5 to-secondary-container/10 rounded-3xl border border-outline-variant/30 p-md shadow-sm space-y-sm">
                        <h4 className="text-[13px] font-bold text-primary flex items-center gap-xs">
                            <span className="material-symbols-outlined text-[18px]">lightbulb</span>
                            Cara Kerja Jemput Sampah
                        </h4>
                        <div className="space-y-sm">
                            {[
                                { step: '1', icon: 'edit_note', text: 'Isi formulir permintaan penjemputan' },
                                { step: '2', icon: 'notifications', text: 'Admin menerima notifikasi & menugaskan petugas' },
                                { step: '3', icon: 'local_shipping', text: 'Petugas datang ke lokasi Anda' },
                                { step: '4', icon: 'scale', text: 'Sampah ditimbang & dicatat langsung oleh petugas' },
                                { step: '5', icon: 'account_balance_wallet', text: 'Saldo masuk ke rekening tabungan Anda' },
                            ].map(({ step, icon, text }) => (
                                <div key={step} className="flex items-start gap-xs">
                                    <div className="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-[10px] font-bold shrink-0">
                                        {step}
                                    </div>
                                    <p className="text-[11px] text-on-surface-variant leading-relaxed">{text}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </NasabahLayout>
    );
}
