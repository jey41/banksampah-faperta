import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { Dialog, DialogPanel, DialogTitle } from '@headlessui/react';
import InputError from '@/Components/InputError';

export default function RegisterModal({ isOpen, onClose, onSwitchToLogin }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        phone: '',
        address: '',
        umur: '',
        gender: '',
        status_pekerjaan: '',
        pekerjaan_lainnya: '',
        universitas: '',
        fakultas: '',
        pendidikan_terakhir: '',
    });

    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    const [emailUsername, setEmailUsername] = useState('');

    const handleEmailUsernameChange = (e) => {
        const username = e.target.value;
        setEmailUsername(username);
        setData('email', username ? `${username}@bsfpunmul.com` : '');
    };

    const handleStatusPekerjaanChange = (e) => {
        const val = e.target.value;
        setData((prevData) => {
            const newData = { ...prevData, status_pekerjaan: val };
            if (!['dosen', 'mahasiswa', 'civitas_akademika'].includes(val)) {
                newData.universitas = '';
                newData.fakultas = '';
            }
            if (val !== 'lainnya') {
                newData.pekerjaan_lainnya = '';
            }
            return newData;
        });
    };

    const submit = (e) => {
        e.preventDefault();
        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
            onSuccess: () => onClose(),
        });
    };

    return (
        <Dialog 
            open={isOpen} 
            onClose={onClose} 
            transition 
            className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 transition duration-300 ease-out data-[closed]:opacity-0"
        >
            <DialogPanel 
                transition 
                className="relative w-full max-w-2xl bg-white rounded-3xl p-xl shadow-xl border border-outline-variant/30 transition duration-300 ease-out data-[closed]:scale-95 data-[closed]:opacity-0 max-h-[85vh] overflow-y-auto"
            >
                <button 
                    type="button"
                    onClick={onClose}
                    className="absolute right-md top-md p-1 rounded-full text-on-surface-variant hover:bg-gray-100 hover:text-primary transition-all z-10"
                >
                    <span className="material-symbols-outlined text-[20px]">close</span>
                </button>

                <div className="text-center mb-lg">
                    <div className="flex justify-center text-primary mb-sm">
                        <span className="material-symbols-outlined text-[40px]" style={{ fontVariationSettings: "'FILL' 1" }}>recycling</span>
                    </div>
                    <DialogTitle as="h2" className="text-[24px] font-bold tracking-tight text-primary">
                        Daftar Akun Baru
                    </DialogTitle>
                    <p className="text-xs text-on-surface-variant mt-1">
                        Bergabunglah menjadi nasabah dan mulailah menabung kebaikan
                    </p>
                </div>

                <form onSubmit={submit} className="space-y-4">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-x-md gap-y-sm">
                        
                        {/* Nama Lengkap */}
                        <div>
                            <label htmlFor="modal_name" className="block text-xs font-bold text-on-surface mb-1">
                                Nama Lengkap
                            </label>
                            <input
                                id="modal_name"
                                type="text"
                                name="name"
                                value={data.name}
                                required
                                onChange={(e) => setData('name', e.target.value)}
                                className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary text-sm text-on-surface bg-white"
                                placeholder="Nama lengkap Anda"
                            />
                            <InputError message={errors.name} className="mt-1 text-xs" />
                        </div>

                        {/* Username Email */}
                        <div>
                            <label htmlFor="modal_email_username" className="block text-xs font-bold text-on-surface mb-1">
                                Alamat Email
                            </label>
                            <div className="flex rounded-xl border border-outline-variant/60 focus-within:ring-1 focus-within:ring-primary focus-within:border-primary overflow-hidden bg-white">
                                <input
                                    id="modal_email_username"
                                    type="text"
                                    name="email_username"
                                    value={emailUsername}
                                    required
                                    onChange={handleEmailUsernameChange}
                                    className="appearance-none block w-full px-3 py-2 border-0 focus:ring-0 focus:outline-none text-sm text-on-surface bg-transparent placeholder-on-surface-variant/40"
                                    placeholder="username"
                                />
                                <span className="inline-flex items-center px-3 bg-gray-50 border-l border-outline-variant/60 text-on-surface-variant text-xs select-none">
                                    @bsfpunmul.com
                                </span>
                            </div>
                            <InputError message={errors.email} className="mt-1 text-xs" />
                        </div>

                        {/* Telepon */}
                        <div>
                            <label htmlFor="modal_phone" className="block text-xs font-bold text-on-surface mb-1">
                                Nomor Telepon / WhatsApp
                            </label>
                            <input
                                id="modal_phone"
                                type="tel"
                                name="phone"
                                value={data.phone}
                                required
                                onChange={(e) => setData('phone', e.target.value)}
                                className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary text-sm text-on-surface bg-white"
                                placeholder="0812xxxxxxxx"
                            />
                            <InputError message={errors.phone} className="mt-1 text-xs" />
                        </div>

                        {/* Umur */}
                        <div>
                            <label htmlFor="modal_umur" className="block text-xs font-bold text-on-surface mb-1">
                                Umur
                            </label>
                            <input
                                id="modal_umur"
                                type="number"
                                name="umur"
                                value={data.umur}
                                required
                                onChange={(e) => setData('umur', e.target.value)}
                                className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary text-sm text-on-surface bg-white"
                                placeholder="18"
                                min="1"
                                max="150"
                            />
                            <InputError message={errors.umur} className="mt-1 text-xs" />
                        </div>

                        {/* Jenis Kelamin */}
                        <div>
                            <label htmlFor="modal_gender" className="block text-xs font-bold text-on-surface mb-1">
                                Jenis Kelamin
                            </label>
                            <select
                                id="modal_gender"
                                name="gender"
                                value={data.gender}
                                required
                                onChange={(e) => setData('gender', e.target.value)}
                                className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary text-sm text-on-surface bg-white"
                            >
                                <option value="">Pilih jenis kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <InputError message={errors.gender} className="mt-1 text-xs" />
                        </div>

                        {/* Pendidikan Terakhir */}
                        <div>
                            <label htmlFor="modal_pendidikan" className="block text-xs font-bold text-on-surface mb-1">
                                Pendidikan Terakhir
                            </label>
                            <select
                                id="modal_pendidikan"
                                name="pendidikan_terakhir"
                                value={data.pendidikan_terakhir}
                                required
                                onChange={(e) => setData('pendidikan_terakhir', e.target.value)}
                                className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary text-sm text-on-surface bg-white"
                            >
                                <option value="">Pilih pendidikan terakhir</option>
                                <option value="sd">SD</option>
                                <option value="smp">SMP</option>
                                <option value="sma">SMA/SMK</option>
                                <option value="s1">S1</option>
                                <option value="s2">S2</option>
                                <option value="s3">S3</option>
                            </select>
                            <InputError message={errors.pendidikan_terakhir} className="mt-1 text-xs" />
                        </div>

                        {/* Status Pekerjaan */}
                        <div>
                            <label htmlFor="modal_status_pekerjaan" className="block text-xs font-bold text-on-surface mb-1">
                                Status Pekerjaan
                            </label>
                            <select
                                id="modal_status_pekerjaan"
                                name="status_pekerjaan"
                                value={data.status_pekerjaan}
                                required
                                onChange={handleStatusPekerjaanChange}
                                className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary text-sm text-on-surface bg-white"
                            >
                                <option value="">Pilih status pekerjaan</option>
                                <option value="bekerja">Bekerja</option>
                                <option value="tidak_bekerja">Tidak Bekerja</option>
                                <option value="pelajar">Pelajar</option>
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="dosen">Dosen</option>
                                <option value="civitas_akademika">Civitas Akademika</option>
                                <option value="pensiun">Pensiun</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                            <InputError message={errors.status_pekerjaan} className="mt-1 text-xs" />
                        </div>

                        {/* Pekerjaan Lainnya (Conditional) */}
                        {data.status_pekerjaan === 'lainnya' && (
                            <div>
                                <label htmlFor="modal_pekerjaan_lainnya" className="block text-xs font-bold text-on-surface mb-1">
                                    Pekerjaan Lainnya
                                </label>
                                <input
                                    id="modal_pekerjaan_lainnya"
                                    type="text"
                                    name="pekerjaan_lainnya"
                                    value={data.pekerjaan_lainnya}
                                    required
                                    onChange={(e) => setData('pekerjaan_lainnya', e.target.value)}
                                    className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary text-sm text-on-surface bg-white"
                                    placeholder="Sebutkan pekerjaan Anda"
                                />
                                <InputError message={errors.pekerjaan_lainnya} className="mt-1 text-xs" />
                            </div>
                        )}

                        {/* Universitas/Fakultas (Conditional) */}
                        {['dosen', 'mahasiswa', 'civitas_akademika'].includes(data.status_pekerjaan) && (
                            <>
                                <div>
                                    <label htmlFor="modal_universitas" className="block text-xs font-bold text-on-surface mb-1">
                                        Universitas / Instansi
                                    </label>
                                    <input
                                        id="modal_universitas"
                                        type="text"
                                        name="universitas"
                                        value={data.universitas}
                                        required
                                        onChange={(e) => setData('universitas', e.target.value)}
                                        className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary text-sm text-on-surface bg-white"
                                        placeholder="Nama universitas atau instansi"
                                    />
                                    <InputError message={errors.universitas} className="mt-1 text-xs" />
                                </div>

                                <div>
                                    <label htmlFor="modal_fakultas" className="block text-xs font-bold text-on-surface mb-1">
                                        Fakultas / Jurusan
                                    </label>
                                    <input
                                        id="modal_fakultas"
                                        type="text"
                                        name="fakultas"
                                        value={data.fakultas}
                                        required
                                        onChange={(e) => setData('fakultas', e.target.value)}
                                        className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary text-sm text-on-surface bg-white"
                                        placeholder="Nama fakultas atau jurusan"
                                    />
                                    <InputError message={errors.fakultas} className="mt-1 text-xs" />
                                </div>
                            </>
                        )}

                        {/* Alamat Rumah */}
                        <div className="md:col-span-2">
                            <label htmlFor="modal_address" className="block text-xs font-bold text-on-surface mb-1">
                                Alamat Rumah
                            </label>
                            <textarea
                                id="modal_address"
                                name="address"
                                value={data.address}
                                required
                                onChange={(e) => setData('address', e.target.value)}
                                className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary text-sm text-on-surface bg-white"
                                placeholder="Alamat lengkap tempat tinggal Anda"
                                rows="2"
                            />
                            <InputError message={errors.address} className="mt-1 text-xs" />
                        </div>

                        {/* Kata Sandi */}
                        <div>
                            <label htmlFor="modal_reg_password" className="block text-xs font-bold text-on-surface mb-1">
                                Kata Sandi
                            </label>
                            <div className="relative flex items-center">
                                <input
                                    id="modal_reg_password"
                                    type={showPassword ? 'text' : 'password'}
                                    name="password"
                                    value={data.password}
                                    required
                                    onChange={(e) => setData('password', e.target.value)}
                                    className="appearance-none block w-full pl-3 pr-10 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary text-sm text-on-surface bg-white"
                                    placeholder="Minimal 8 karakter"
                                />
                                <button
                                    type="button"
                                    onClick={() => setShowPassword(!showPassword)}
                                    className="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5"
                                >
                                    <span className="material-symbols-outlined select-none text-[18px] text-on-surface-variant/70 hover:text-primary transition-colors">
                                        {showPassword ? 'visibility_off' : 'visibility'}
                                    </span>
                                </button>
                            </div>
                            <InputError message={errors.password} className="mt-1 text-xs" />
                        </div>

                        {/* Konfirmasi Kata Sandi */}
                        <div>
                            <label htmlFor="modal_password_confirmation" className="block text-xs font-bold text-on-surface mb-1">
                                Konfirmasi Kata Sandi
                            </label>
                            <div className="relative flex items-center">
                                <input
                                    id="modal_password_confirmation"
                                    type={showConfirmPassword ? 'text' : 'password'}
                                    name="password_confirmation"
                                    value={data.password_confirmation}
                                    required
                                    onChange={(e) => setData('password_confirmation', e.target.value)}
                                    className="appearance-none block w-full pl-3 pr-10 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary text-sm text-on-surface bg-white"
                                    placeholder="Ketik ulang kata sandi"
                                />
                                <button
                                    type="button"
                                    onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                                    className="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5"
                                >
                                    <span className="material-symbols-outlined select-none text-[18px] text-on-surface-variant/70 hover:text-primary transition-colors">
                                        {showConfirmPassword ? 'visibility_off' : 'visibility'}
                                    </span>
                                </button>
                            </div>
                            <InputError message={errors.password_confirmation} className="mt-1 text-xs" />
                        </div>

                    </div>

                    <div className="pt-md md:col-span-2">
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full flex justify-center py-2 px-4 border border-transparent rounded-full shadow-sm text-sm font-bold text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 transition-colors"
                        >
                            {processing ? 'Mendaftarkan...' : 'Daftar Akun'}
                        </button>
                    </div>
                </form>

                <div className="mt-6 text-center text-xs text-on-surface-variant">
                    Sudah memiliki akun?{' '}
                    <button 
                        type="button"
                        onClick={onSwitchToLogin}
                        className="font-bold text-primary hover:text-secondary hover:underline bg-transparent border-0 cursor-pointer"
                    >
                        Masuk di sini
                    </button>
                </div>
            </DialogPanel>
        </Dialog>
    );
}
