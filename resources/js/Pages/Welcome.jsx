import React, { useState, useEffect, useRef } from 'react';
import { Link, Head, usePage } from '@inertiajs/react';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import PublicLayout from '@/Layouts/PublicLayout';
import { motion, useScroll, useTransform, useInView, animate } from 'framer-motion';

export default function Welcome({ prices = [], articles = [], totalCarbonContribution = 0, totalWaste = 0 }) {
    const { auth } = usePage().props;
    const [isMobile, setIsMobile] = useState(false);

    const heroRef = useRef(null);
    const { scrollY } = useScroll();
    const backgroundY = useTransform(scrollY, [0, 500], [0, isMobile ? 0 : 100]);

    useEffect(() => {
        const handleResize = () => setIsMobile(window.innerWidth < 768);
        handleResize();
        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    const containerVariants = {
        hidden: { opacity: 0 },
        visible: {
            opacity: 1,
            transition: {
                staggerChildren: 0.12
            }
        }
    };

    const itemVariants = {
        hidden: { opacity: 0, y: 24 },
        visible: {
            opacity: 1,
            y: 0,
            transition: { duration: 0.6, ease: [0.25, 1, 0.5, 1] }
        }
    };

    const handleOpenRegisterModal = () => {
        window.dispatchEvent(new CustomEvent('open-register'));
    };

    return (
        <>
            <Head title="Bank Sampah Faperta Unmul - Kelola Sampah Jadi Berkah" />
            
            {/* Hero Section */}
            <section ref={heroRef} id="home" className="relative bg-white overflow-hidden py-2xl md:py-32 border-b border-outline-variant/20">
                <motion.div style={{ y: backgroundY }} className="absolute inset-0 z-0">
                    <img 
                        alt="Hero Background" 
                        className="w-full h-full object-cover opacity-15 object-center" 
                        src="/images/hero-bfsp.jpeg"
                    />
                    <div className="absolute inset-0 bg-gradient-to-r from-background via-background/5 to-transparent"></div>
                </motion.div>
                <div className="relative z-10 max-w-container-max mx-auto px-lg md:px-xl grid grid-cols-1 md:grid-cols-2 gap-2xl items-center">
                    <motion.div 
                        variants={containerVariants} 
                        initial="hidden" 
                        animate="visible" 
                        className="space-y-md"
                    >
                        <motion.h1 variants={itemVariants} className="text-[36px] md:text-[52px] leading-[1.05] font-extrabold text-primary tracking-tight">
                            Kelola Sampah <br/><span className="text-secondary">Jadi Berkah</span>
                        </motion.h1>
                        <motion.p variants={itemVariants} className="text-[15px] md:text-[16px] text-on-surface-variant max-w-lg leading-relaxed font-medium">
                            Ubah kebiasaan membuang menjadi menabung. Bergabunglah dengan kami untuk melestarikan lingkungan kampus sambil mengumpulkan tabungan yang bermanfaat bagi masa depan Anda.
                        </motion.p>
                        <motion.div variants={itemVariants} className="pt-xs">
                            {auth.user ? (
                                auth.user.role === 'nasabah' ? (
                                    <Link
                                        href="/nasabah/dashboard"
                                        className="inline-block bg-primary text-white font-semibold text-[14px] px-xl py-md rounded-full shadow-lg hover:bg-secondary transition-all no-underline"
                                    >
                                        Pergi ke Dashboard
                                    </Link>
                                ) : (
                                    <a
                                        href="/admin"
                                        className="inline-block bg-primary text-white font-semibold text-[14px] px-xl py-md rounded-full shadow-lg hover:bg-secondary transition-all no-underline"
                                    >
                                        Pergi ke Dashboard
                                    </a>
                                )
                            ) : (
                                <button
                                    onClick={handleOpenRegisterModal}
                                    className="inline-block bg-primary text-white font-semibold text-[14px] px-xl py-md rounded-full shadow-lg hover:bg-secondary transition-all cursor-pointer border-0 text-center"
                                >
                                    Daftar Sekarang
                                </button>
                            )}
                        </motion.div>
                    </motion.div>
                    <div className="hidden md:block relative">
                        <motion.div
                            initial={{ opacity: 0, scale: 0.9, rotate: 3, y: 0 }}
                            animate={{ 
                                opacity: 1, 
                                scale: 1, 
                                y: [0, -10, 0] 
                            }}
                            transition={{
                                opacity: { duration: 0.6 },
                                scale: { duration: 0.6 },
                                y: {
                                    repeat: Infinity,
                                    repeatType: "mirror",
                                    duration: 3.5,
                                    ease: "easeInOut"
                                }
                            }}
                            className="bg-white/90 backdrop-blur-md border border-white/50 rounded-3xl p-xl shadow-[0px_10px_30px_rgba(45,90,39,0.08)] rotate-2 hover:rotate-0 transition-all duration-500 max-w-lg mx-auto"
                        >
                            <h4 className="text-[14px] font-bold text-primary mb-md flex items-center gap-xs">
                                <span className="material-symbols-outlined text-[18px]">analytics</span>
                                Dampak Lingkungan Kita
                            </h4>
                            
                            <div className="grid grid-cols-3 gap-xs mb-lg">
                                {/* Total Sampah */}
                                <div className="space-y-xs border-r border-outline-variant/20 pr-xs">
                                    <div className="w-9 h-9 rounded-xl bg-primary-container/40 flex items-center justify-center text-primary mb-xs">
                                        <span className="material-symbols-outlined text-[18px]" style={{ fontVariationSettings: "'FILL' 1" }}>recycling</span>
                                    </div>
                                    <p className="text-[11px] text-on-surface-variant font-semibold uppercase tracking-wider">Sampah Terkelola</p>
                                    <p className="text-[16px] text-primary font-bold">
                                        <Counter value={Number(totalWaste || 0)} /> kg
                                    </p>
                                </div>
                                
                                {/* Reduksi Karbon */}
                                <div className="space-y-xs border-r border-outline-variant/20 px-xs">
                                    <div className="w-9 h-9 rounded-xl bg-secondary-container/40 flex items-center justify-center text-secondary mb-xs">
                                        <span className="material-symbols-outlined text-[18px]" style={{ fontVariationSettings: "'FILL' 1" }}>eco</span>
                                    </div>
                                    <p className="text-[11px] text-on-surface-variant font-semibold uppercase tracking-wider">Reduksi Karbon</p>
                                    <p className="text-[16px] text-secondary font-bold">
                                        <Counter value={Number(totalCarbonContribution || 0)} /> kg CO₂e
                                    </p>
                                </div>

                                {/* Setara Pohon */}
                                <div className="space-y-xs pl-xs">
                                    <div className="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center text-green-700 mb-xs">
                                        <span className="material-symbols-outlined text-[18px]" style={{ fontVariationSettings: "'FILL' 1" }}>forest</span>
                                    </div>
                                    <p className="text-[11px] text-on-surface-variant font-semibold uppercase tracking-wider">Setara Penyerapan</p>
                                    <p className="text-[16px] text-green-700 font-bold">
                                        <Counter value={Math.round(Number(totalCarbonContribution || 0) / 21)} isInteger={true} /> pohon/thn
                                    </p>
                                </div>
                            </div>
                        </motion.div>
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
                    <motion.div 
                        variants={{
                            hidden: {},
                            visible: {
                                transition: {
                                    staggerChildren: 0.15
                                }
                            }
                        }}
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-100px" }}
                        className="grid grid-cols-1 md:grid-cols-3 gap-lg"
                    >
                        {/* Step 1 */}
                        <motion.div 
                            variants={{
                                hidden: { opacity: 0, y: 30 },
                                visible: { opacity: 1, y: 0, transition: { duration: 0.6, ease: "easeOut" } }
                            }}
                            className="relative bg-background rounded-3xl p-xl shadow-sm border border-outline-variant/30 text-center group hover:ring-1 hover:ring-primary/20 transition-all duration-300 overflow-hidden"
                        >
                            <span className="absolute top-md right-lg text-[48px] font-black text-primary/5 select-none transition-colors group-hover:text-primary/10">01</span>
                            <div className="w-16 h-16 mx-auto bg-primary/5 text-primary rounded-2xl flex items-center justify-center mb-lg shadow-sm group-hover:bg-primary group-hover:text-white transition-all duration-300">
                                <span className="material-symbols-outlined text-[32px]" style={{ fontVariationSettings: "'FILL' 1" }}>app_registration</span>
                            </div>
                            <h3 className="text-[18px] font-bold text-on-surface mb-sm">Daftar Akun</h3>
                            <p className="text-[13px] text-on-surface-variant leading-relaxed">
                                Buat akun dengan mudah melalui platform kami untuk mulai melacak kontribusi tabungan sampah Anda.
                            </p>
                        </motion.div>
                        {/* Step 2 */}
                        <motion.div 
                            variants={{
                                hidden: { opacity: 0, y: 30 },
                                visible: { opacity: 1, y: 0, transition: { duration: 0.6, ease: "easeOut" } }
                            }}
                            className="relative bg-background rounded-3xl p-xl shadow-sm border border-outline-variant/30 text-center group hover:ring-1 hover:ring-primary/20 transition-all duration-300 overflow-hidden"
                        >
                            <span className="absolute top-md right-lg text-[48px] font-black text-primary/5 select-none transition-colors group-hover:text-primary/10">02</span>
                            <div className="w-16 h-16 mx-auto bg-primary/5 text-primary rounded-2xl flex items-center justify-center mb-lg shadow-sm group-hover:bg-primary group-hover:text-white transition-all duration-300">
                                <span className="material-symbols-outlined text-[32px]" style={{ fontVariationSettings: "'FILL' 1" }}>recycling</span>
                            </div>
                            <h3 className="text-[18px] font-bold text-on-surface mb-sm">Setor Sampah</h3>
                            <p className="text-[13px] text-on-surface-variant leading-relaxed">
                                Bawa sampah terpilah Anda ke depo penampungan. Petugas akan menimbang dan menginput nilai rupiahnya secara riil.
                            </p>
                        </motion.div>
                        {/* Step 3 */}
                        <motion.div 
                            variants={{
                                hidden: { opacity: 0, y: 30 },
                                visible: { opacity: 1, y: 0, transition: { duration: 0.6, ease: "easeOut" } }
                            }}
                            className="relative bg-background rounded-3xl p-xl shadow-sm border border-outline-variant/30 text-center group hover:ring-1 hover:ring-primary/20 transition-all duration-300 overflow-hidden"
                        >
                            <span className="absolute top-md right-lg text-[48px] font-black text-primary/5 select-none transition-colors group-hover:text-primary/10">03</span>
                            <div className="w-16 h-16 mx-auto bg-primary/5 text-primary rounded-2xl flex items-center justify-center mb-lg shadow-sm group-hover:bg-primary group-hover:text-white transition-all duration-300">
                                <span className="material-symbols-outlined text-[32px]" style={{ fontVariationSettings: "'FILL' 1" }}>account_balance_wallet</span>
                            </div>
                            <h3 className="text-[18px] font-bold text-on-surface mb-sm">Tarik Saldo</h3>
                            <p className="text-[13px] text-on-surface-variant leading-relaxed">
                                Nikmati hasil kerja keras Anda. Tarik saldo tabungan langsung ke rekening bank atau e-wallet kapan saja Anda inginkan.
                            </p>
                        </motion.div>
                    </motion.div>
                </div>
            </section>

            {/* Operational Schedule */}
            <section id="jadwal" className="py-2xl bg-background border-b border-outline-variant/20">
                <div className="max-w-container-max mx-auto px-lg md:px-xl">
                    <motion.div 
                        initial={{ opacity: 0, y: 40 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true, margin: "-100px" }}
                        transition={{ duration: 0.8, ease: "easeOut" }}
                        className="flex flex-col md:flex-row items-stretch gap-2xl"
                    >
                        {/* Schedule Card */}
                        <div className="flex-1 space-y-lg flex flex-col justify-center">
                            <h2 className="text-[28px] md:text-[32px] font-bold text-primary tracking-tight">Jadwal Operasional</h2>
                            <p className="text-[13px] md:text-[14px] text-on-surface-variant leading-relaxed font-medium">
                                Kunjungi depo kami pada jam operasional untuk menyetorkan sampah terpilah Anda. Tim petugas kami siap membantu proses penimbangan dan pencatatan saldo secara cepat, akurat, dan transparan.
                            </p>
                            <div className="bg-white rounded-3xl p-lg shadow-sm border border-outline-variant/30">
                                <div className="flex items-center justify-between border-b border-outline-variant/20 pb-md mb-md">
                                    <div className="flex items-center gap-md">
                                        <div className="w-10 h-10 bg-primary/10 text-primary rounded-xl flex items-center justify-center">
                                            <span className="material-symbols-outlined text-[20px]" style={{ fontVariationSettings: "'FILL' 1" }}>event_available</span>
                                        </div>
                                        <div>
                                            <h4 className="text-[14px] font-bold text-on-surface">Hari Layanan</h4>
                                            <p className="text-[13px] text-on-surface-variant font-medium">Senin - Sabtu</p>
                                        </div>
                                    </div>
                                    <span className="text-[11px] font-extrabold px-sm py-[2px] bg-primary/15 text-primary rounded-full uppercase tracking-wider">Aktif</span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-md">
                                        <div className="w-10 h-10 bg-primary/10 text-primary rounded-xl flex items-center justify-center">
                                            <span className="material-symbols-outlined text-[20px]" style={{ fontVariationSettings: "'FILL' 1" }}>schedule</span>
                                        </div>
                                        <div>
                                            <h4 className="text-[14px] font-bold text-on-surface">Jam Layanan</h4>
                                            <p className="text-[13px] text-on-surface-variant font-medium">08:00 - 16:00 WITA</p>
                                        </div>
                                    </div>
                                    <span className="text-[11px] font-extrabold px-sm py-[2px] bg-secondary/15 text-secondary rounded-full uppercase tracking-wider">Buka</span>
                                </div>
                                <div className="mt-lg pt-md border-t border-outline-variant/20 flex items-center gap-xs text-error font-bold text-[11px]">
                                    <span className="material-symbols-outlined text-[14px]">info</span>
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
                                loading="lazy"
                            />
                        </div>
                    </motion.div>
                </div>
            </section>

            {/* Price Catalog Section (Preview) */}
            <section id="harga" className="py-2xl bg-white border-b border-outline-variant/20">
                <div className="max-w-container-max mx-auto px-lg md:px-xl">
                    <div className="text-center mb-xl space-y-xs">
                        <h2 className="text-[28px] md:text-[32px] font-bold text-primary">Katalog Harga Sampah</h2>
                        <p className="text-[14px] md:text-[16px] text-on-surface-variant max-w-2xl mx-auto">
                            Daftar harga beli per unit sampah terpilah (Pratinjau). Klik tombol di bawah untuk melihat seluruh daftar harga secara rinci.
                        </p>
                    </div>
                    
                    <motion.div 
                        initial={{ opacity: 0, y: 30 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true, margin: "-100px" }}
                        transition={{ duration: 0.8, ease: "easeOut" }}
                        className="bg-white rounded-3xl border border-outline-variant/20 overflow-hidden shadow-sm max-w-3xl mx-auto p-xs"
                    >
                        <Table>
                            <TableHeader>
                                <TableRow className="bg-surface-container-low hover:bg-surface-container-low border-b border-outline-variant/20">
                                    <TableHead className="font-extrabold text-primary uppercase text-[11px] tracking-[0.12em] px-lg py-sm">Nama Sampah</TableHead>
                                    <TableHead className="font-extrabold text-primary uppercase text-[11px] tracking-[0.12em] px-lg py-sm">Kategori</TableHead>
                                    <TableHead className="font-extrabold text-primary uppercase text-[11px] tracking-[0.12em] px-lg py-sm text-right">Harga Beli Nasabah</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody className="text-[13px]">
                                {prices.length > 0 ? (
                                    prices.map((p) => (
                                        <TableRow key={p.id} className="hover:bg-background/40 transition-colors border-b border-outline-variant/10">
                                            <TableCell className="px-lg py-md font-bold text-on-surface">{p.name}</TableCell>
                                            <TableCell className="px-lg py-md capitalize">
                                                <span className="inline-block px-sm py-[2px] bg-primary/5 text-primary text-[11px] font-bold rounded">
                                                    {p.category.replace('_', ' ')}
                                                </span>
                                            </TableCell>
                                            <TableCell className="px-lg py-md text-right font-extrabold text-primary text-[14px]">
                                                Rp {numberFormat(p.price_buy)} <span className="text-[11px] text-on-surface-variant font-medium">/ {p.unit}</span>
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

                        <div className="mt-md text-center">
                            <Link
                                href="/harga"
                                className="inline-flex items-center gap-xs text-[14px] font-bold text-primary hover:text-secondary transition-colors no-underline"
                            >
                                Lihat Seluruh Katalog Harga Lengkap
                                <span className="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </Link>
                        </div>
                    </motion.div>
                </div>
            </section>

            {/* Articles Section (Preview) */}
            <section id="artikel" className="py-2xl bg-background">
                <div className="max-w-container-max mx-auto px-lg md:px-xl">
                    <div className="text-center mb-xl space-y-xs">
                        <h2 className="text-[28px] md:text-[32px] font-bold text-primary">Artikel &amp; Edukasi</h2>
                        <p className="text-[14px] md:text-[16px] text-on-surface-variant max-w-2xl mx-auto">
                            Dapatkan tips dan panduan terkini seputar cara memilah sampah dan melestarikan lingkungan.
                        </p>
                    </div>

                    <motion.div 
                        variants={{
                            hidden: {},
                            visible: {
                                transition: {
                                    staggerChildren: 0.15
                                }
                            }
                        }}
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-100px" }}
                        className="grid grid-cols-1 md:grid-cols-3 gap-lg max-w-6xl mx-auto"
                    >
                        {articles.length > 0 ? (
                            articles.map((art) => (
                                <motion.article 
                                    key={art.id} 
                                    variants={{
                                        hidden: { opacity: 0, y: 30 },
                                        visible: { opacity: 1, y: 0, transition: { duration: 0.6 } }
                                    }}
                                    whileHover={{ y: -8, scale: 1.01, boxShadow: "0 10px 25px -5px rgba(0,0,0,0.08)" }}
                                    className="bg-white rounded-3xl border border-outline-variant/30 overflow-hidden shadow-sm flex flex-col transition-all duration-300 relative"
                                >
                                    {/* Category Badge */}
                                    <div className="absolute top-md left-md bg-secondary/90 backdrop-blur-sm text-white text-[11px] font-extrabold px-sm py-[3px] rounded-md shadow-sm z-10 uppercase tracking-wider">
                                        Edukasi
                                    </div>
                                    <div className="h-48 overflow-hidden bg-primary/5 relative">
                                        {art.image_url ? (
                                            <img 
                                                alt={art.title} 
                                                className="w-full h-full object-cover" 
                                                src={art.image_url} 
                                                loading="lazy"
                                            />
                                        ) : (
                                            <div className="absolute inset-0 flex items-center justify-center text-primary/40">
                                                <span className="material-symbols-outlined text-[64px]" style={{ fontVariationSettings: "'FILL' 0" }}>photo</span>
                                            </div>
                                        )}
                                    </div>
                                    <div className="p-lg flex-grow flex flex-col justify-between gap-md">
                                        <div className="space-y-xs">
                                            <div className="flex items-center gap-xs text-[11px] text-on-surface-variant font-bold mb-xs uppercase tracking-wider">
                                                <span className="material-symbols-outlined text-[13px]">calendar_today</span>
                                                <span>{art.created_at ? new Date(art.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : 'Baru Saja'}</span>
                                                <span className="text-outline-variant">•</span>
                                                <span>3 Menit Baca</span>
                                            </div>
                                            <h3 className="text-[16px] font-bold text-on-surface line-clamp-2 leading-snug hover:text-primary transition-colors">
                                                {art.title}
                                            </h3>
                                            <p className="text-[13px] text-on-surface-variant line-clamp-3 leading-relaxed font-medium">
                                                {art.content}
                                            </p>
                                        </div>
                                        <Link
                                            href={`/artikel/${art.slug}`}
                                            className="text-[13px] font-bold text-primary hover:text-secondary flex items-center gap-xs mt-sm self-start transition-colors no-underline"
                                        >
                                            Baca Selengkapnya
                                            <span className="material-symbols-outlined text-[16px]">arrow_forward</span>
                                        </Link>
                                    </div>
                                </motion.article>
                            ))
                        ) : (
                            <div className="col-span-3 text-center py-lg text-on-surface-variant">
                                Belum ada artikel yang dipublikasikan.
                            </div>
                        )}
                    </motion.div>

                    <div className="mt-xl text-center">
                        <Link
                            href="/artikel"
                            className="inline-flex items-center gap-xs bg-primary text-white font-semibold text-[14px] px-xl py-md rounded-full shadow-md hover:bg-secondary transition-all no-underline"
                        >
                            Lihat Semua Artikel Edukasi
                            <span className="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </Link>
                    </div>
                </div>
            </section>

            {/* Kontak & Lokasi Section */}
            <section id="kontak" className="py-2xl bg-white border-b border-outline-variant/20">
                <div className="max-w-container-max mx-auto px-lg md:px-xl">
                    <div className="text-center mb-xl space-y-xs">
                        <h2 className="text-[28px] md:text-[32px] font-bold text-primary">Hubungi Kami &amp; Lokasi</h2>
                        <p className="text-[14px] md:text-[16px] text-on-surface-variant max-w-2xl mx-auto">
                            Kunjungi depo kami untuk menyetor sampah atau ikuti media sosial kami untuk informasi terbaru seputar program kelestarian lingkungan.
                        </p>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-xl items-stretch max-w-6xl mx-auto">
                        {/* Map Embed (Left Column) */}
                        <div className="lg:col-span-7 rounded-2xl overflow-hidden shadow-md border border-outline-variant/30 min-h-[350px] relative">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.6738986518175!2d117.1473950749666!3d-0.4674687995254131!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df67f33d7398189%3A0x7d2870191834e5ce!2sFakultas%20Pertanian%20Universitas%20Mulawarman!5e0!3m2!1sid!2sid!4v1719066600000!5m2!1sid!2sid" 
                                width="100%" 
                                height="100%" 
                                style={{ border: 0 }} 
                                allowFullScreen="" 
                                loading="lazy" 
                                referrerPolicy="no-referrer-when-downgrade"
                                className="absolute inset-0 w-full h-full"
                            ></iframe>
                        </div>

                        {/* Social & Contact Info Cards (Right Column) */}
                        <div className="lg:col-span-5 flex flex-col justify-between gap-md">
                            {/* Instagram Card */}
                            <motion.div 
                                whileHover={{ scale: 1.02 }}
                                className="bg-background rounded-3xl p-lg border border-outline-variant/30 flex items-start gap-md shadow-sm hover:ring-1 hover:ring-primary/10 transition-all duration-300"
                            >
                                <div className="p-sm bg-pink-50 rounded-xl text-[#e1306c] flex items-center justify-center shadow-sm">
                                    <svg className="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                    </svg>
                                </div>
                                <div className="space-y-[4px] flex-grow">
                                    <h4 className="text-[15px] font-bold text-on-surface">Instagram</h4>
                                    <p className="text-[12px] text-on-surface-variant leading-relaxed font-medium">
                                        Ikuti akun kami untuk berita, sosialisasi, dan edukasi pemilahan sampah.
                                    </p>
                                    <a 
                                        href="https://www.instagram.com/asah_fapertaunmul?igsh=MWFsbm1ndGluYTQ0eA==" 
                                        target="_blank" 
                                        rel="noopener noreferrer"
                                        className="inline-flex items-center gap-[2px] text-[12px] font-bold text-primary hover:text-secondary mt-xs no-underline"
                                    >
                                        @asah_fapertaunmul
                                        <span className="material-symbols-outlined text-[14px]">open_in_new</span>
                                    </a>
                                </div>
                            </motion.div>

                            {/* WhatsApp Card */}
                            <motion.div 
                                whileHover={{ scale: 1.02 }}
                                className="bg-background rounded-3xl p-lg border border-outline-variant/30 flex items-start gap-md shadow-sm hover:ring-1 hover:ring-primary/10 transition-all duration-300"
                            >
                                <div className="p-sm bg-green-50 rounded-xl text-[#25d366] flex items-center justify-center shadow-sm">
                                    <svg className="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.458 5.704 1.459h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                    </svg>
                                </div>
                                <div className="space-y-[4px] flex-grow">
                                    <h4 className="text-[15px] font-bold text-on-surface">WhatsApp</h4>
                                    <p className="text-[12px] text-on-surface-variant leading-relaxed font-medium">
                                        Hubungi kami via WhatsApp untuk pertanyaan seputar kerja sama dan kemitraan.
                                    </p>
                                    <a 
                                        href="https://wa.me/622252968595" 
                                        target="_blank" 
                                        rel="noopener noreferrer"
                                        className="inline-flex items-center gap-[2px] text-[12px] font-bold text-primary hover:text-secondary mt-xs no-underline"
                                    >
                                        Hubungi WhatsApp
                                        <span className="material-symbols-outlined text-[14px]">open_in_new</span>
                                    </a>
                                </div>
                            </motion.div>

                            {/* Alamat / Google Maps Card */}
                            <motion.div 
                                whileHover={{ scale: 1.02 }}
                                className="bg-background rounded-3xl p-lg border border-outline-variant/30 flex items-start gap-md shadow-sm hover:ring-1 hover:ring-primary/10 transition-all duration-300"
                            >
                                <div className="p-sm bg-green-50 rounded-xl text-primary flex items-center justify-center shadow-sm">
                                    <span className="material-symbols-outlined text-[24px]" style={{ fontVariationSettings: "'FILL' 1" }}>location_on</span>
                                </div>
                                <div className="space-y-[4px] flex-grow">
                                    <h4 className="text-[15px] font-bold text-on-surface">Alamat &amp; Depo</h4>
                                    <p className="text-[12px] text-on-surface-variant leading-relaxed font-medium">
                                        Kantor Jurusan Agroekoteknologi, Fakultas Pertanian Universitas Mulawarman
                                    </p>
                                    <a 
                                        href="https://maps.app.goo.gl/ACJTNrZJUyUqfh4c8?g_st==ic" 
                                        target="_blank" 
                                        rel="noopener noreferrer"
                                        className="inline-flex items-center gap-[2px] text-[12px] font-bold text-primary hover:text-secondary mt-xs no-underline"
                                    >
                                        Petunjuk Arah (Google Maps)
                                        <span className="material-symbols-outlined text-[14px]">open_in_new</span>
                                    </a>
                                </div>
                            </motion.div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Mitra Kami Section (Infinity Brand Scroll) */}
            <section className="bg-background py-lg border-t border-b border-outline-variant/10 overflow-hidden">
                <div className="max-w-container-max mx-auto px-lg md:px-xl">
                    <p className="text-[11px] font-extrabold text-center text-outline uppercase tracking-[0.18em] mb-md">
                        Mitra Kerja Sama
                    </p>
                    <div className="w-full inline-flex flex-nowrap overflow-hidden [mask-image:_linear-gradient(to_right,_transparent_0,_black_128px,_black_calc(100%-128px),_transparent_100%)] [-webkit-mask-image:_linear-gradient(to_right,_transparent_0,_black_128px,_black_calc(100%-128px),_transparent_100%)]">
                        <ul className="flex items-center justify-center md:justify-start sm:[&_li]:mx-12 [&_li]:mx-8 [&_img]:max-w-none animate-infinite-scroll py-sm">
                            {[...Array(4)].map((_, i) => (
                                <React.Fragment key={i}>
                                    <li>
                                        <img src="/images/logo/logo-nutrifood.png" alt="Nutrifood" loading="lazy" className="h-14 md:h-18 w-auto object-contain opacity-70 hover:opacity-100 transition-opacity duration-300 filter grayscale hover:grayscale-0" />
                                    </li>
                                    <li>
                                        <img src="/images/logo/logo-pegadaian.png" alt="Pegadaian" loading="lazy" className="h-14 md:h-18 w-auto object-contain opacity-70 hover:opacity-100 transition-opacity duration-300 filter grayscale hover:grayscale-0" />
                                    </li>
                                    <li>
                                        <img src="/images/logo/logo-selaluteh.png" alt="Selalu Teh" loading="lazy" className="h-14 md:h-18 w-auto object-contain opacity-70 hover:opacity-100 transition-opacity duration-300 filter grayscale hover:grayscale-0" />
                                    </li>
                                </React.Fragment>
                            ))}
                        </ul>
                        <ul className="flex items-center justify-center md:justify-start sm:[&_li]:mx-12 [&_li]:mx-8 [&_img]:max-w-none animate-infinite-scroll py-sm" aria-hidden="true">
                            {[...Array(4)].map((_, i) => (
                                <React.Fragment key={i}>
                                    <li>
                                        <img src="/images/logo/logo-nutrifood.png" alt="Nutrifood" loading="lazy" className="h-14 md:h-18 w-auto object-contain opacity-70 hover:opacity-100 transition-opacity duration-300 filter grayscale hover:grayscale-0" />
                                    </li>
                                    <li>
                                        <img src="/images/logo/logo-pegadaian.png" alt="Pegadaian" loading="lazy" className="h-14 md:h-18 w-auto object-contain opacity-70 hover:opacity-100 transition-opacity duration-300 filter grayscale hover:grayscale-0" />
                                    </li>
                                    <li>
                                        <img src="/images/logo/logo-selaluteh.png" alt="Selalu Teh" loading="lazy" className="h-14 md:h-18 w-auto object-contain opacity-70 hover:opacity-100 transition-opacity duration-300 filter grayscale hover:grayscale-0" />
                                    </li>
                                </React.Fragment>
                            ))}
                        </ul>
                    </div>
                </div>
            </section>
        </>
    );
}

Welcome.layout = (page) => <PublicLayout children={page} />;

function numberFormat(val) {
    return new Intl.NumberFormat('id-ID').format(val);
}

function Counter({ value, isInteger = false }) {
    const ref = useRef(null);
    const inView = useInView(ref, { once: true });

    useEffect(() => {
        if (inView) {
            const controls = animate(0, value, {
                duration: 1.5,
                ease: "easeOut",
                onUpdate: (latest) => {
                    if (ref.current) {
                        ref.current.textContent = isInteger ? Math.round(latest).toString() : latest.toFixed(1);
                    }
                }
            });
            return () => controls.stop();
        }
    }, [value, inView, isInteger]);

    return <span ref={ref}>0.0</span>;
}
