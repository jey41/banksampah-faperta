import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import InputError from '@/Components/InputError';

export default function Register() {
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
        setData('email', username ? `${username}@bsfp.com` : '');
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
        });
    };

    return (
        <>
            <Head title="Daftar Akun - Bank Sampah Digital" />
            
            <div className="min-h-screen bg-background flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans antialiased">
                <div className="sm:mx-auto sm:w-full sm:max-w-md">
                    <div className="flex justify-center text-primary">
                        <span className="material-symbols-outlined text-[48px]" style={{ fontVariationSettings: "'FILL' 1" }}>recycling</span>
                    </div>
                    <h2 className="mt-4 text-center text-[28px] font-bold tracking-tight text-primary">
                        Daftar Akun Baru
                    </h2>
                    <p className="mt-2 text-center text-sm text-on-surface-variant max-w">
                        Bergabunglah menjadi nasabah dan mulailah menabung kebaikan
                    </p>
                </div>

                <div className="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                    <div className="mb-4 px-4 sm:px-0 text-left">
                        <Link
                            href="/"
                            className="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:text-secondary transition-colors"
                        >
                            <span className="material-symbols-outlined text-[18px]">arrow_back</span>
                            Kembali ke Beranda
                        </Link>
                    </div>
                    <div className="bg-white py-8 px-4 shadow-sm border border-outline-variant/30 sm:rounded-2xl sm:px-10">
                        <form onSubmit={submit} className="space-y-5">
                            <div>
                                <label htmlFor="name" className="block text-sm font-bold text-on-surface">
                                    Nama Lengkap
                                </label>
                                <div className="mt-1">
                                    <input
                                        id="name"
                                        type="text"
                                        name="name"
                                        value={data.name}
                                        required
                                        onChange={(e) => setData('name', e.target.value)}
                                        className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface"
                                        placeholder="Nama lengkap Anda"
                                    />
                                </div>
                                <InputError message={errors.name} className="mt-1" />
                            </div>

                            <div>
                                <label htmlFor="email_username" className="block text-sm font-bold text-on-surface">
                                    Alamat Email
                                </label>
                                <div className="mt-1 flex rounded-xl shadow-sm border border-outline-variant/60 focus-within:ring-1 focus-within:ring-primary focus-within:border-primary overflow-hidden">
                                    <input
                                        id="email_username"
                                        type="text"
                                        name="email_username"
                                        value={emailUsername}
                                        required
                                        onChange={handleEmailUsernameChange}
                                        className="appearance-none block w-full px-3 py-2 border-0 focus:ring-0 focus:outline-none sm:text-sm text-on-surface bg-transparent placeholder-on-surface-variant/40"
                                        placeholder="username"
                                    />
                                    <span className="inline-flex items-center px-3 bg-background border-l border-outline-variant/60 text-on-surface-variant sm:text-sm select-none">
                                        @bsfp.com
                                    </span>
                                </div>
                                <InputError message={errors.email} className="mt-1" />
                            </div>

                            <div>
                                <label htmlFor="phone" className="block text-sm font-bold text-on-surface">
                                    Nomor Telepon / WhatsApp
                                </label>
                                <div className="mt-1">
                                    <input
                                        id="phone"
                                        type="tel"
                                        name="phone"
                                        value={data.phone}
                                        required
                                        onChange={(e) => setData('phone', e.target.value)}
                                        className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface"
                                        placeholder="0812xxxxxxxx"
                                    />
                                </div>
                                <InputError message={errors.phone} className="mt-1" />
                            </div>

                            <div>
                                <label htmlFor="address" className="block text-sm font-bold text-on-surface">
                                    Alamat Rumah
                                </label>
                                <div className="mt-1">
                                    <textarea
                                        id="address"
                                        name="address"
                                        value={data.address}
                                        required
                                        onChange={(e) => setData('address', e.target.value)}
                                        className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface"
                                        placeholder="Alamat lengkap tempat tinggal Anda"
                                        rows="2"
                                    />
                                </div>
                                <InputError message={errors.address} className="mt-1" />
                            </div>

                            <div>
                                <label htmlFor="umur" className="block text-sm font-bold text-on-surface">
                                    Umur
                                </label>
                                <div className="mt-1">
                                    <input
                                        id="umur"
                                        type="number"
                                        name="umur"
                                        value={data.umur}
                                        required
                                        onChange={(e) => setData('umur', e.target.value)}
                                        className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface"
                                        placeholder="18"
                                        min="1"
                                        max="150"
                                    />
                                </div>
                                <InputError message={errors.umur} className="mt-1" />
                            </div>

                            <div>
                                <label htmlFor="gender" className="block text-sm font-bold text-on-surface">
                                    Jenis Kelamin
                                </label>
                                <div className="mt-1">
                                    <select
                                        id="gender"
                                        name="gender"
                                        value={data.gender}
                                        required
                                        onChange={(e) => setData('gender', e.target.value)}
                                        className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface bg-white"
                                    >
                                        <option value="">Pilih jenis kelamin</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <InputError message={errors.gender} className="mt-1" />
                            </div>

                            <div>
                                <label htmlFor="status_pekerjaan" className="block text-sm font-bold text-on-surface">
                                    Status Pekerjaan
                                </label>
                                <div className="mt-1">
                                    <select
                                        id="status_pekerjaan"
                                        name="status_pekerjaan"
                                        value={data.status_pekerjaan}
                                        required
                                        onChange={handleStatusPekerjaanChange}
                                        className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface bg-white"
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
                                </div>
                                <InputError message={errors.status_pekerjaan} className="mt-1" />
                            </div>

                            {data.status_pekerjaan === 'lainnya' && (
                                <div>
                                    <label htmlFor="pekerjaan_lainnya" className="block text-sm font-bold text-on-surface">
                                        Pekerjaan Lainnya
                                    </label>
                                    <div className="mt-1">
                                        <input
                                            id="pekerjaan_lainnya"
                                            type="text"
                                            name="pekerjaan_lainnya"
                                            value={data.pekerjaan_lainnya}
                                            required
                                            onChange={(e) => setData('pekerjaan_lainnya', e.target.value)}
                                            className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface"
                                            placeholder="Sebutkan pekerjaan Anda"
                                        />
                                    </div>
                                    <InputError message={errors.pekerjaan_lainnya} className="mt-1" />
                                </div>
                            )}

                            {['dosen', 'mahasiswa', 'civitas_akademika'].includes(data.status_pekerjaan) && (
                                <>
                                    <div>
                                        <label htmlFor="universitas" className="block text-sm font-bold text-on-surface">
                                            Universitas / Instansi
                                        </label>
                                        <div className="mt-1">
                                            <input
                                                id="universitas"
                                                type="text"
                                                name="universitas"
                                                value={data.universitas}
                                                required
                                                onChange={(e) => setData('universitas', e.target.value)}
                                                className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface"
                                                placeholder="Nama universitas atau instansi"
                                            />
                                        </div>
                                        <InputError message={errors.universitas} className="mt-1" />
                                    </div>

                                    <div>
                                        <label htmlFor="fakultas" className="block text-sm font-bold text-on-surface">
                                            Fakultas / Jurusan
                                        </label>
                                        <div className="mt-1">
                                            <input
                                                id="fakultas"
                                                type="text"
                                                name="fakultas"
                                                value={data.fakultas}
                                                required
                                                onChange={(e) => setData('fakultas', e.target.value)}
                                                className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface"
                                                placeholder="Nama fakultas atau jurusan"
                                            />
                                        </div>
                                        <InputError message={errors.fakultas} className="mt-1" />
                                    </div>
                                </>
                            )}

                            <div>
                                <label htmlFor="pendidikan_terakhir" className="block text-sm font-bold text-on-surface">
                                    Pendidikan Terakhir
                                </label>
                                <div className="mt-1">
                                    <select
                                        id="pendidikan_terakhir"
                                        name="pendidikan_terakhir"
                                        value={data.pendidikan_terakhir}
                                        required
                                        onChange={(e) => setData('pendidikan_terakhir', e.target.value)}
                                        className="appearance-none block w-full px-3 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface bg-white"
                                    >
                                        <option value="">Pilih pendidikan terakhir</option>
                                        <option value="sd">SD</option>
                                        <option value="smp">SMP</option>
                                        <option value="sma">SMA/SMK</option>
                                        <option value="s1">S1</option>
                                        <option value="s2">S2</option>
                                        <option value="s3">S3</option>
                                    </select>
                                </div>
                                <InputError message={errors.pendidikan_terakhir} className="mt-1" />
                            </div>

                            <div>
                                <label htmlFor="password" className="block text-sm font-bold text-on-surface">
                                    Kata Sandi
                                </label>
                                <div className="mt-1 relative flex items-center">
                                    <input
                                        id="password"
                                        type={showPassword ? 'text' : 'password'}
                                        name="password"
                                        value={data.password}
                                        required
                                        onChange={(e) => setData('password', e.target.value)}
                                        className="appearance-none block w-full pl-3 pr-10 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface"
                                        placeholder="Minimal 8 karakter"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowPassword(!showPassword)}
                                        className="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5"
                                    >
                                        <span className="material-symbols-outlined select-none text-[20px] text-on-surface-variant/70 hover:text-primary transition-colors">
                                            {showPassword ? 'visibility_off' : 'visibility'}
                                        </span>
                                    </button>
                                </div>
                                <InputError message={errors.password} className="mt-1" />
                            </div>

                            <div>
                                <label htmlFor="password_confirmation" className="block text-sm font-bold text-on-surface">
                                    Konfirmasi Kata Sandi
                                </label>
                                <div className="mt-1 relative flex items-center">
                                    <input
                                        id="password_confirmation"
                                        type={showConfirmPassword ? 'text' : 'password'}
                                        name="password_confirmation"
                                        value={data.password_confirmation}
                                        required
                                        onChange={(e) => setData('password_confirmation', e.target.value)}
                                        className="appearance-none block w-full pl-3 pr-10 py-2 border border-outline-variant/60 rounded-xl shadow-sm placeholder-on-surface-variant/40 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm text-on-surface"
                                        placeholder="Ketik ulang kata sandi"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                                        className="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5"
                                    >
                                        <span className="material-symbols-outlined select-none text-[20px] text-on-surface-variant/70 hover:text-primary transition-colors">
                                            {showConfirmPassword ? 'visibility_off' : 'visibility'}
                                        </span>
                                    </button>
                                </div>
                                <InputError message={errors.password_confirmation} className="mt-1" />
                            </div>

                            <div className="pt-sm">
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="w-full flex justify-center py-2 px-4 border border-transparent rounded-full shadow-sm text-sm font-bold text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 transition-colors"
                                >
                                    Daftar Akun
                                </button>
                            </div>
                        </form>

                        <div className="mt-6 text-center text-sm text-on-surface-variant">
                            Sudah memiliki akun?{' '}
                            <Link href="/login" className="font-bold text-primary hover:text-secondary hover:underline">
                                Masuk di sini
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

