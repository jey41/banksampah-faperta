import React, { useState, useEffect } from 'react';
import { Link, Head, usePage } from '@inertiajs/react';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import LoginModal from '@/Components/Auth/LoginModal';
import RegisterModal from '@/Components/Auth/RegisterModal';

export default function Welcome({ prices = [], articles = [], totalCarbonContribution = 0 }) {
    const { auth } = usePage().props;
    const [activeSection, setActiveSection] = useState('home');
    const [clickedId, setClickedId] = useState(null);
    const [isLoginOpen, setIsLoginOpen] = useState(false);
    const [isRegisterOpen, setIsRegisterOpen] = useState(false);

    const handleSwitchToRegister = () => {
        setIsLoginOpen(false);
        setTimeout(() => setIsRegisterOpen(true), 250);
    };

    const handleSwitchToLogin = () => {
        setIsRegisterOpen(false);
        setTimeout(() => setIsLoginOpen(true), 250);
    };

    useEffect(() => {
        const observerOptions = {
            root: null,
            rootMargin: '-50% 0px -50% 0px',
            threshold: 0
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    setActiveSection(entry.target.id);
                }
            });
        }, observerOptions);

        const sections = ['home', 'alur', 'jadwal', 'harga', 'artikel'];
        sections.forEach((id) => {
            const el = document.getElementById(id);
            if (el) observer.observe(el);
        });

        return () => observer.disconnect();
    }, []);

    const handleNavClick = (e, sectionId) => {
        e.preventDefault();
        setActiveSection(sectionId);
        setClickedId(sectionId);
        
        setTimeout(() => setClickedId(null), 300);

        const element = document.getElementById(sectionId);
        if (element) {
            const yOffset = -64; // header height offset
            const y = element.getBoundingClientRect().top + window.pageYOffset + yOffset;
            window.scrollTo({ top: y, behavior: 'smooth' });
        }
    };

    return (
        <>
            <style>{`
                @keyframes nav-click-bounce {
                    0% { transform: scale(1); }
                    30% { transform: scale(0.92); }
                    100% { transform: scale(1); }
                }
                .nav-clicked {
                    animation: nav-click-bounce 0.3s cubic-bezier(0.25, 1, 0.5, 1);
                }
                
                .nav-link-underline::after {
                    content: '';
                    display: block;
                    width: 0;
                    height: 2px;
                    background-color: #2d5a27;
                    transition: width 0.3s cubic-bezier(0.25, 1, 0.5, 1);
                    margin-top: 2px;
                    border-radius: 9999px;
                }
                
                .nav-link-active {
                    color: #2d5a27 !important;
                }
                
                .nav-link-active::after {
                    width: 100%;
                }
            `}</style>
            <Head title="Bank Sampah Faperta Unmul - Kelola Sampah Jadi Berkah" />
            
            <div className="bg-background text-on-surface font-sans min-h-screen flex flex-col antialiased">
                {/* TopNavBar */}
                <header className="bg-white border-b border-outline-variant/30 sticky top-0 z-50 shadow-sm">
                    <div className="flex justify-between items-center w-full px-lg md:px-xl max-w-container-max mx-auto h-16">
                        <div className="text-[20px] font-bold text-primary flex items-center gap-xs">
                            <span className="material-symbols-outlined text-[28px]" style={{ fontVariationSettings: "'FILL' 1" }}>recycling</span>
                            BSFP Unmul
                        </div>
                        <nav className="hidden md:flex items-center gap-lg font-semibold text-[14px]">
                            {[
                                { id: 'home', label: 'Beranda' },
                                { id: 'alur', label: 'Alur Kerja' },
                                { id: 'jadwal', label: 'Jadwal' },
                                { id: 'harga', label: 'Katalog Harga' },
                                { id: 'artikel', label: 'Edukasi' }
                            ].map((item) => (
                                <a
                                    key={item.id}
                                    href={`#${item.id}`}
                                    onClick={(e) => handleNavClick(e, item.id)}
                                    className={`relative text-on-surface-variant hover:text-primary transition-all duration-300 nav-link-underline ${
                                        activeSection === item.id ? 'nav-link-active' : ''
                                    } ${
                                        clickedId === item.id ? 'nav-clicked' : ''
                                    }`}
                                >
                                    {item.label}
                                </a>
                            ))}
                        </nav>
                        <div className="flex items-center gap-sm">
                            {auth.user ? (
                                auth.user.role === 'nasabah' ? (
                                    <Link
                                        href="/nasabah/dashboard"
                                        className="bg-primary text-white font-semibold text-[14px] px-lg py-sm rounded-full hover:bg-opacity-90 transition-all shadow-md"
                                    >
                                        Dashboard
                                    </Link>
                                ) : (
                                    <a
                                        href="/admin"
                                        className="bg-primary text-white font-semibold text-[14px] px-lg py-sm rounded-full hover:bg-opacity-90 transition-all shadow-md"
                                    >
                                        Dashboard
                                    </a>
                                )
                            ) : (
                                <>
                                    <button
                                        onClick={() => setIsLoginOpen(true)}
                                        className="text-primary font-semibold text-[14px] px-md py-sm rounded-full hover:bg-surface-container-low transition-all bg-transparent border-0 cursor-pointer"
                                    >
                                        Masuk
                                    </button>
                                    <button
                                        onClick={() => setIsRegisterOpen(true)}
                                        className="bg-primary text-white font-semibold text-[14px] px-lg py-sm rounded-full hover:bg-opacity-90 transition-all shadow-md cursor-pointer border-0"
                                    >
                                        Daftar
                                    </button>
                                </>
                            )}
                        </div>
                    </div>
                </header>

                <main className="flex-grow">
                    {/* Hero Section */}
                    <section id="home" className="relative bg-white overflow-hidden py-2xl md:py-32 border-b border-outline-variant/20">
                        <div className="absolute inset-0 z-0">
                            <img 
                                alt="Hero Background" 
                                className="w-full h-full object-cover opacity-15 object-center" 
                                src="/images/hero-bfsp.jpeg"
                            />
                            <div className="absolute inset-0 bg-gradient-to-r from-background via-background/5 to-transparent"></div>
                        </div>
                        <div className="relative z-10 max-w-container-max mx-auto px-lg md:px-xl grid grid-cols-1 md:grid-cols-2 gap-2xl items-center">
                            <div className="space-y-md">
                                <h1 className="text-[36px] md:text-[48px] leading-[1.1] font-bold text-primary">
                                    Kelola Sampah <br/><span className="text-secondary">Jadi Berkah</span>
                                </h1>
                                <p className="text-[16px] md:text-[18px] text-on-surface-variant max-w-lg leading-relaxed">
                                    Ubah kebiasaan membuang menjadi menabung. Bergabunglah dengan kami untuk melestarikan lingkungan sambil membangun simpanan yang bermanfaat bagi masa depan Anda.
                                </p>
                                <div className="pt-sm">
                                    {auth.user ? (
                                        auth.user.role === 'nasabah' ? (
                                            <Link
                                                href="/nasabah/dashboard"
                                                className="inline-block bg-primary text-white font-semibold text-[14px] px-xl py-md rounded-full shadow-lg hover:bg-secondary transition-all"
                                            >
                                                Pergi ke Dashboard
                                            </Link>
                                        ) : (
                                            <a
                                                href="/admin"
                                                className="inline-block bg-primary text-white font-semibold text-[14px] px-xl py-md rounded-full shadow-lg hover:bg-secondary transition-all"
                                            >
                                                Pergi ke Dashboard
                                            </a>
                                        )
                                    ) : (
                                        <button
                                            onClick={() => setIsRegisterOpen(true)}
                                            className="inline-block bg-primary text-white font-semibold text-[14px] px-xl py-md rounded-full shadow-lg hover:bg-secondary transition-all cursor-pointer border-0 text-center"
                                        >
                                            Daftar Sekarang
                                        </button>
                                    )}
                                </div>
                            </div>
                            <div className="hidden md:block relative">
                                <div className="bg-white/80 backdrop-blur-md border border-white/40 rounded-3xl p-xl shadow-[0px_4px_20px_rgba(45,90,39,0.08)] transform rotate-3 hover:rotate-0 transition-transform duration-500 max-w-md mx-auto">
                                    <div className="flex items-center gap-md mb-lg">
                                        <div className="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center text-on-primary">
                                            <span className="material-symbols-outlined text-[24px]" style={{ fontVariationSettings: "'FILL' 1" }}>eco</span>
                                        </div>
                                        <div>
                                            <p className="text-[12px] text-on-surface-variant font-medium">Total Kontribusi Karbon</p>
                                            <p className="text-[20px] text-primary font-bold">{Number(totalCarbonContribution || 0).toFixed(1)} kg CO₂</p>
                                        </div>
                                    </div>
                                    <div className="w-full h-2 bg-surface-container-high rounded-full overflow-hidden mb-sm">
                                        <div className="h-full bg-gradient-to-r from-primary-container to-secondary w-3/4 rounded-full"></div>
                                    </div>
                                    <p className="text-[12px] text-on-surface-variant text-right font-medium">Target Hijau Bogor Tercapai 75%</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Workflow Section (Alur Kerja) */}
                    <section id="alur" className="py-2xl bg-white border-b border-outline-variant/20">
                        <div className="max-w-container-max mx-auto px-lg md:px-xl">
                            <div className="text-center mb-2xl space-y-sm">
                                <h2 className="text-[28px] md:text-[32px] font-bold text-primary">Alur Kerja Kami</h2>
                                <p className="text-[14px] md:text-[16px] text-on-surface-variant max-w-2xl mx-auto">Tiga langkah mudah untuk mulai berkontribusi pada lingkungan dan ekonomi Anda.</p>
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-lg">
                                {/* Step 1 */}
                                <div className="bg-background rounded-2xl p-xl shadow-sm border border-outline-variant/30 text-center group hover:ring-1 hover:ring-primary/20 transition-all duration-300">
                                    <div className="w-16 h-16 mx-auto bg-white text-primary rounded-full flex items-center justify-center mb-lg shadow-sm group-hover:scale-110 transition-transform duration-300">
                                        <span className="material-symbols-outlined text-[32px]" style={{ fontVariationSettings: "'FILL' 1" }}>app_registration</span>
                                    </div>
                                    <h3 className="text-[18px] font-bold text-on-surface mb-sm">1. Daftar Akun</h3>
                                    <p className="text-[14px] text-on-surface-variant leading-relaxed">
                                        Buat akun dengan mudah melalui platform kami untuk mulai melacak kontribusi tabungan sampah Anda.
                                    </p>
                                </div>
                                {/* Step 2 */}
                                <div className="bg-background rounded-2xl p-xl shadow-sm border border-outline-variant/30 text-center group hover:ring-1 hover:ring-primary/20 transition-all duration-300">
                                    <div className="w-16 h-16 mx-auto bg-white text-primary rounded-full flex items-center justify-center mb-lg shadow-sm group-hover:scale-110 transition-transform duration-300">
                                        <span className="material-symbols-outlined text-[32px]" style={{ fontVariationSettings: "'FILL' 1" }}>recycling</span>
                                    </div>
                                    <h3 className="text-[18px] font-bold text-on-surface mb-sm">2. Setor Sampah</h3>
                                    <p className="text-[14px] text-on-surface-variant leading-relaxed">
                                        Bawa sampah terpilah Anda ke depo penampungan. Petugas akan menimbang dan menginput nilai rupiahnya secara riil.
                                    </p>
                                </div>
                                {/* Step 3 */}
                                <div className="bg-background rounded-2xl p-xl shadow-sm border border-outline-variant/30 text-center group hover:ring-1 hover:ring-primary/20 transition-all duration-300">
                                    <div className="w-16 h-16 mx-auto bg-white text-primary rounded-full flex items-center justify-center mb-lg shadow-sm group-hover:scale-110 transition-transform duration-300">
                                        <span className="material-symbols-outlined text-[32px]" style={{ fontVariationSettings: "'FILL' 1" }}>account_balance_wallet</span>
                                    </div>
                                    <h3 className="text-[18px] font-bold text-on-surface mb-sm">3. Tarik Saldo</h3>
                                    <p className="text-[14px] text-on-surface-variant leading-relaxed">
                                        Nikmati hasil kerja keras Anda. Tarik saldo tabungan langsung ke rekening bank atau e-wallet kapan saja Anda inginkan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Operational Schedule & Price Catalog */}
                    <section id="jadwal" className="py-2xl bg-background border-b border-outline-variant/20">
                        <div className="max-w-container-max mx-auto px-lg md:px-xl">
                            <div className="flex flex-col md:flex-row items-stretch gap-2xl">
                                {/* Schedule Card */}
                                <div className="flex-1 space-y-lg flex flex-col justify-center">
                                    <h2 className="text-[28px] md:text-[32px] font-bold text-primary">Jadwal Operasional</h2>
                                    <p className="text-[14px] md:text-[16px] text-on-surface-variant leading-relaxed">
                                        Kunjungi depo kami pada jam operasional untuk menyetorkan sampah terpilah Anda. Tim petugas kami siap membantu proses penimbangan dan pencatatan saldo dengan cepat dan transparan.
                                    </p>
                                    <div className="bg-white rounded-2xl p-xl shadow-sm border border-outline-variant/30">
                                        <div className="flex items-center gap-md border-b border-outline-variant/20 pb-md mb-md">
                                            <span className="material-symbols-outlined text-primary text-[28px]" style={{ fontVariationSettings: "'FILL' 1" }}>event_available</span>
                                            <div>
                                                <h4 className="text-[16px] font-bold text-on-surface">Hari Layanan</h4>
                                                <p className="text-[14px] text-on-surface-variant font-medium">Senin - Sabtu</p>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-md">
                                            <span className="material-symbols-outlined text-primary text-[28px]" style={{ fontVariationSettings: "'FILL' 1" }}>schedule</span>
                                            <div>
                                                <h4 className="text-[16px] font-bold text-on-surface">Jam Layanan</h4>
                                                <p className="text-[14px] text-on-surface-variant font-medium">08:00 - 16:00 WIB</p>
                                            </div>
                                        </div>
                                        <div className="mt-lg pt-md border-t border-outline-variant/20 flex items-center gap-xs text-error font-semibold text-[12px]">
                                            <span className="material-symbols-outlined text-[16px]">info</span>
                                            Tutup pada hari Minggu dan Hari Libur Nasional.
                                        </div>
                                    </div>
                                </div>
                                {/* Image Card */}
                                <div className="flex-1 w-full min-h-[300px] md:min-h-full rounded-2xl overflow-hidden shadow-md relative">
                                    <img 
                                        alt="Fasilitas Depo" 
                                        className="absolute inset-0 w-full h-full object-cover" 
                                        src="/images/bsfpxwcid.png"
                                    />
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Price Catalog Section */}
                    <section id="harga" className="py-2xl bg-white border-b border-outline-variant/20">
                        <div className="max-w-container-max mx-auto px-lg md:px-xl">
                            <div className="text-center mb-xl space-y-xs">
                                <h2 className="text-[28px] md:text-[32px] font-bold text-primary">Katalog Harga Sampah</h2>
                                <p className="text-[14px] md:text-[16px] text-on-surface-variant max-w-2xl mx-auto">
                                    Daftar harga beli per unit sampah terpilah yang kami terima dari Nasabah.
                                </p>
                            </div>
                            
                            <div className="bg-white rounded-2xl border border-outline-variant/30 overflow-hidden shadow-sm max-w-3xl mx-auto p-md">
                                <Table>
                                    <TableHeader>
                                        <TableRow className="bg-background hover:bg-background">
                                            <TableHead className="font-bold text-primary uppercase text-[13px] tracking-wider px-lg py-md">Nama Sampah</TableHead>
                                            <TableHead className="font-bold text-primary uppercase text-[13px] tracking-wider px-lg py-md">Kategori</TableHead>
                                            <TableHead className="font-bold text-primary uppercase text-[13px] tracking-wider px-lg py-md text-right">Harga Beli Nasabah</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody className="text-[14px]">
                                        {prices.length > 0 ? (
                                            prices.map((p) => (
                                                <TableRow key={p.id} className="hover:bg-background/40 transition-colors">
                                                    <TableCell className="px-lg py-md font-semibold text-on-surface">{p.name}</TableCell>
                                                    <TableCell className="px-lg py-md capitalize text-on-surface-variant">{p.category.replace('_', ' ')}</TableCell>
                                                    <TableCell className="px-lg py-md text-right font-bold text-primary">
                                                        Rp {numberFormat(p.price_buy)} / {p.unit}
                                                    </TableCell>
                                                </TableRow>
                                            ))
                                        ) : (
                                            <TableRow>
                                                <TableCell colSpan="3" className="px-lg py-xl text-center text-on-surface-variant font-medium">
                                                    Katalog harga sampah belum tersedia.
                                                </TableCell>
                                            </TableRow>
                                        )}
                                    </TableBody>
                                </Table>
                            </div>
                        </div>
                    </section>

                    {/* Articles Section (Edukasi Lingkungan) */}
                    <section id="artikel" className="py-2xl bg-background">
                        <div className="max-w-container-max mx-auto px-lg md:px-xl">
                            <div className="text-center mb-xl space-y-xs">
                                <h2 className="text-[28px] md:text-[32px] font-bold text-primary">Artikel &amp; Edukasi</h2>
                                <p className="text-[14px] md:text-[16px] text-on-surface-variant max-w-2xl mx-auto">
                                    Dapatkan tips dan panduan terkini seputar cara memilah sampah dan melestarikan lingkungan.
                                </p>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-3 gap-lg max-w-6xl mx-auto">
                                {articles.length > 0 ? (
                                    articles.map((art) => (
                                        <article key={art.id} className="bg-white rounded-2xl border border-outline-variant/30 overflow-hidden shadow-sm flex flex-col hover:-translate-y-1 transition-transform duration-300">
                                            <div className="h-48 overflow-hidden bg-primary/5 relative">
                                                {art.image_url ? (
                                                    <img 
                                                        alt={art.title} 
                                                        className="w-full h-full object-cover" 
                                                        src={art.image_url} 
                                                    />
                                                ) : (
                                                    <div className="absolute inset-0 flex items-center justify-center text-primary/40">
                                                        <span className="material-symbols-outlined text-[64px]" style={{ fontVariationSettings: "'FILL' 0" }}>photo</span>
                                                    </div>
                                                )}
                                            </div>
                                            <div className="p-lg flex-grow flex flex-col justify-between gap-md">
                                                <div className="space-y-sm">
                                                    <h3 className="text-[16px] font-bold text-on-surface line-clamp-2 leading-snug hover:text-primary transition-colors">
                                                        {art.title}
                                                    </h3>
                                                    <p className="text-[13px] text-on-surface-variant line-clamp-3 leading-relaxed">
                                                        {art.content}
                                                    </p>
                                                </div>
                                                <Link
                                                    href={`/artikel/${art.slug}`}
                                                    className="text-[13px] font-bold text-primary hover:text-secondary flex items-center gap-xs mt-sm self-start transition-colors"
                                                >
                                                    Baca Selengkapnya
                                                    <span className="material-symbols-outlined text-[16px]">arrow_forward</span>
                                                </Link>
                                            </div>
                                        </article>
                                    ))
                                ) : (
                                    <div className="col-span-3 text-center py-lg text-on-surface-variant">
                                        Belum ada artikel yang dipublikasikan.
                                    </div>
                                )}
                            </div>
                        </div>
                    </section>
                </main>

                {/* Footer */}
                <footer className="bg-surface-container-highest border-t border-outline-variant/20 text-on-surface mt-auto">
                    <div className="max-w-container-max mx-auto px-lg md:px-xl py-xl flex flex-col md:flex-row justify-between items-start gap-lg">
                        <div className="space-y-xs max-w-sm">
                            <div className="text-[18px] font-bold text-primary flex items-center gap-xs mb-sm">
                                <span className="material-symbols-outlined text-[24px]" style={{ fontVariationSettings: "'FILL' 1" }}>recycling</span>
                                Bank Sampah
                            </div>
                            <p className="text-[13px] text-on-surface-variant leading-relaxed">
                                © 2026 Bank Sampah Faperta Unmul. Mengelola sampah dengan bijak, mengubah sampah menjadi tabungan kebaikan.
                            </p>
                        </div>
                        <nav className="flex flex-col md:flex-row gap-lg md:gap-2xl text-[13px] font-semibold">
                            <div className="flex flex-col gap-sm">
                                <a className="text-on-surface-variant hover:text-primary hover:underline transition-colors" href="#">Tentang Kami</a>
                                <a className="text-on-surface-variant hover:text-primary hover:underline transition-colors" href="#">Kebijakan Privasi</a>
                            </div>
                            <div className="flex flex-col gap-sm">
                                <a className="text-on-surface-variant hover:text-primary hover:underline transition-colors" href="#">Syarat &amp; Ketentuan</a>
                                <a className="text-on-surface-variant hover:text-primary hover:underline transition-colors" href="#">Hubungi Kami</a>
                            </div>
                        </nav>
                    </div>
                </footer>
            </div>

            <LoginModal 
                isOpen={isLoginOpen} 
                onClose={() => setIsLoginOpen(false)} 
                onSwitchToRegister={handleSwitchToRegister}
            />
            <RegisterModal 
                isOpen={isRegisterOpen} 
                onClose={() => setIsRegisterOpen(false)} 
                onSwitchToLogin={handleSwitchToLogin}
            />
        </>
    );
}

function numberFormat(val) {
    return new Intl.NumberFormat('id-ID').format(val);
}
